<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tennis',
                'slug' => 'tennis',
                'icon' => 'fa-solid fa-shoe-prints',
                'description' => 'Zapatillas deportivas de alto rendimiento',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gorras',
                'slug' => 'gorras',
                'icon' => 'fa-solid fa-hat-cowboy',
                'description' => 'Gorras deportivas y casuales',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Poleras',
                'slug' => 'poleras',
                'icon' => 'fa-solid fa-shirt',
                'description' => 'Poleras deportivas y casuales',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}