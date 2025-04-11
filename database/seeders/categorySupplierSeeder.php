<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class categorySupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('category_supplier')->insert([
             'category_id' => 1,
             'supplier_id' => 1,
             'description' => 'Proveedor de categoria 1',
             'amount' => 100.00,
             'date_init' => now(),
         ]);
         DB::table('category_supplier')->insert([
             'category_id' => 2,
             'supplier_id' => 2,
             'description' => 'Proveedor de categoria 2',
             'amount' => 200.00,
             'date_init' => now(),
         ]);
         DB::table('category_supplier')->insert([
             'category_id' => 3,
             'supplier_id' => 3,
             'description' => 'Proveedor de categoria 3',
             'amount' => 350.00,
             'date_init' => now(),
         ]);
         DB::table('category_supplier')->insert([
             'category_id' => 2,
             'supplier_id' => 1,
             'description' => 'Proveedor de categoria 2',
             'amount' => 500.00,
             'date_init' => now(),
         ]);
         DB::table('category_supplier')->insert([
            'category_id' => 3,
            'supplier_id' => 1,
            'description' => 'Proveedor de categoria 3',
            'amount' => 400.00,
            'date_init' => now(),
        ]);
        DB::table('category_supplier')->insert([
            'category_id' => 1,
            'supplier_id' => 2,
            'description' => 'Proveedor de categoria 1',
            'amount' => 300.00,
            'date_init' => now(),
        ]);
        DB::table('category_supplier')->insert([
            'category_id' => 3,
            'supplier_id' => 2,
            'description' => 'Proveedor de categoria 3',
            'amount' => 200.00,
            'date_init' => now(),
        ]);
    }
}
