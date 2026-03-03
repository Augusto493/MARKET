<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\PricingService;
use App\Services\Stays\StaysPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    protected function defaultDateRange(): array
    {
        $from = now();
        $to = now()->addDays(30);
        return [$from, $to];
    }

    public function index(Request $request)
    {
        $query = Property::published()->with(['photos', 'owner']);
        $query = $this->applyFilters($query, $request);

        if ($request->filled('checkin') && $request->filled('checkout')) {
            $checkin = Carbon::parse($request->checkin);
            $checkout = Carbon::parse($request->checkout);
            $query->whereDoesntHave('calendarCache', function ($q) use ($checkin, $checkout) {
                $q->whereBetween('data', [$checkin, $checkout])->where('status', 'booked');
            });
        }

        $dateFrom = $request->filled('checkin') ? Carbon::parse($request->checkin) : now();
        $dateTo = $request->filled('checkout') ? Carbon::parse($request->checkout) : now()->addDays(30);
        if ($dateFrom->gt($dateTo)) {
            $dateTo = $dateFrom->copy()->addDays(30);
        }

        $query = $this->applyOrder($query, $request, [$dateFrom, $dateTo]);
        $properties = $query->paginate(12);

        $hospedes = (int) $request->get('hospedes', 2);
        $avgPrices = $properties->isEmpty() ? [] : $this->resolveAvgPrices($properties, $dateFrom, $dateTo, $hospedes);

        return view('marketplace.index', compact('properties', 'avgPrices'));
    }

    public function search(Request $request)
    {
        $query = Property::published()->with(['photos', 'owner']);
        $query = $this->applyFilters($query, $request);

        if ($request->filled('checkin') && $request->filled('checkout')) {
            $checkin = Carbon::parse($request->checkin);
            $checkout = Carbon::parse($request->checkout);
            $query->whereDoesntHave('calendarCache', function ($q) use ($checkin, $checkout) {
                $q->whereBetween('data', [$checkin, $checkout])->where('status', 'booked');
            });
        }

        $dateFrom = $request->filled('checkin') ? Carbon::parse($request->checkin) : now();
        $dateTo = $request->filled('checkout') ? Carbon::parse($request->checkout) : now()->addDays(30);
        if ($dateFrom->gt($dateTo)) {
            $dateTo = $dateFrom->copy()->addDays(30);
        }

        $query = $this->applyOrder($query, $request, [$dateFrom, $dateTo]);
        $properties = $query->paginate(12);

        $avgPrices = $this->resolveAvgPrices($properties, $dateFrom, $dateTo, (int) $request->get('hospedes', 2));

        return view('marketplace.search', [
            'properties' => $properties,
            'avgPrices' => $avgPrices,
            'checkin' => $request->get('checkin'),
            'checkout' => $request->get('checkout'),
            'cidade' => $request->get('cidade', 'Balneário Camboriú'),
            'hospedes' => $request->get('hospedes', 2),
            'preco_min' => $request->get('preco_min'),
            'preco_max' => $request->get('preco_max'),
            'ordem' => $request->get('ordem'),
        ]);
    }

    public function show(Property $property)
    {
        if (!$property->publicado_marketplace || !$property->ativo) {
            abort(404);
        }
        $property->load(['photos', 'amenities', 'owner']);
        return view('marketplace.property', compact('property'));
    }

    public function calculatePrice(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $property->load('owner');
        $checkin = Carbon::parse($validated['checkin']);
        $checkout = Carbon::parse($validated['checkout']);
        $guests = (int) $request->input('guests_count', 2);
        $guests = max(1, min($guests, $property->capacidade_hospedes ?: 99));

        $staysPricing = new StaysPricingService();
        $result = $staysPricing->calculatePeriodPriceForProperty($property, $checkin, $checkout, $guests);

        if ($result === null) {
            $pricing = new PricingService();
            $result = $pricing->calculatePeriodPrice($property, $checkin, $checkout);
        }

        return response()->json($result);
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', '%' . $request->cidade . '%');
        }
        if ($request->filled('quartos')) {
            $query->where('quartos', '>=', $request->quartos);
        }
        if ($request->filled('hospedes')) {
            $query->where('capacidade_hospedes', '>=', $request->hospedes);
        }
        if ($request->filled('preco_min')) {
            $query->whereHas('rateCache', fn ($q) => $q->where('preco_base', '>=', $request->preco_min));
        }
        if ($request->filled('preco_max')) {
            $query->whereHas('rateCache', fn ($q) => $q->where('preco_base', '<=', $request->preco_max));
        }
        return $query;
    }

    protected function applyOrder($query, Request $request, array $dateRange): \Illuminate\Database\Eloquent\Builder
    {
        [$dateFrom, $dateTo] = $dateRange;
        $ordem = $request->get('ordem');
        $from = $dateFrom->format('Y-m-d');
        $to = $dateTo->format('Y-m-d');

        if ($ordem === 'preco_asc') {
            $query->orderByRaw(
                '(SELECT AVG(r.preco_base) FROM property_rate_cache r WHERE r.property_id = properties.id AND r.data BETWEEN ? AND ?) ASC',
                [$from, $to]
            )->orderBy('properties.id');
            return $query;
        }
        if ($ordem === 'preco_desc') {
            $query->orderByRaw(
                '(SELECT AVG(r.preco_base) FROM property_rate_cache r WHERE r.property_id = properties.id AND r.data BETWEEN ? AND ?) DESC',
                [$from, $to]
            )->orderBy('properties.id');
            return $query;
        }

        $query->orderBy('prioridade', 'desc')->orderBy('destaque', 'desc')->orderBy('id');
        return $query;
    }

    /**
     * Resolve preços médios por noite: usa Stays API quando há datas e propriedades com stays_property_id,
     * senão usa cache local (PricingService).
     */
    protected function resolveAvgPrices($properties, Carbon $dateFrom, Carbon $dateTo, int $guests): array
    {
        if ($properties->isEmpty()) {
            return [];
        }
        $collection = $properties instanceof \Illuminate\Pagination\AbstractPaginator
            ? $properties->getCollection()
            : $properties;
        $staysPricing = new StaysPricingService();
        return $staysPricing->getAverageDailyRatesForProperties($collection, $dateFrom, $dateTo, $guests);
    }
}
