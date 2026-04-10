<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Choices são as opções disponíveis dentro de um product_option
        // Usadas pelos types: select (escolha única) e extra (adicional pago)
        // Ex: option "Ponto da carne" → choices: Mal passado, Ao ponto, Bem passado
        // Ex: option "Adicionais"     → choices: Queijo extra (+R$2), Bacon (+R$3)
        Schema::create('product_option_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')
                ->constrained('product_options')
                ->cascadeOnDelete();
            $table->string('label')->comment('Ex: Ao ponto, Queijo extra');
            $table->decimal('price_add', 8, 2)->default(0)->comment('Acréscimo no preço do item');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_choices');
    }
};
