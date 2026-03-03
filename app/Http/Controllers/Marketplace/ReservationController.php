<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Reservation;
use App\Services\PricingService;
use App\Services\Stays\MockStaysAdapter;
use App\Services\Stays\StaysPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function locateForm()
    {
        return view('marketplace.locate-reservation');
    }

    public function showByCode(string $codigo)
    {
        $reservation = Reservation::byCodigo($codigo)->first();
        if (! $reservation) {
            return redirect()->route('marketplace.locate-reservation')
                ->with('error', 'Reserva não encontrada. Verifique o código.');
        }
        $reservation->load('property.photos');
        return view('marketplace.reservation-show', compact('reservation'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'checkin' => 'required|date',
            'checkout' => 'required|date|after:checkin',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'nullable|string|max:50',
            'guests_count' => 'integer|min:1',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $property->load('owner');
        $checkin = Carbon::parse($validated['checkin']);
        $checkout = Carbon::parse($validated['checkout']);
        $nights = $checkin->diffInDays($checkout);
        $guests = max(1, (int) ($validated['guests_count'] ?? 1));

        $staysPricing = new StaysPricingService();
        $result = $staysPricing->calculatePeriodPriceForProperty($property, $checkin, $checkout, $guests);

        if ($result === null) {
            $pricing = new PricingService();
            $result = $pricing->calculatePeriodPrice($property, $checkin, $checkout);
        }

        $reservation = Reservation::create([
            'owner_id' => $property->owner_id,
            'property_id' => $property->id,
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'] ?? null,
            'guests_count' => $validated['guests_count'] ?? 1,
            'checkin_date' => $checkin,
            'checkout_date' => $checkout,
            'nights' => $nights,
            'base_total' => $result['base_total'],
            'markup_total' => $result['markup_total'] ?? 0,
            'final_total' => $result['grand_total'],
            'cleaning_fee' => $result['cleaning_fee'] ?? 0,
            'currency' => 'BRL',
            'status' => 'lead',
            'origem' => 'marketplace',
        ]);

        // Tentar criar na Stays (mock por padrão)
        try {
            $adapter = new MockStaysAdapter();
            $payload = [
                'property_id' => $property->stays_property_id,
                'checkin' => $checkin->format('Y-m-d'),
                'checkout' => $checkout->format('Y-m-d'),
                'guests' => $validated['guests_count'] ?? 1,
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
            ];
            $response = $adapter->createReservation($payload);
            if (!empty($response['success']) && !empty($response['reservation_id'])) {
                $reservation->update([
                    'stays_reservation_id' => $response['reservation_id'],
                    'status' => 'pending',
                ]);
            }
        } catch (\Throwable $e) {
            $reservation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return redirect()->route('marketplace.reservation.show', $reservation)
            ->with('success', 'Solicitação de reserva enviada.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('property.photos');
        return view('marketplace.reservation-show', compact('reservation'));
    }
}
