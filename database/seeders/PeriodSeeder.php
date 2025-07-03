<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Period::create([
            'name' => '2025-1',
            'description' => 'Enero - Junio 2025',
            'state' => 1,
        ]);
        Period::create([
            'name' => '2025-2',
            'description' => 'Julio - Diciembre 2025',
            'state' => 1,
        ]);
    }
}
