# Stays.net Adapter - Documentação

## Visão Geral

O sistema utiliza um adapter pattern para integrar com a API da Stays.net. Isso permite trocar facilmente entre um adapter mock (para desenvolvimento) e o adapter HTTP real (para produção).

## Estrutura

- **StaysAdapterInterface**: Interface que define os métodos obrigatórios
- **MockStaysAdapter**: Implementação mock para desenvolvimento/testes
- **HttpStaysAdapter**: Implementação HTTP (precisa ser preenchida com endpoints reais)
- **StaysService**: Service layer que normaliza dados e gerencia sincronização

## Como Implementar os Endpoints Reais

### 1. Edite `app/Services/Stays/HttpStaysAdapter.php`

Substitua os métodos marcados com `// TODO:` pelos endpoints reais da API Stays.net.

### 2. Configure Autenticação

No método `getAuthHeaders()`, implemente a autenticação conforme a documentação da Stays:

```php
protected function getAuthHeaders(): array
{
    return [
        'Authorization' => 'Bearer ' . $this->getAccessToken(),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-API-Key' => $this->clientId, // se necessário
    ];
}
```

### 3. Implemente Obtenção de Token

No método `getAccessToken()`, implemente OAuth2 ou autenticação similar:

```php
protected function getAccessToken(): string
{
    if ($this->token) {
        return $this->token;
    }

    $response = Http::post("{$this->baseUrl}/oauth/token", [
        'client_id' => $this->clientId,
        'client_secret' => $this->clientSecret,
        'grant_type' => 'client_credentials',
    ]);

    $this->token = $response->json()['access_token'];
    return $this->token;
}
```

### 4. Mapeie os Endpoints

Para cada método, mapeie para o endpoint real da Stays:

- `testConnection()` → `GET /api/v1/ping` ou similar
- `listProperties()` → `GET /api/v1/properties`
- `getPropertyDetails($id)` → `GET /api/v1/properties/{id}`
- `getPropertyPhotos($id)` → `GET /api/v1/properties/{id}/photos`
- `getAvailability($id, $from, $to)` → `GET /api/v1/properties/{id}/availability`
- `getRates($id, $from, $to)` → `GET /api/v1/properties/{id}/rates`
- `createReservation($payload)` → `POST /api/v1/reservations`
- `getReservation($id)` → `GET /api/v1/reservations/{id}`
- `listReservations($from, $to)` → `GET /api/v1/reservations`

### 5. Normalize os Dados

O `StaysService` espera dados em um formato específico. Se a API retornar em formato diferente, normalize no adapter ou ajuste o service.

## Formato Esperado dos Dados

### Property Details
```php
[
    'id' => 'string',
    'name' => 'string',
    'description' => 'string',
    'short_description' => 'string',
    'max_guests' => int,
    'bedrooms' => int,
    'beds' => int,
    'bathrooms' => int,
    'city' => 'string',
    'neighborhood' => 'string',
    'latitude' => float,
    'longitude' => float,
    'status' => 'active|inactive',
]
```

### Photos
```php
[
    [
        'id' => 'string',
        'url' => 'string',
        'thumbnail' => 'string',
        'order' => int,
        'main' => bool,
        'caption' => 'string',
    ]
]
```

### Availability
```php
[
    [
        'date' => 'Y-m-d',
        'status' => 'available|booked|blocked|unavailable',
        'min_nights' => int|null,
        'max_nights' => int|null,
    ]
]
```

### Rates
```php
[
    [
        'date' => 'Y-m-d',
        'price' => float,
        'currency' => 'BRL',
        'cleaning_fee' => float|null,
    ]
]
```

## Configuração

No `.env`:
```env
STAYS_ADAPTER=http  # ou 'mock' para desenvolvimento
STAYS_BASE_URL=https://api.stays.net
```

As credenciais de cada owner são armazenadas criptografadas na tabela `owners`.

## Testando

Para testar a conexão de um owner:
```bash
php artisan tinker
```
```php
$owner = \App\Models\Owner::find(1);
$adapter = new \App\Services\Stays\HttpStaysAdapter([
    'base_url' => $owner->stays_base_url,
    'client_id' => $owner->stays_client_id,
    'client_secret' => $owner->stays_client_secret,
]);
$result = $adapter->testConnection();
dd($result);
```

Ou use a interface admin: `/admin/owners/{id}/test-connection`
