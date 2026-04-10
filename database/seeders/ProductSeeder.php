<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Espetos
            'Espetos' => [
                ['name' => 'Espeto de frango',          'price' => 8.00],
                ['name' => 'Espeto de carne',           'price' => 10.00],
                ['name' => 'Espeto de linguiça',        'price' => 9.00],
                ['name' => 'Espeto de queijo coalho',   'price' => 8.00],
                ['name' => 'Espeto misto',              'price' => 12.00],
                ['name' => 'Espeto de coração de frango', 'price' => 7.00],
            ],

            // Porções
            'Porções' => [
                ['name' => 'Porção de batata frita',    'price' => 25.00],
                ['name' => 'Porção de mandioca frita',  'price' => 22.00],
                ['name' => 'Porção de frango frito',    'price' => 35.00],
                ['name' => 'Porção de calabresa',       'price' => 30.00],
                ['name' => 'Porção de bolinho de bacalhau', 'price' => 32.00],
                ['name' => 'Tábua de frios',            'price' => 45.00],
            ],

            // Chopps
            'Chopps' => [
                ['name' => 'Chopp 300ml',               'price' => 9.00],
                ['name' => 'Chopp 500ml',               'price' => 14.00],
                ['name' => 'Chopp 1L',                  'price' => 25.00],
                ['name' => 'Chopp escuro 300ml',        'price' => 11.00],
                ['name' => 'Chopp escuro 500ml',        'price' => 16.00],
            ],

            // Bebidas
            'Bebidas' => [
                ['name' => 'Água sem gás 500ml',        'price' => 5.00],
                ['name' => 'Água com gás 500ml',        'price' => 6.00],
                ['name' => 'Suco de laranja 500ml',     'price' => 12.00],
                ['name' => 'Suco de maracujá 500ml',    'price' => 12.00],
                ['name' => 'Cerveja long neck',         'price' => 11.00],
                ['name' => 'Dose de cachaça',           'price' => 8.00],
                ['name' => 'Caipirinha',                'price' => 18.00],
            ],

            // Refrigerantes
            'Refrigerantes' => [
                ['name' => 'Coca-Cola lata',            'price' => 7.00],
                ['name' => 'Guaraná lata',              'price' => 6.00],
                ['name' => 'Coca-Cola 600ml',           'price' => 10.00],
                ['name' => 'Soda limonada',             'price' => 8.00],
            ],

            // Combos
            'Combos' => [
                ['name' => 'Combo 5 espetos + chopp 500ml',  'price' => 49.00],
                ['name' => 'Combo 10 espetos + chopp 1L',    'price' => 89.00],
                ['name' => 'Combo porção + 2 chopps 500ml',  'price' => 55.00],
            ],
        ];

        $order = 1;

        foreach ($products as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($items as $item) {
                Product::firstOrCreate(
                    ['name' => $item['name'], 'category_id' => $category->id],
                    [
                        'price'       => $item['price'],
                        'description' => null,
                        'image'       => null,
                        'active'      => true,
                        'order'       => $order++,
                    ]
                );
            }
        }
    }
}
