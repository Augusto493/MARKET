<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        if (!User::where('email', 'admin@hospedabc.com.br')->exists()) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@hospedabc.com.br',
                'password' => bcrypt('admin123'),
            ]);
            $admin->assignRole('superadmin');
        }

        if (!Owner::exists()) {
            Owner::create([
                'nome' => 'Demo Owner (Mock)',
                'email' => 'demo@hospedabc.com.br',
                'status' => 'active',
                'sync_status' => 'pending',
            ]);
        }
    }
}
