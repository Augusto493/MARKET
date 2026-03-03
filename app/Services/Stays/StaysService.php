<?php

namespace App\Services\Stays;

use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\PropertyAmenity;
use App\Models\PropertyCalendarCache;
use App\Models\PropertyRateCache;
use App\Models\PropertyRule;
use App\Models\PropertyLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StaysService
{
    protected StaysAdapterInterface $adapter;

    public function __construct(StaysAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Extrai texto de campo multilíngue da Stays (_mstitle, _msdesc: { pt_BR, en_US, ... })
     */
    protected function msValue(array $data, string $key, ?string $preferLocale = 'pt_BR'): ?string
    {
        $obj = $data[$key] ?? null;
        if (! is_array($obj)) {
            return null;
        }
        if ($preferLocale && ! empty($obj[$preferLocale])) {
            return (string) $obj[$preferLocale];
        }
        return $obj ? (string) reset($obj) : null;
    }

    /**
     * Normaliza dados de propriedade da Stays para nosso formato.
     * API Stays usa: id, _id, internalName, _mstitle, _msdesc, address, latLng, _i_maxGuests, _i_rooms, _i_beds, _f_bathrooms, status.
     */
    public function normalizeProperty(array $staysData, Owner $owner): array
    {
        $nome = $this->msValue($staysData, '_mstitle', 'pt_BR')
            ?? $staysData['internalName'] ?? $staysData['name'] ?? $staysData['title'] ?? $staysData['listingName'] ?? $staysData['listing_name'] ?? null;
        $descricao = $this->msValue($staysData, '_msdesc', 'pt_BR')
            ?? $staysData['description'] ?? null;
        $address = $staysData['address'] ?? [];
        $latLng = $staysData['latLng'] ?? [];
        // Fallbacks para capacidade (API Content pode usar nomes diferentes ou estrutura aninhada)
        $capacidade = (int) ($staysData['_i_maxGuests'] ?? $staysData['maxGuests'] ?? $staysData['max_guests'] ?? $staysData['guests'] ?? 1);
        $quartos = (int) ($staysData['_i_rooms'] ?? $staysData['rooms'] ?? $staysData['bedrooms'] ?? $staysData['bedroomCount'] ?? 0);
        $camas = (int) ($staysData['_i_beds'] ?? $staysData['beds'] ?? $staysData['bedCount'] ?? 0);
        $banheiros = (int) ($staysData['_f_bathrooms'] ?? $staysData['bathrooms'] ?? $staysData['bathroomCount'] ?? 0);
        if ($capacidade < 1) {
            $capacidade = 1;
        }

        return [
            'owner_id' => $owner->id,
            'stays_property_id' => $staysData['id'] ?? $staysData['_id'] ?? null,
            'stays_unit_id' => $staysData['unit_id'] ?? null,
            'nome' => $nome ?? 'Sem nome',
            'descricao' => $descricao,
            'descricao_curta' => $descricao ? strip_tags(mb_substr($descricao, 0, 300)) : ($staysData['short_description'] ?? null),
            'capacidade_hospedes' => $capacidade,
            'quartos' => $quartos,
            'camas' => $camas,
            'banheiros' => $banheiros,
            'cidade' => $address['city'] ?? $staysData['city'] ?? 'Balneário Camboriú',
            'bairro' => $address['region'] ?? $address['neighborhood'] ?? $staysData['neighborhood'] ?? null,
            'latitude' => isset($latLng['_f_lat']) ? (float) $latLng['_f_lat'] : ($staysData['latitude'] ?? null),
            'longitude' => isset($latLng['_f_lng']) ? (float) $latLng['_f_lng'] : ($staysData['longitude'] ?? null),
            'ativo' => ($staysData['status'] ?? 'inactive') === 'active',
            'stays_raw_data' => $staysData,
        ];
    }

    /**
     * Constrói mapa _id -> nome a partir do searchfilter (amenities + inventory com _mstitle).
     */
    public static function buildAmenitiesCatalog(array $searchFilterData): array
    {
        $catalog = [];
        $items = array_merge(
            $searchFilterData['amenities'] ?? [],
            $searchFilterData['inventory'] ?? []
        );
        foreach ($items as $item) {
            if (! is_array($item) || empty($item['_id'])) {
                continue;
            }
            $title = $item['_mstitle']['pt_BR'] ?? $item['_mstitle']['en_US'] ?? null;
            if ($title === null && ! empty($item['_mstitle'])) {
                $title = (string) reset($item['_mstitle']);
            }
            if ($title !== null && $title !== '') {
                $catalog[(string) $item['_id']] = $title;
            }
        }
        return $catalog;
    }

    /**
     * Sincroniza uma propriedade completa
     * @param  array  $amenitiesCatalog  Mapa _id -> nome (de getSearchFilter + buildAmenitiesCatalog)
     */
    public function syncProperty(string $staysPropertyId, Owner $owner, array $amenitiesCatalog = []): ?Property
    {
        try {
            $details = $this->adapter->getPropertyDetails($staysPropertyId);
            if (empty($details)) {
                return null;
            }

            $normalized = $this->normalizeProperty($details, $owner);
            
            $property = Property::updateOrCreate(
                ['stays_property_id' => $staysPropertyId, 'owner_id' => $owner->id],
                array_merge($normalized, ['stays_synced_at' => now()])
            );

            // Sincronizar fotos
            $this->syncPropertyPhotos($property);

            // Sincronizar amenities (se vierem na resposta), com catálogo para nomes legíveis
            if (isset($details['amenities'])) {
                $this->syncPropertyAmenities($property, $details['amenities'], $amenitiesCatalog);
            }

            // Sincronizar regras (se vierem na resposta)
            if (isset($details['rules'])) {
                $this->syncPropertyRules($property, $details['rules']);
            }

            // Sincronizar localização (API Stays: address + latLng, ou location)
            $locationData = $details['location'] ?? null;
            if (! $locationData && (isset($details['address']) || isset($details['latLng']))) {
                $addr = $details['address'] ?? [];
                $latLng = $details['latLng'] ?? [];
                $locationData = [
                    'city' => $addr['city'] ?? null,
                    'state' => $addr['state'] ?? $addr['stateCode'] ?? null,
                    'zip' => $addr['zip'] ?? null,
                    'neighborhood' => $addr['region'] ?? $addr['neighborhood'] ?? null,
                    'full_address' => trim(implode(', ', array_filter([
                        $addr['street'] ?? null,
                        $addr['streetNumber'] ?? null,
                        $addr['additional'] ?? null,
                        $addr['region'] ?? null,
                        $addr['city'] ?? null,
                        $addr['stateCode'] ?? null,
                        $addr['zip'] ?? null,
                    ]))) ?: null,
                    'latitude' => $latLng['_f_lat'] ?? null,
                    'longitude' => $latLng['_f_lng'] ?? null,
                ];
            }
            if ($locationData) {
                $this->syncPropertyLocation($property, $locationData);
            }

            return $property;
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar propriedade', [
                'property_id' => $staysPropertyId,
                'owner_id' => $owner->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function syncPropertyPhotos(Property $property): void
    {
        try {
            $photos = $this->adapter->getPropertyPhotos($property->stays_property_id);
            foreach ($photos as $index => $photoData) {
                if (! is_array($photoData)) {
                    continue;
                }
                $url = $photoData['url'] ?? $photoData['meta']['url'] ?? $photoData['_t_meta']['url'] ?? (is_string($photoData['_id'] ?? null) ? null : '');
                if ($url === null && ! empty($photoData['_id'])) {
                    $url = 'https://play.stays.net/image/d235/' . $photoData['_id'];
                }
                $url = $url ?? '';
                $staysId = $photoData['id'] ?? $photoData['_id'] ?? null;
                PropertyPhoto::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'stays_photo_id' => $staysId,
                    ],
                    [
                        'url' => $url,
                        'thumbnail_url' => $photoData['thumbnail'] ?? $photoData['thumbnailUrl'] ?? null,
                        'hash' => md5($url),
                        'ordem' => $photoData['order'] ?? $photoData['ordem'] ?? $index,
                        'principal' => $photoData['main'] ?? $photoData['principal'] ?? ($index === 0),
                        'legenda' => $photoData['caption'] ?? $photoData['legenda'] ?? null,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar fotos', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function syncPropertyAmenities(Property $property, array $amenities, array $amenitiesCatalog = []): void
    {
        foreach ($amenities as $amenityData) {
            if (! is_array($amenityData)) {
                $amenityData = ['name' => $amenityData, 'icon' => null, 'category' => null];
            }
            $id = $amenityData['_id'] ?? $amenityData['id'] ?? null;
            $nome = $amenitiesCatalog[$id ?? ''] ?? $amenityData['name'] ?? $amenityData['label'] ?? $this->msValue($amenityData, '_mstitle', 'pt_BR');
            if ($nome === null || $nome === '') {
                $nome = is_string($id) ? ('Comodidade ' . substr($id, -6)) : 'Comodidade';
            }
            $nome = is_string($nome) ? $nome : (string) $nome;
            $iconeVal = $amenityData['icon'] ?? $amenityData['icone'] ?? null;
            $catVal = $amenityData['category'] ?? $amenityData['categoria'] ?? null;
            $icone = $iconeVal === null ? null : (is_string($iconeVal) ? $iconeVal : (is_array($iconeVal) ? json_encode($iconeVal) : (string) $iconeVal));
            $categoria = $catVal === null ? null : (is_string($catVal) ? $catVal : (is_array($catVal) ? json_encode($catVal) : (string) $catVal));
            PropertyAmenity::updateOrCreate(
                [
                    'property_id' => $property->id,
                    'nome' => $nome,
                ],
                [
                    'icone' => $icone,
                    'categoria' => $categoria,
                ]
            );
        }
    }

    protected function syncPropertyRules(Property $property, array $rules): void
    {
        foreach ($rules as $ruleData) {
            PropertyRule::updateOrCreate(
                [
                    'property_id' => $property->id,
                    'tipo' => $ruleData['type'] ?? 'other',
                ],
                [
                    'valor' => $ruleData['value'] ?? null,
                    'descricao' => $ruleData['description'] ?? null,
                ]
            );
        }
    }

    protected function syncPropertyLocation(Property $property, array $locationData): void
    {
        PropertyLocation::updateOrCreate(
            ['property_id' => $property->id],
            [
                'endereco_completo' => $locationData['full_address'] ?? null,
                'cidade' => $locationData['city'] ?? 'Balneário Camboriú',
                'estado' => $locationData['state'] ?? null,
                'cep' => $locationData['zip'] ?? null,
                'bairro' => $locationData['neighborhood'] ?? null,
                'latitude' => $locationData['latitude'] ?? null,
                'longitude' => $locationData['longitude'] ?? null,
                'referencia' => $locationData['reference'] ?? null,
            ]
        );
    }

    /**
     * Atualiza cache de disponibilidade.
     * API Stays calendar retorna: [{ date, avail (0/1), closedToArrival, closedToDeparture, prices: [{ minStay, _mcval }] }]
     */
    public function syncAvailability(Property $property, int $days = 180): void
    {
        $dateFrom = now()->format('Y-m-d');
        $dateTo = now()->addDays($days)->format('Y-m-d');

        try {
            $calendar = $this->adapter->getAvailability($property->stays_property_id, $dateFrom, $dateTo);
            if (! is_array($calendar)) {
                return;
            }
            foreach ($calendar as $day) {
                $date = $day['date'] ?? null;
                if (! $date) {
                    continue;
                }
                $avail = $day['avail'] ?? null;
                $status = $this->mapAvailabilityStatus(
                    isset($avail) ? ($avail > 0 ? 'available' : 'unavailable') : ($day['status'] ?? 'available')
                );
                $firstPrice = isset($day['prices'][0]) ? $day['prices'][0] : null;
                PropertyCalendarCache::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'data' => $date,
                    ],
                    [
                        'status' => $status,
                        'min_nights' => $firstPrice['minStay'] ?? $day['min_nights'] ?? null,
                        'max_nights' => $day['max_nights'] ?? null,
                        'cached_at' => now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar disponibilidade', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Atualiza cache de preços.
     * API Stays calendar retorna por data: prices[0]._mcval { BRL, USD, EUR }.
     */
    public function syncRates(Property $property, int $days = 180): void
    {
        $dateFrom = now()->format('Y-m-d');
        $dateTo = now()->addDays($days)->format('Y-m-d');

        try {
            $calendar = $this->adapter->getRates($property->stays_property_id, $dateFrom, $dateTo);
            if (! is_array($calendar)) {
                return;
            }
            foreach ($calendar as $day) {
                $date = $day['date'] ?? null;
                if (! $date) {
                    continue;
                }
                $mcval = $day['prices'][0]['_mcval'] ?? null;
                $preco = 0;
                $moeda = 'BRL';
                if (is_array($mcval)) {
                    $preco = (float) ($mcval['BRL'] ?? $mcval['USD'] ?? reset($mcval) ?? 0);
                    $moeda = isset($mcval['BRL']) ? 'BRL' : (isset($mcval['USD']) ? 'USD' : 'BRL');
                } else {
                    $preco = (float) ($day['price'] ?? $day['_f_val'] ?? 0);
                    $moeda = $day['currency'] ?? 'BRL';
                }
                PropertyRateCache::updateOrCreate(
                    [
                        'property_id' => $property->id,
                        'data' => $date,
                    ],
                    [
                        'preco_base' => $preco,
                        'moeda' => $moeda,
                        'taxa_limpeza' => $day['cleaning_fee'] ?? null,
                        'cached_at' => now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar preços', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function mapAvailabilityStatus(string $status): string
    {
        $map = [
            'available' => 'available',
            'booked' => 'booked',
            'blocked' => 'blocked',
            'unavailable' => 'unavailable',
        ];

        return $map[strtolower($status)] ?? 'available';
    }
}
