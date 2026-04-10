<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'table_id'       => Table::factory(),
            'user_id'        => User::factory()->waiter(),
            'status'         => 'open',
            'total'          => 0,
            'payment_method' => null,
            'opened_at'      => now(),
            'closed_at'      => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status'         => 'closed',
            'total'          => fake()->randomFloat(2, 30, 300),
            'payment_method' => fake()->randomElement(['dinheiro', 'cartão', 'pix']),
            'closed_at'      => now(),
        ]);
    }
}
