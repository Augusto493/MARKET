<?php

// Script temporário para criar usuário admin e sincronizar
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Criar usuário admin
$user = User::firstOrCreate(
    ['email' => 'admin@hospedabc.com.br'],
    [
        'name'              => 'Admin HospedaBC',
        'password'          => bcrypt('admin123'),
        'email_verified_at' => now(),
    ]
);

echo "✅ Usuário: {$user->email} | ID: {$user->id}\n";

// Verificar se sistema de roles existe (Spatie ou próprio)
if (method_exists($user, 'assignRole')) {
    try {
        // Verificar se a role existe
        $roleClass = \Spatie\Permission\Models\Role::class;
        $role = $roleClass::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $user->assignRole('superadmin');
        echo "✅ Role 'superadmin' atribuída.\n";
    } catch (\Exception $e) {
        echo "⚠️  Roles: " . $e->getMessage() . "\n";
        echo "   O usuário foi criado sem role (você pode atribuir manualmente).\n";
    }
} else {
    echo "ℹ️  Sistema de roles não encontrado — usuário criado sem role.\n";
    
    // Verificar se existe coluna role na tabela users
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');
    if (in_array('role', $columns)) {
        $user->update(['role' => 'superadmin']);
        echo "✅ Campo 'role' definido como superadmin diretamente.\n";
    }
}

echo "\n📋 Resumo:\n";
echo "   Email: admin@hospedabc.com.br\n";
echo "   Senha: admin123\n";
echo "   URL:   http://localhost/market/public/admin/dashboard\n";
