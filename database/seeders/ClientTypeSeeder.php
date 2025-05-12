<?php

namespace Database\Seeders;

use App\Models\ClientType;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ClientType::factory()->count(50)->create();

        ClientType::create([
            'name' => 'Persona',
            'state' => true,
        ]);
        ClientType::create([
            'name' => 'Empresa',
            'state' => true,
        ]);

        //  default customer 
        Customer::create([
            'name' => 'Cliente por default',
            'codigo' => '00000000000',
            'client_type_id' => 1,
            'state' => true,
        ]);
    }
}
