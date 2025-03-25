<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create permissions

        // model users
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);
        Permission::create(['name' => 'ver usuarios']);
    
        // model clientes
        Permission::create(['name' => 'crear clientes']);
        Permission::create(['name' => 'editar clientes']);
        Permission::create(['name' => 'eliminar clientes']);
        Permission::create(['name' => 'ver clientes']);

        // model proveedores
        Permission::create(['name' => 'crear proveedores']);
        Permission::create(['name' => 'editar proveedores']);
        Permission::create(['name' => 'eliminar proveedores']);
        Permission::create(['name' => 'ver proveedores']);

        // model servicios
        Permission::create(['name' => 'crear servicios']);
        Permission::create(['name' => 'editar servicios']);
        Permission::create(['name' => 'eliminar servicios']);
        Permission::create(['name' => 'ver servicios']);

        // model descuentos
        Permission::create(['name' => 'crear descuentos']);
        Permission::create(['name' => 'editar descuentos']);
        Permission::create(['name' => 'eliminar descuentos']);
        Permission::create(['name' => 'ver descuentos']);
    }
}
