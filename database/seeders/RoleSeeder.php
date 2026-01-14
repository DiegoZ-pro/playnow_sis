<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'permissions' => json_encode([
                    'dashboard.view',
                    'inventory.view',
                    'inventory.create',
                    'inventory.edit',
                    'inventory.delete',
                    'sales.view',
                    'sales.create',
                    'sales.edit',
                    'sales.delete',
                    'customers.view',
                    'customers.create',
                    'customers.edit',
                    'customers.delete',
                    'reports.view',
                    'reports.export',
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'settings.view',
                    'settings.edit',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Vendedor',
                'slug' => 'vendedor',
                'permissions' => json_encode([
                    'dashboard.view',
                    'inventory.view',
                    'sales.view',
                    'sales.create',
                    'customers.view',
                    'customers.create',
                    'reports.view',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}