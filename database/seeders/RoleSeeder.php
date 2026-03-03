<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        $permissions = [
            'view owners',
            'create owners',
            'edit owners',
            'delete owners',
            'sync owners',
            'view properties',
            'create properties',
            'edit properties',
            'delete properties',
            'view reservations',
            'create reservations',
            'edit reservations',
            'delete reservations',
            'view markup rules',
            'create markup rules',
            'edit markup rules',
            'delete markup rules',
            'view logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Criar roles
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view owners',
            'create owners',
            'edit owners',
            'sync owners',
            'view properties',
            'edit properties',
            'view reservations',
            'edit reservations',
            'view markup rules',
            'create markup rules',
            'edit markup rules',
            'view logs',
        ]);

        $operador = Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']);
        $operador->givePermissionTo([
            'view owners',
            'view properties',
            'view reservations',
            'edit reservations',
            'view logs',
        ]);

        $ownerView = Role::firstOrCreate(['name' => 'owner_view', 'guard_name' => 'web']);
        $ownerView->givePermissionTo([
            'view properties',
            'view reservations',
        ]);
    }
}
