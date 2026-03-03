<?php

/**
 * Faz download das imagens dos imóveis do banco para o diretório local
 */
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PropertyPhoto;

$storageDir = __DIR__ . '/public/images/imoveis';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
    echo "Diretório criado: $storageDir\n";
}

$photos = PropertyPhoto::all();
echo "Total de fotos para baixar: " . $photos->count() . "\n\n";

$updated = 0;
$failed  = 0;

foreach ($photos as $photo) {
    $url = $photo->url;
    
    // Se já é local, pular
    if (str_starts_with($url, '/') || str_starts_with($url, 'http://localhost')) {
        echo "  Já é local: $url\n";
        continue;
    }
    
    // Gerar nome de arquivo local
    $filename = 'imovel_' . $photo->property_id . '_' . $photo->id . '.jpg';
    $localPath = $storageDir . '/' . $filename;
    $publicUrl = '/images/imoveis/' . $filename;
    
    echo "Baixando foto ID={$photo->id} para {$filename}...\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; HospedaBC/1.0)',
        CURLOPT_HTTPHEADER     => [
            'Referer: https://hospedabc.com.br',
            'Accept: image/*',
        ],
    ]);
    
    $imageData = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr   = curl_error($ch);
    curl_close($ch);
    
    if ($curlErr || $httpCode !== 200 || !$imageData || strlen($imageData) < 1000) {
        echo "  FALHOU (HTTP $httpCode, CURL: $curlErr, tam=" . strlen($imageData ?? '') . ")\n";
        $failed++;
        continue;
    }
    
    file_put_contents($localPath, $imageData);
    echo "  OK! Salvo (" . round(filesize($localPath) / 1024) . "KB) -> $publicUrl\n";
    
    // Atualizar no banco
    $photo->update([
        'url'           => $publicUrl,
        'thumbnail_url' => $publicUrl,
    ]);
    
    $updated++;
}

echo "\n=== DONE ===\n";
echo "Atualizadas: $updated\n";
echo "Falharam:    $failed\n";
