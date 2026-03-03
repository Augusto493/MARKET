<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Owner;

$owner = Owner::find(1);
$owner->refresh();

echo "=== TESTE FINAL DA API STAYS.NET ===\n\n";
echo "URL:      " . $owner->stays_base_url . "\n";
echo "ClientId: " . $owner->stays_client_id . "\n";

$secret = $owner->stays_client_secret;
echo "Secret:   " . $secret . " (tam=" . strlen($secret) . ")\n\n";

$baseUrl = rtrim($owner->stays_base_url, '/');
$url = $baseUrl . '/external/v1/booking/searchfilter';

echo "Chamando: $url\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_HTTPHEADER     => [
        'X-ClientId: ' . $owner->stays_client_id,
        'X-ClientSecret: ' . $secret,
        'Accept: application/json',
    ],
    CURLOPT_SSL_VERIFYPEER => false,
]);
$response = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr   = curl_error($ch);
curl_close($ch);

echo "CURL error: " . ($curlErr ?: 'nenhum') . "\n";
echo "HTTP Status: $httpCode\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "=== SUCESSO! API FUNCIONANDO! ===\n";
    echo "Chaves na resposta: " . implode(', ', array_keys($data ?? [])) . "\n";
} elseif ($httpCode === 401) {
    echo "=== AINDA 401 - CREDENCIAIS INVÁLIDAS ===\n";
    echo "Resposta: " . substr($response, 0, 200) . "\n";
    echo "\nVoce precisa verificar as credenciais no painel Stays.net:\n";
    echo "  https://fcezar.stays.net/dashboard/settings/api\n";
    echo "  - X-ClientId:     " . $owner->stays_client_id . "\n";
    echo "  - X-ClientSecret: [o secret correto do painel]\n";
} else {
    echo "Resposta inesperada ($httpCode): " . substr($response, 0, 300) . "\n";
}
