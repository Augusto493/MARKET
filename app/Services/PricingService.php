<?php

namespace App\Services;

use App\Models\MarkupRule;
use App\Models\Property;
use App\Models\PropertyRateCache;
use Carbon\Carbon;

class PricingService
{
    /**
     * Retorna o preço médio por diária de um imóvel em um período (cache Stays).
     * Se from/to forem null, usa período padrão (hoje até +30 dias).
     */
    public function getAverageDailyRate(Property $property, ?Carbon $from = null, ?Carbon $to = null): ?float
    {
        $from = $from ?? now();
        $to = $to ?? now()->addDays(30);
        $avg = PropertyRateCache::where('property_id', $property->id)
            ->whereBetween('data', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->avg('preco_base');
        return $avg !== null ? (float) $avg : null;
    }

    /**
     * Retorna mapa property_id => preço médio por diária para vários imóveis (uma query).
     */
    public function getAverageDailyRatesForProperties(array $propertyIds, Carbon $from, Carbon $to): array
    {
        if (empty($propertyIds)) {
            return [];
        }
        $rows = PropertyRateCache::whereIn('property_id', $propertyIds)
            ->whereBetween('data', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->selectRaw('property_id, AVG(preco_base) as avg_price')
            ->groupBy('property_id')
            ->pluck('avg_price', 'property_id');
        return $rows->map(fn ($v) => (float) $v)->all();
    }
    /**
     * Calcula o preço final aplicando markup
     */
    public function calculateFinalPrice(Property $property, Carbon $checkin, Carbon $checkout, float $basePrice): array
    {
        $nights = $checkin->diffInDays($checkout);
        $markupTotal = 0;
        $markupRules = [];

        // Buscar regras aplicáveis (ordem de prioridade: property > owner > global)
        $rules = MarkupRule::active()
            ->where(function ($query) use ($property) {
                $query->where('tipo', 'global')
                    ->orWhere(function ($q) use ($property) {
                        $q->where('tipo', 'owner')->where('owner_id', $property->owner_id);
                    })
                    ->orWhere(function ($q) use ($property) {
                        $q->where('tipo', 'property')->where('property_id', $property->id);
                    });
            })
            ->orderBy('prioridade', 'desc')
            ->orderByRaw("CASE tipo WHEN 'property' THEN 1 WHEN 'owner' THEN 2 WHEN 'global' THEN 3 END")
            ->get();

        foreach ($rules as $rule) {
            if ($this->ruleApplies($rule, $checkin, $checkout, $nights)) {
                $markup = $this->applyMarkup($basePrice, $rule);
                $markupTotal += $markup;
                $markupRules[] = [
                    'rule' => $rule->nome,
                    'type' => $rule->markup_type,
                    'value' => $rule->markup_value,
                    'markup' => $markup,
                ];
            }
        }

        $finalPrice = $basePrice + $markupTotal;

        return [
            'base_price' => $basePrice,
            'markup_total' => $markupTotal,
            'final_price' => $finalPrice,
            'nights' => $nights,
            'rules_applied' => $markupRules,
        ];
    }

    /**
     * Verifica se uma regra se aplica às datas e noites
     */
    protected function ruleApplies(MarkupRule $rule, Carbon $checkin, Carbon $checkout, int $nights): bool
    {
        // Verificar período
        if ($rule->data_inicio && $checkin->lt($rule->data_inicio)) {
            return false;
        }
        if ($rule->data_fim && $checkout->gt($rule->data_fim)) {
            return false;
        }

        // Verificar dias da semana
        if ($rule->dias_semana) {
            $checkinDay = $checkin->dayOfWeek;
            if (!in_array($checkinDay, $rule->dias_semana)) {
                return false;
            }
        }

        // Verificar mínimo de noites
        if ($rule->min_noites && $nights < $rule->min_noites) {
            return false;
        }

        // Verificar máximo de noites
        if ($rule->max_noites && $nights > $rule->max_noites) {
            return false;
        }

        return true;
    }

    /**
     * Aplica markup de uma regra
     */
    protected function applyMarkup(float $basePrice, MarkupRule $rule): float
    {
        if ($rule->markup_type === 'percent') {
            return ($basePrice * $rule->markup_value) / 100;
        }

        return $rule->markup_value;
    }

    /**
     * Calcula preço total para um período
     */
    public function calculatePeriodPrice(Property $property, Carbon $checkin, Carbon $checkout): array
    {
        $nights = $checkin->diffInDays($checkout);
        $totalBase = 0;
        $totalMarkup = 0;
        $dailyPrices = [];

        $current = $checkin->copy();
        while ($current->lt($checkout)) {
            // Buscar preço do cache
            $rateCache = $property->rateCache()
                ->where('data', $current->format('Y-m-d'))
                ->first();

            $basePrice = $rateCache ? (float) $rateCache->preco_base : 0;

            if ($basePrice > 0) {
                $dayPricing = $this->calculateFinalPrice($property, $current, $current->copy()->addDay(), $basePrice);
                $totalBase += $basePrice;
                $totalMarkup += $dayPricing['markup_total'];
                
                $dailyPrices[] = [
                    'date' => $current->format('Y-m-d'),
                    'base' => $basePrice,
                    'markup' => $dayPricing['markup_total'],
                    'final' => $basePrice + $dayPricing['markup_total'],
                ];
            }

            $current->addDay();
        }

        // Taxa de limpeza (pegar do primeiro dia ou configurar)
        $cleaningFee = $property->rateCache()
            ->where('data', $checkin->format('Y-m-d'))
            ->first()?->taxa_limpeza ?? 0;

        return [
            'nights' => $nights,
            'base_total' => $totalBase,
            'markup_total' => $totalMarkup,
            'final_total' => $totalBase + $totalMarkup,
            'cleaning_fee' => $cleaningFee,
            'grand_total' => $totalBase + $totalMarkup + $cleaningFee,
            'daily_prices' => $dailyPrices,
            'currency' => 'BRL',
        ];
    }
}
