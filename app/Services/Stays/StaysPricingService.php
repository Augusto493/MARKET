<?php

namespace App\Services\Stays;

use App\Models\Owner;
use App\Models\Property;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StaysPricingService
{
    /**
     * Retorna mapa property_id => preço médio por noite para as propriedades e período dados.
     * Usa a API Stays calculate-price quando a propriedade tem stays_property_id e o owner tem credenciais.
     * Fallback para PricingService (PropertyRateCache) quando API falhar ou property sem Stays.
     */
    public function getAverageDailyRatesForProperties(
        Collection $properties,
        Carbon $dateFrom,
        Carbon $dateTo,
        int $guests = 1
    ): array {
        if ($properties->isEmpty()) {
            return [];
        }

        $from = $dateFrom->format('Y-m-d');
        $to = $dateTo->format('Y-m-d');
        $nights = max(1, $dateFrom->diffInDays($dateTo));

        $staysProperties = $properties->filter(fn (Property $p) => ! empty($p->stays_property_id));
        $fallbackPricing = new PricingService();
        $fallbackPrices = $fallbackPricing->getAverageDailyRatesForProperties(
            $properties->pluck('id')->all(),
            $dateFrom,
            $dateTo
        );

        $result = $fallbackPrices;

        foreach ($staysProperties->groupBy('owner_id') as $ownerId => $ownerProperties) {
            $owner = Owner::find($ownerId);
            if (! $owner) {
                continue;
            }

            try {
                $adapter = StaysAdapterFactory::forOwner($owner);
                $listingIds = $ownerProperties->pluck('stays_property_id')->unique()->values()->all();
                $prices = $adapter->calculatePrice($listingIds, $from, $to, $guests);

                $staysIdToPropertyId = $ownerProperties->keyBy('stays_property_id')->map->id->all();

                foreach ($prices as $item) {
                    $listingId = $item['_idlisting'] ?? $item['listingId'] ?? null;
                    if ($listingId === null) {
                        continue;
                    }
                    $propertyId = $staysIdToPropertyId[$listingId] ?? null;
                    if ($propertyId === null) {
                        continue;
                    }
                    $mctotal = $item['_mctotal'] ?? [];
                    $total = is_array($mctotal) ? (float) ($mctotal['BRL'] ?? $mctotal['USD'] ?? reset($mctotal) ?? 0) : (float) $mctotal;
                    if ($total > 0 && $nights > 0) {
                        $result[$propertyId] = round($total / $nights, 2);
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $result;
    }

    /**
     * Calcula o preço total para um imóvel em um período (tempo real via API Stays).
     * Retorna o mesmo formato de PricingService::calculatePeriodPrice quando a propriedade tem Stays;
     * retorna null quando não usar API (sem stays_property_id ou falha).
     */
    public function calculatePeriodPriceForProperty(Property $property, Carbon $checkin, Carbon $checkout, int $guests = 1): ?array
    {
        if (empty($property->stays_property_id)) {
            return null;
        }

        $owner = $property->owner;
        if (! $owner || empty($owner->stays_client_id)) {
            return null;
        }

        $from = $checkin->format('Y-m-d');
        $to = $checkout->format('Y-m-d');
        $nights = max(1, $checkin->diffInDays($checkout));

        try {
            $adapter = StaysAdapterFactory::forOwner($owner);
            $prices = $adapter->calculatePrice([$property->stays_property_id], $from, $to, $guests);
        } catch (\Throwable $e) {
            return null;
        }

        $item = $prices[0] ?? null;
        if (! $item) {
            return null;
        }

        $mctotal = $item['_mctotal'] ?? [];
        $total = is_array($mctotal) ? (float) ($mctotal['BRL'] ?? $mctotal['USD'] ?? reset($mctotal) ?? 0) : (float) $mctotal;
        if ($total <= 0) {
            return null;
        }

        return [
            'nights' => $nights,
            'base_total' => $total,
            'markup_total' => 0,
            'final_total' => $total,
            'cleaning_fee' => 0,
            'grand_total' => $total,
            'daily_prices' => [],
            'currency' => 'BRL',
        ];
    }
}
