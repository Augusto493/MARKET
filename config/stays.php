<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stays.net API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuração para integração com a API da Stays.net
    | Os endpoints reais devem ser configurados aqui quando disponíveis
    |
    */

    'adapter' => env('STAYS_ADAPTER', 'mock'), // 'mock' ou 'http'

    'http' => [
        'base_url' => env('STAYS_BASE_URL', 'https://api.stays.net'),
        'timeout' => env('STAYS_TIMEOUT', 30),
        'retry_attempts' => env('STAYS_RETRY_ATTEMPTS', 3),
    ],

    // Prefixo oficial da API Stays: https://stays.net/external-api/
    'endpoints' => [
        'test' => '/external/v1/booking/searchfilter',
        'search_listings' => '/external/v1/booking/search-listings',
        'calculate_price' => '/external/v1/booking/calculate-price',
        'properties' => '/external/v1/content/properties',
        'listings' => '/external/v1/content/listings',
        'listing' => '/external/v1/content/listings/{id}',
        'calendar_listing' => '/external/v1/calendar/listing/{id}',
        'reservations' => '/external/v1/booking/reservations',
        'reservation' => '/external/v1/booking/reservations/{id}',
    ],

    'cache' => [
        'availability_days' => env('STAYS_CACHE_AVAILABILITY_DAYS', 180),
        'rates_days' => env('STAYS_CACHE_RATES_DAYS', 180),
    ],
];
