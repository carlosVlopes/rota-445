<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('tables')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()
                ->comment('Garçom que abriu a comanda');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('total', 10, 2)->default(0)
                ->comment('Preenchido automaticamente ao fechar');
            $table->string('payment_method')->nullable()
                ->comment('Ex: dinheiro, cartão, pix');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            // Uma mesa só pode ter uma comanda aberta por vez
            $table->index(['table_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
