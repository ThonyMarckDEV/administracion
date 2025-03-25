<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //get the roles
        $adminRole = Role::findById(1);
        $personalRole = Role::findById(2);


        // get the permissions 
        $permissions = Permission::all()->pluck('name')->toArray();

        // create the admin user
        $admin_1 = User::create([
            'name' => 'Jesus Junior',
            'email' => 'junior@gmail.com',
            'username' => 'junior15',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);

        $adminRole->syncPermissions($permissions);
        $admin_1->assignRole($adminRole);
    }
}
