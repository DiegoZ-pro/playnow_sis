<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tallas para Tennis (category_id = 1)
        $tennisSizes = [];
        for ($i = 35; $i <= 46; $i++) {
            $tennisSizes[] = [
                'category_id' => 1,
                'value' => (string)$i,
                'order' => $i - 34,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Tallas para Gorras (category_id = 2)
        $gorrasSizes = [
            ['category_id' => 2, 'value' => 'Única', 'order' => 1, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 2, 'value' => 'S/M', 'order' => 2, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 2, 'value' => 'L/XL', 'order' => 3, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        // Tallas para Poleras (category_id = 3)
        $polerasSizes = [
            ['category_id' => 3, 'value' => 'XS', 'order' => 1, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'value' => 'S', 'order' => 2, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'value' => 'M', 'order' => 3, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'value' => 'L', 'order' => 4, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'value' => 'XL', 'order' => 5, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => 3, 'value' => 'XXL', 'order' => 6, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('sizes')->insert(array_merge($tennisSizes, $gorrasSizes, $polerasSizes));
    }
}