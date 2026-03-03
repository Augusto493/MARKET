<?php

// Ler o SQLite diretamente para ver o secret original
$sqlitePath = __DIR__ . '/database/database.sqlite';

if (!file_exists($sqlitePath)) {
    echo "SQLite NAO encontrado em: $sqlitePath\n";
    
    // Tentar MySQL diretamente
    require __DIR__ . '/vendor/autoload.php';
    $app = require __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $pdo = DB::connection()->getPdo();
    $stmt = $pdo->query("SELECT id, nome, stays_client_id, LEFT(stays_client_secret, 100) as secret_preview, stays_client_secret, stays_base_url FROM owners LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "ID={$r['id']} | client_id={$r['stays_client_id']} | base_url={$r['stays_base_url']}\n";
        echo "secret_len=" . strlen($r['stays_client_secret']) . "\n";
        echo "secret_preview=" . $r['secret_preview'] . "\n\n";
    }
    exit;
}

echo "SQLite encontrado!\n";
$pdo = new PDO('sqlite:' . $sqlitePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query('SELECT id, nome, stays_client_id, stays_client_secret, stays_base_url FROM owners LIMIT 5');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    echo "Nenhum owner no SQLite.\n";
} else {
    foreach ($rows as $r) {
        echo "ID: {$r['id']}\n";
        echo "  Nome:      {$r['nome']}\n";
        echo "  client_id: {$r['stays_client_id']}\n";
        echo "  secret:    {$r['stays_client_secret']}\n";
        echo "  base_url:  {$r['stays_base_url']}\n\n";
    }
}
