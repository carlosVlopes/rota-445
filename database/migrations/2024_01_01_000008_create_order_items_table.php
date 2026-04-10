<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);

            // Preço snapshot: salva o preço no momento do pedido
            // Protege o histórico se o admin alterar preços depois
            $table->decimal('unit_price', 8, 2)
                ->comment('Preço unitário no momento do pedido');

            $table->text('notes')->nullable()
                ->comment('Observação livre digitada pelo garçom');

            $table->enum('status', ['pending', 'printing', 'delivered'])->default('pending');

            // Número sequencial do pedido para impressão na chapa
            $table->unsignedInteger('print_sequence')->nullable()
                ->comment('Número impresso no cupom da chapa');

            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
