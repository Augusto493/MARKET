<?php

// Lê o owner do SQLite e cria no MySQL via Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Owner;
use App\Models\User;

// Ler do SQLite via PDO
$sqlitePath = __DIR__ . '/database/database.sqlite';
if (! file_exists($sqlitePath)) {
    echo "❌ database.sqlite não encontrado.\n";
    exit(1);
}

$pdo = new PDO('sqlite:' . $sqlitePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->query('SELECT * FROM owners LIMIT 10');
$owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($owners)) {
    echo "❌ Nenhum owner no SQLite.\n";
    exit(1);
}

echo "Owners encontrados no SQLite: " . count($owners) . "\n\n";
foreach ($owners as $ownerData) {
    echo "  ID: {$ownerData['id']} | Nome: {$ownerData['nome']} | stays_client_id: {$ownerData['stays_client_id']}\n";
}

// Verificar se já existe admin user no MySQL
$adminUser = User::where('email', 'admin@hospedabc.com.br')->first();

echo "\n";

// Criar/atualizar cada owner no MySQL
foreach ($owners as $ownerData) {
    $existing = Owner::where('id', $ownerData['id'])->first();

    $data = [
        'nome'                    => $ownerData['nome'] ?? 'Owner',
        'email'                   => $ownerData['email'] ?? null,
        'telefone'                => $ownerData['telefone'] ?? null,
        'status'                  => $ownerData['status'] ?? 'active',
        'stays_base_url'          => $ownerData['stays_base_url'] ?? null,
        'stays_client_id'         => $ownerData['stays_client_id'] ?? null,
        'stays_client_secret'     => $ownerData['stays_client_secret'] ?? null,
        'stays_account_identifier'=> $ownerData['stays_account_identifier'] ?? null,
        'user_id'                 => $adminUser?->id,
    ];

    if ($existing) {
        $existing->update($data);
        echo "✅ Owner '{$data['nome']}' (ID: {$ownerData['id']}) atualizado no MySQL.\n";
    } else {
        Owner::create(array_merge($data, ['id' => $ownerData['id']]));
        echo "✅ Owner '{$data['nome']}' (ID: {$ownerData['id']}) criado no MySQL.\n";
    }
}

echo "\nPronto! Agora rode: php artisan stays:sync 1 --full\n";
