<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('label')->comment('Ex: Ponto da carne, Adicionais, Observação');
            $table->enum('type', [
                'toggle',  // sim/não — ex: sem cebola
                'select',  // escolha única — ex: ponto da carne
                'extra',   // adicional com preço — ex: queijo extra
                'text',    // campo livre — ex: observação do cliente
            ]);
            $table->boolean('required')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
