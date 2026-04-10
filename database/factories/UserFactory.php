<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'pin'      => (string) fake()->numerify('####'),
            'role'     => fake()->randomElement(['waiter', 'cashier']),
            'active'   => true,
            'password' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'name'     => 'Administrador',
            'email'    => 'admin@espetaria.com',
            'pin'      => null,
            'role'     => 'admin',
            'password' => Hash::make('password'),
        ]);
    }

    public function waiter(): static
    {
        return $this->state(fn () => [
            'role' => 'waiter',
            'pin'  => (string) fake()->numerify('####'),
        ]);
    }

    public function cashier(): static
    {
        return $this->state(fn () => [
            'role' => 'cashier',
            'pin'  => (string) fake()->numerify('####'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
