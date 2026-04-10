<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Espetos',       'order' => 1],
            ['name' => 'Porções',       'order' => 2],
            ['name' => 'Chopps',        'order' => 3],
            ['name' => 'Bebidas',       'order' => 4],
            ['name' => 'Refrigerantes', 'order' => 5],
            ['name' => 'Combos',        'order' => 6],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['order' => $cat['order'], 'active' => true]
            );
        }
    }
}
