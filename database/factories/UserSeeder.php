<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::firstOrCreate(
            ['email' => 'admin@espetaria.com'],
            [
                'name'     => 'Administrador',
                'pin'      => null,
                'role'     => 'admin',
                'active'   => true,
                'password' => Hash::make('password'),
            ]
        );

        // Garçons fixos com PIN conhecido para testes
        $waiters = [
            ['name' => 'Carlos',  'pin' => '1111'],
            ['name' => 'Ana',     'pin' => '2222'],
            ['name' => 'Roberto', 'pin' => '3333'],
        ];

        foreach ($waiters as $waiter) {
            User::firstOrCreate(
                ['pin' => $waiter['pin'], 'role' => 'waiter'],
                [
                    'name'     => $waiter['name'],
                    'email'    => null,
                    'active'   => true,
                    'password' => null,
                ]
            );
        }

        // Caixa fixo
        User::firstOrCreate(
            ['pin' => '9999', 'role' => 'cashier'],
            [
                'name'     => 'Caixa',
                'email'    => null,
                'active'   => true,
                'password' => null,
            ]
        );
    }
}
