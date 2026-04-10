<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Armazena as escolhas do garçom para cada item do pedido
        // Ex: item "Espeto de frango" → opção "Ponto" → escolha "Ao ponto"
        // Ex: item "Porção batata"    → opção "Adicional" → escolha "Queijo extra" (+R$2)
        // Ex: item "X-burguer"        → opção "Obs livre" → text_value "sem picles"
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->cascadeOnDelete();
            $table->foreignId('option_id')
                ->constrained('product_options')
                ->restrictOnDelete();
            $table->foreignId('choice_id')
                ->nullable()
                ->constrained('product_option_choices')
                ->nullOnDelete()
                ->comment('Nulo para tipos toggle e text');
            $table->string('text_value')->nullable()
                ->comment('Preenchido apenas para type = text');

            // Snapshot do acréscimo no momento do pedido
            $table->decimal('price_delta', 8, 2)->default(0)
                ->comment('Valor cobrado no momento do pedido (0 para toggle/text)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};
