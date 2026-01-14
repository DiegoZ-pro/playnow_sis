<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Negro', 'hex_code' => '#000000', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Blanco', 'hex_code' => '#FFFFFF', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rojo', 'hex_code' => '#FF0000', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Azul', 'hex_code' => '#0000FF', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Verde', 'hex_code' => '#008000', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Amarillo', 'hex_code' => '#FFFF00', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gris', 'hex_code' => '#808080', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rosa', 'hex_code' => '#FFC0CB', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Naranja', 'hex_code' => '#FFA500', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Morado', 'hex_code' => '#800080', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('colors')->insert($colors);
    }
}