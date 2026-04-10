<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // 20 mesas numeradas — ajuste conforme o estabelecimento
        for ($i = 1; $i <= 20; $i++) {
            Table::firstOrCreate(
                ['number' => str_pad($i, 2, '0', STR_PAD_LEFT)],
                ['status' => 'free']
            );
        }
    }
}
