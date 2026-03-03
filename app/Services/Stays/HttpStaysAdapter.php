<?php

namespace App\Services\Stays;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpStaysAdapter implements StaysAdapterInterface
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct(string $baseUrl, string $clientId, string $clientSecret)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    protected function getAuthHeaders(): array
    {
        return [
            'X-ClientId'     => $this->clientId,
            'X-ClientSecret' => $this->clientSecret,
            'Accept'         => 'application/json',
            'Content-Type'   => 'application/json',
        ];
    }

    /**
     * Faz uma requisição à API da Stays.
     * Para GET, $data é enviado como query string (Laravel trata automaticamente).
     * Para POST/PUT/PATCH, $data é enviado como corpo JSON.
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

            $http = Http::withHeaders($this->getAuthHeaders())->timeout(30);

            $method = strtolower($method);

            if ($method === 'get') {
                // GET: $data vira query string (?from=...&to=...)
                $response = $http->get($url, $data);
            } else {
                // POST, PUT, PATCH: $data vira body JSON
                $response = $http->{$method}($url, $data);
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            $body   = $response->body();
            $status = $response->status();

            // Se a resposta for HTML (erro de rota/nginx), dar mensagem amigável
            $errorMessage = $body;
            if (preg_match('/^\s*<\s*!?\s*doctype\s+html/i', $body)) {
                $errorMessage = $status === 404
                    ? 'Endpoint não encontrado (404). Verifique a URL base da API Stays.net.'
                    : "O servidor retornou uma página HTML em vez de JSON (HTTP {$status}). Verifique a URL base.";
            }

            Log::warning('Stays API não-sucesso', [
                'endpoint' => $endpoint,
                'status'   => $status,
                'error'    => substr($errorMessage, 0, 500),
            ]);

            return [
                'success' => false,
                'error'   => $errorMessage,
                'status'  => $status,
            ];

        } catch (\Exception $e) {
            Log::error('Stays API Exception', [
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Testa conexão usando GET /external/v1/booking/searchfilter.
     */
    public function testConnection(): array
    {
        $result = $this->makeRequest('GET', '/external/v1/booking/searchfilter');

        return [
            'success' => $result['success'] ?? false,
            'message' => $result['success']
                ? 'Conexão estabelecida com a API Stays.net'
                : ($result['error'] ?? 'Erro desconhecido'),
            'account' => $result['data'] ?? null,
        ];
    }

    /**
     * Retorna dados do searchfilter para resolver nomes de comodidades.
     */
    public function getSearchFilter(): array
    {
        $result = $this->makeRequest('GET', '/external/v1/booking/searchfilter');
        if (! ($result['success'] ?? false)) {
            return [];
        }
        $data = $result['data'] ?? [];
        return [
            'amenities' => $data['amenities'] ?? [],
            'inventory' => $data['inventory'] ?? [],
        ];
    }

    public function listProperties(): array
    {
        $result = $this->makeRequest('GET', '/external/v1/content/listings');
        if (! $result['success']) {
            return [];
        }
        $data = $result['data'] ?? [];
        if (! is_array($data)) {
            return [];
        }
        // Garantir que cada item tem 'id' (API pode devolver id ou _id)
        return array_map(function ($item) {
            if (! is_array($item)) {
                return $item;
            }
            if (empty($item['id']) && ! empty($item['_id'])) {
                $item['id'] = $item['_id'];
            }
            return $item;
        }, $data);
    }

    public function getPropertyDetails(string $propertyId): array
    {
        $result = $this->makeRequest('GET', "/external/v1/content/listings/{$propertyId}");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function getPropertyPhotos(string $propertyId): array
    {
        $listing = $this->getPropertyDetails($propertyId);

        $images = $listing['_t_imagesMeta'] ?? $listing['images'] ?? [];
        if (is_array($images) && ! empty($images)) {
            return $images;
        }

        $mainMeta = $listing['_t_mainImageMeta'] ?? null;
        if (is_array($mainMeta) && ! empty($mainMeta['url'])) {
            return [[
                'url'   => $mainMeta['url'],
                'id'    => $listing['_idmainImage'] ?? null,
                'order' => 0,
                'main'  => true,
            ]];
        }

        return [];
    }

    public function getAvailability(string $propertyId, string $dateFrom, string $dateTo): array
    {
        // CORREÇÃO: params passados como array → Laravel converte para query string
        $result = $this->makeRequest('GET', "/external/v1/calendar/listing/{$propertyId}", [
            'from' => $dateFrom,
            'to'   => $dateTo,
        ]);
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function getRates(string $propertyId, string $dateFrom, string $dateTo): array
    {
        // CORREÇÃO: params passados como array → Laravel converte para query string
        $result = $this->makeRequest('GET', "/external/v1/calendar/listing/{$propertyId}", [
            'from' => $dateFrom,
            'to'   => $dateTo,
        ]);
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function createReservation(array $payload): array
    {
        $result = $this->makeRequest('POST', '/external/v1/booking/reservations', $payload);
        return $result['success'] ? ($result['data'] ?? []) : [
            'success' => false,
            'error'   => $result['error'] ?? 'Erro ao criar reserva',
        ];
    }

    public function getReservation(string $reservationId): array
    {
        $result = $this->makeRequest('GET', "/external/v1/booking/reservations/{$reservationId}");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function listReservations(string $dateFrom, string $dateTo): array
    {
        $result = $this->makeRequest('GET', '/external/v1/booking/reservations', [
            'from'     => $dateFrom,
            'to'       => $dateTo,
            'dateType' => 'arrival',
        ]);
        $data = $result['data'] ?? [];
        return is_array($data) ? $data : [];
    }

    /**
     * Busca listagens disponíveis para um período.
     */
    public function searchListings(array $params): array
    {
        $body = array_filter([
            'from'    => $params['from'] ?? null,
            'to'      => $params['to'] ?? null,
            'guests'  => $params['guests'] ?? 1,
            'rooms'   => $params['rooms'] ?? null,
            'cities'  => $params['cities'] ?? null,
            'regions' => $params['regions'] ?? null,
            'skip'    => $params['skip'] ?? null,
            'limit'   => $params['limit'] ?? null,
        ], fn ($v) => $v !== null);

        $result = $this->makeRequest('POST', '/external/v1/booking/search-listings', $body);
        if (! ($result['success'] ?? false)) {
            return [];
        }
        $data = $result['data'] ?? [];
        return is_array($data) ? $data : [];
    }

    /**
     * Calcula preço para uma ou mais listagens.
     * Retorna: array de [{ _idlisting, from, to, guests, _mctotal: { BRL, ... }, ... }].
     */
    public function calculatePrice(array $listingIds, string $from, string $to, int $guests = 1, ?string $promocode = null): array
    {
        if (empty($listingIds)) {
            return [];
        }

        $body = [
            'listingIds' => array_values($listingIds),
            'from'       => $from,
            'to'         => $to,
            'guests'     => $guests,
        ];

        if ($promocode !== null && $promocode !== '') {
            $body['promocode'] = $promocode;
        }

        $result = $this->makeRequest('POST', '/external/v1/booking/calculate-price', $body);
        if (! ($result['success'] ?? false)) {
            return [];
        }
        $data = $result['data'] ?? [];
        return is_array($data) ? $data : [];
    }
}
