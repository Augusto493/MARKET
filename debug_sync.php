<?php

// Diagnóstico do stays:sync
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Owner;
use App\Services\Stays\StaysAdapterFactory;
use App\Services\Stays\StaysService;

$owner = Owner::find(1);
if (! $owner) {
    echo "❌ Owner ID=1 não encontrado.\n";
    exit(1);
}

echo "✅ Owner: {$owner->nome}\n";
echo "   stays_base_url:  {$owner->stays_base_url}\n";
echo "   stays_client_id: {$owner->stays_client_id}\n";
echo "   has secret:      " . (! empty($owner->stays_client_secret) ? 'sim' : 'não') . "\n\n";

// Testar adapter
$adapter = StaysAdapterFactory::forOwner($owner);
echo "✅ Adapter criado: " . get_class($adapter) . "\n\n";

// Testar conexão
echo "🔌 Testando conexão...\n";
$conn = $adapter->testConnection();
echo "   Sucesso: " . ($conn['success'] ? 'sim' : 'não') . "\n";
echo "   Mensagem: " . ($conn['message'] ?? 'N/A') . "\n\n";

if (! $conn['success']) {
    echo "❌ Conexão falhou. Verifique as credenciais da Stays.net.\n";
    exit(1);
}

// Testar listagem de imóveis
echo "📋 Listando imóveis...\n";
$properties = $adapter->listProperties();
echo "   Imóveis encontrados: " . count($properties) . "\n";
if (! empty($properties)) {
    echo "   Primeiro: " . ($properties[0]['id'] ?? 'sem id') . "\n\n";
}

// Testar getAvailability (a correção principal)
if (! empty($properties)) {
    $propId = $properties[0]['id'];
    $from = date('Y-m-d');
    $to   = date('Y-m-d', strtotime('+30 days'));
    
    echo "📅 Testando Calendar API para {$propId}...\n";
    $avail = $adapter->getAvailability($propId, $from, $to);
    echo "   Retornou " . count($avail) . " dias de disponibilidade\n";
    
    if (empty($avail)) {
        echo "   ⚠️ Nenhum dado retornado (pode ser imóvel sem disponibilidade)\n";
    } else {
        echo "   ✅ Calendar API funcionando!\n";
        echo "   Exemplo: " . json_encode($avail[0] ?? []) . "\n";
    }
}

echo "\n✅ Diagnóstico concluído.\n";
