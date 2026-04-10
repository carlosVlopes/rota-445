<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('pin', 6)->nullable()->comment('PIN numérico para acesso rápido no celular');
            $table->enum('role', ['admin', 'waiter', 'cashier']);
            $table->boolean('active')->default(true);
            $table->string('password')->nullable()->comment('Apenas para admin');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
