<?php

namespace App\Services\Stays;

use Carbon\Carbon;

class MockStaysAdapter implements StaysAdapterInterface
{
    public function testConnection(): array
    {
        return [
            'success' => true,
            'message' => 'Conexão mockada bem-sucedida',
            'account' => 'Mock Account',
        ];
    }

    public function getSearchFilter(): array
    {
        return ['amenities' => [], 'inventory' => []];
    }

    public function listProperties(): array
    {
        return [
            [
                'id' => 'prop_001',
                'name' => 'Apartamento Vista Mar - Balneário Camboriú',
                'status' => 'active',
            ],
            [
                'id' => 'prop_002',
                'name' => 'Casa com Piscina - Centro',
                'status' => 'active',
            ],
            [
                'id' => 'prop_003',
                'name' => 'Studio Moderno - Praia Central',
                'status' => 'active',
            ],
        ];
    }

    public function getPropertyDetails(string $propertyId): array
    {
        $properties = [
            'prop_001' => [
                'id' => 'prop_001',
                'name' => 'Apartamento Vista Mar - Balneário Camboriú',
                'description' => 'Apartamento moderno com vista para o mar, localizado no coração de Balneário Camboriú. Ideal para casais e famílias.',
                'short_description' => 'Apartamento com vista mar no centro',
                'max_guests' => 4,
                'bedrooms' => 2,
                'beds' => 2,
                'bathrooms' => 1,
                'city' => 'Balneário Camboriú',
                'neighborhood' => 'Centro',
                'latitude' => -26.9903,
                'longitude' => -48.6346,
                'status' => 'active',
            ],
            'prop_002' => [
                'id' => 'prop_002',
                'name' => 'Casa com Piscina - Centro',
                'description' => 'Casa espaçosa com piscina privativa, garagem e área de churrasco. Perfeita para grupos maiores.',
                'short_description' => 'Casa com piscina e garagem',
                'max_guests' => 8,
                'bedrooms' => 4,
                'beds' => 5,
                'bathrooms' => 3,
                'city' => 'Balneário Camboriú',
                'neighborhood' => 'Centro',
                'latitude' => -26.9920,
                'longitude' => -48.6320,
                'status' => 'active',
            ],
            'prop_003' => [
                'id' => 'prop_003',
                'name' => 'Studio Moderno - Praia Central',
                'description' => 'Studio aconchegante a poucos metros da praia, com tudo que você precisa para uma estadia confortável.',
                'short_description' => 'Studio próximo à praia',
                'max_guests' => 2,
                'bedrooms' => 0,
                'beds' => 1,
                'bathrooms' => 1,
                'city' => 'Balneário Camboriú',
                'neighborhood' => 'Praia Central',
                'latitude' => -26.9880,
                'longitude' => -48.6360,
                'status' => 'active',
            ],
        ];

        return $properties[$propertyId] ?? $properties['prop_001'];
    }

    public function getPropertyPhotos(string $propertyId): array
    {
        $photos = [
            'prop_001' => [
                ['url' => 'https://via.placeholder.com/800x600?text=Photo+1', 'thumbnail' => 'https://via.placeholder.com/400x300?text=Thumb+1', 'id' => 'photo_1', 'order' => 1, 'main' => true],
                ['url' => 'https://via.placeholder.com/800x600?text=Photo+2', 'thumbnail' => 'https://via.placeholder.com/400x300?text=Thumb+2', 'id' => 'photo_2', 'order' => 2, 'main' => false],
                ['url' => 'https://via.placeholder.com/800x600?text=Photo+3', 'thumbnail' => 'https://via.placeholder.com/400x300?text=Thumb+3', 'id' => 'photo_3', 'order' => 3, 'main' => false],
            ],
            'prop_002' => [
                ['url' => 'https://via.placeholder.com/800x600?text=Casa+1', 'thumbnail' => 'https://via.placeholder.com/400x300?text=Casa+Thumb+1', 'id' => 'photo_4', 'order' => 1, 'main' => true],
                ['url' => 'https://via.placeholder.com/800x600?text=Casa+2', 'thumbnail' => 'https://via.placeholder.com/400x300?text=Casa+Thumb+2', 'id' => 'photo_5', 'order' => 2, 'main' => false],
            ],
            'prop_003' => [
                ['url' => 'https://via.placeholder.com/800x600?text=Studio+1', 'thumbnail' => 'https://via.placeholder.com/400x300?text=Studio+Thumb+1', 'id' => 'photo_6', 'order' => 1, 'main' => true],
            ],
        ];

        return $photos[$propertyId] ?? [];
    }

