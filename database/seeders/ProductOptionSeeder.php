<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionChoice;
use Illuminate\Database\Seeder;

class ProductOptionSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // Espetos de carne → ponto da carne (select obrigatório)
        // -------------------------------------------------------
        $carnes = Product::where('name', 'like', '%carne%')
            ->orWhere('name', 'like', '%misto%')
            ->get();

        foreach ($carnes as $product) {
            $option = ProductOption::firstOrCreate(
                ['product_id' => $product->id, 'label' => 'Ponto da carne'],
                ['type' => 'select', 'required' => true, 'order' => 1]
            );

            $pontos = ['Mal passado', 'Ao ponto', 'Bem passado'];
            foreach ($pontos as $i => $ponto) {
                ProductOptionChoice::firstOrCreate(
                    ['option_id' => $option->id, 'label' => $ponto],
                    ['price_add' => 0, 'order' => $i + 1]
                );
            }
        }

        // -------------------------------------------------------
        // Porções → adicionais pagos (extra)
        // -------------------------------------------------------
        $porcoes = Product::where('name', 'like', 'Porção%')->get();

        foreach ($porcoes as $product) {
            $option = ProductOption::firstOrCreate(
                ['product_id' => $product->id, 'label' => 'Adicionais'],
                ['type' => 'extra', 'required' => false, 'order' => 1]
            );

            $adicionais = [
                ['label' => 'Queijo ralado',  'price_add' => 3.00],
                ['label' => 'Bacon',          'price_add' => 4.00],
                ['label' => 'Cheddar',        'price_add' => 4.00],
                ['label' => 'Catupiry',       'price_add' => 4.00],
            ];

            foreach ($adicionais as $i => $adicional) {
                ProductOptionChoice::firstOrCreate(
                    ['option_id' => $option->id, 'label' => $adicional['label']],
                    ['price_add' => $adicional['price_add'], 'order' => $i + 1]
                );
            }

            // Toggle: meia porção (sem acréscimo, mas muda preparo)
            ProductOption::firstOrCreate(
                ['product_id' => $product->id, 'label' => 'Meia porção'],
                ['type' => 'toggle', 'required' => false, 'order' => 2]
            );

            // Observação livre
            ProductOption::firstOrCreate(
                ['product_id' => $product->id, 'label' => 'Observação'],
                ['type' => 'text', 'required' => false, 'order' => 3]
            );
        }

        // -------------------------------------------------------
        // Espetos de frango → nível de tempero (select)
        // -------------------------------------------------------
        $frango = Product::where('name', 'like', '%frango%')->get();

        foreach ($frango as $product) {
            $option = ProductOption::firstOrCreate(
                ['product_id' => $product->id, 'label' => 'Tempero'],
                ['type' => 'select', 'required' => false, 'order' => 1]
            );

            $temperos = ['Suave', 'Tradicional', 'Picante'];
            foreach ($temperos as $i => $tempero) {
                ProductOptionChoice::firstOrCreate(
                    ['option_id' => $option->id, 'label' => $tempero],
                    ['price_add' => 0, 'order' => $i + 1]
                );
            }
        }

        // -------------------------------------------------------
        // Caipirinha → tipo de fruta (select)
        // -------------------------------------------------------
        $caipirinha = Product::where('name', 'Caipirinha')->first();

        if ($caipirinha) {
            $option = ProductOption::firstOrCreate(
                ['product_id' => $caipirinha->id, 'label' => 'Fruta'],
                ['type' => 'select', 'required' => true, 'order' => 1]
            );

            $frutas = ['Limão', 'Morango', 'Maracujá', 'Kiwi'];
            foreach ($frutas as $i => $fruta) {
                ProductOptionChoice::firstOrCreate(
                    ['option_id' => $option->id, 'label' => $fruta],
                    ['price_add' => 0, 'order' => $i + 1]
                );
            }

            // Toggle: com açúcar / sem açúcar
            ProductOption::firstOrCreate(
                ['product_id' => $caipirinha->id, 'label' => 'Sem açúcar'],
                ['type' => 'toggle', 'required' => false, 'order' => 2]
            );
        }

        // -------------------------------------------------------
        // Todos os produtos → observação livre (se ainda não tiver)
        // -------------------------------------------------------
        Product::all()->each(function ($product) {
            ProductOption::firstOrCreate(
                ['product_id' => $product->id, 'label' => 'Observação'],
                ['type' => 'text', 'required' => false, 'order' => 99]
            );
        });
    }
}
