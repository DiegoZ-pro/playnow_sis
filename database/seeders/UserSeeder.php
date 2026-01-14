<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'role_id' => 1, // Administrador
                'name' => 'Administrador',
                'email' => 'admin@playnow.com',
                'password' => Hash::make('admin123'),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 2, // Vendedor
                'name' => 'Vendedor Demo',
                'email' => 'vendedor@playnow.com',
                'password' => Hash::make('vendedor123'),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}