    public function getAvailability(string $propertyId, string $dateFrom, string $dateTo): array
    {
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        $availability = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            // Simula disponibilidade: alguns dias ocupados aleatoriamente
            $status = 'available';
            if (rand(1, 10) <= 2) {
                $status = 'booked';
            }

            $availability[] = [
                'date' => $current->format('Y-m-d'),
                'status' => $status,
                'min_nights' => rand(2, 5),
                'max_nights' => null,
            ];

            $current->addDay();
        }

        return $availability;
    }

    public function getRates(string $propertyId, string $dateFrom, string $dateTo): array
    {
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        $rates = [];

        $basePrices = [
            'prop_001' => 250.00,
            'prop_002' => 450.00,
            'prop_003' => 180.00,
        ];

        $basePrice = $basePrices[$propertyId] ?? 200.00;

        $current = $start->copy();
        while ($current->lte($end)) {
            // Simula variação de preço (finais de semana mais caros)
            $multiplier = 1.0;
            if ($current->isWeekend()) {
                $multiplier = 1.3;
            }

            $rates[] = [
                'date' => $current->format('Y-m-d'),
                'price' => round($basePrice * $multiplier, 2),
                'currency' => 'BRL',
                'cleaning_fee' => 50.00,
            ];

            $current->addDay();
        }

        return $rates;
    }

    public function createReservation(array $payload): array
    {
        // Simula criação de reserva
        return [
            'success' => true,
            'reservation_id' => 'res_' . uniqid(),
            'status' => 'confirmed',
            'message' => 'Reserva criada com sucesso (mock)',
        ];
    }

    public function getReservation(string $reservationId): array
    {
        return [
            'id' => $reservationId,
            'status' => 'confirmed',
            'property_id' => 'prop_001',
            'checkin' => '2026-03-01',
            'checkout' => '2026-03-05',
            'guests' => 2,
        ];
    }

    public function listReservations(string $dateFrom, string $dateTo): array
    {
        return [
            [
                'id' => 'res_001',
                'property_id' => 'prop_001',
                'checkin' => $dateFrom,
                'checkout' => $dateTo,
                'status' => 'confirmed',
            ],
        ];
    }

    public function searchListings(array $params): array
    {
        $from = $params['from'] ?? now()->format('Y-m-d');
        $to = $params['to'] ?? now()->addDays(2)->format('Y-m-d');
        $guests = (int) ($params['guests'] ?? 1);
        $list = $this->listProperties();
        $basePrices = ['prop_001' => 250.00, 'prop_002' => 450.00, 'prop_003' => 180.00];
        $nights = max(1, (int) Carbon::parse($from)->diffInDays(Carbon::parse($to)));
        $out = [];
        foreach ($list as $i => $item) {
            $id = $item['id'] ?? ('prop_00' . ($i + 1));
            $total = ($basePrices[$id] ?? 200) * $nights;
            $out[] = [
                '_id' => $id,
                'id' => $id,
                '_mstitle' => ['pt_BR' => $item['name'] ?? 'Acomodação'],
                '_t_mainImageMeta' => ['url' => 'https://via.placeholder.com/800x600?text=Listing+' . ($i + 1)],
                'address' => ['region' => 'Centro', 'city' => 'Balneário Camboriú'],
                '_i_maxGuests' => 4,
                '_i_rooms' => 2,
                '_f_bathrooms' => 1,
                'bookingPrice' => [
                    'from' => $from,
                    'to' => $to,
                    '_mctotal' => ['BRL' => $total, 'USD' => round($total / 5, 2)],
                    'mainCurrency' => 'BRL',
                ],
            ];
        }
        return $out;
    }

    public function calculatePrice(array $listingIds, string $from, string $to, int $guests = 1, ?string $promocode = null): array
    {
        $basePrices = ['prop_001' => 250.00, 'prop_002' => 450.00, 'prop_003' => 180.00];
        $nights = max(1, (int) Carbon::parse($from)->diffInDays(Carbon::parse($to)));
        $out = [];
        foreach ($listingIds as $listingId) {
            $total = ($basePrices[$listingId] ?? 200.00) * $nights;
            $out[] = [
                '_idlisting' => $listingId,
                'from' => $from,
                'to' => $to,
                'guests' => $guests,
                '_mctotal' => ['BRL' => $total, 'USD' => round($total / 5, 2)],
                'feesIncluded' => true,
                'fees' => [],
                'mainCurrency' => 'BRL',
            ];
        }
        return $out;
    }
}
