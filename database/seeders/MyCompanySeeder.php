<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MyCompany;
use Illuminate\Support\Facades\DB;


class MyCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Borra todos los registros existentes
        DB::table('mycompany')->truncate();

        MyCompany::factory()->create([
            'ruc' => '20000000001',
            'razon_social' => 'SOLUCIONES EN INGENIERIA T&J',
            'nombre_comercial' => 'MiComercial',
            'ubigueo' => '150101',
            'departamento' => 'Piura',
            'provincia' => 'Piura',
            'distrito' => 'Piura',
            'urbanizacion' => null,
            'direccion' => 'Urb. Sol de Piura Los Portales Mz B12 Lote 1',
            'cod_local' => '0000',
        ]);
    }
}
