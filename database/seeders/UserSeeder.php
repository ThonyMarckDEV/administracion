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

        $admin_2 = User::create([
            'name' => 'Bryan Rebaza',
            'email' => 'Bryanre@gmail.com',
            'username' => 'Bryan11',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);

         $admin_3 = User::create([
            'name' => 'Pablo Isaac Lupu Garcia',
            'email' => 'pablolupu2020@gmail.com',
            'username' => 'pablolupu',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);
        $admin_4 = User::create([
            'name' => 'Jesus Junior',
            'email' => 'junior4@gmail.com',
            'username' => 'jesus17',
            'password' => Hash::make('12345678'),
            'status' => 0,
        ]);
        $admin_5 = User::create([
            'name' => 'Jesus Junior',
            'email' => 'junior5@gmail.com',
            'username' => 'jesus18',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);
        $admin_6 = User::create([
            'name' => 'Jesus Junior',
            'email' => 'junior6@gmail.com',
            'username' => 'jesus19',
            'password' => Hash::make('12345678'),
            'status' => 0,
        ]);
        $admin_7 = User::create([
            'name' => 'Jesus Junior',
            'email' => 'junior7@gmail.com',
            'username' => 'jesus20',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);

        $admin_8 = User::create([
            'name' => 'Gustavo Siancas',
            'email' => 'gustavo@gmail.com',
            'username' => 'gustavo25',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);
        $admin_9 = User::create([
            'name' => 'Anthony Marck',
            'email' => 'thonymarck385213xd@gmail.com',
            'username' => 'thonymarck',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);
         $admin_10 = User::create([
            'name' => 'Karin Hair',
            'email' => 'kayisanta5@gmail.com',
            'username' => 'kchozo27',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);

        $admin_11 = User::create([
            'name' => 'Renato Moran',
            'email' => 'renatomoran27@gmail.com',
            'username' => 'renato27',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);

        $admin_12 = User::create([
            'name' => 'Rolando Payano',
            'email' => 'payano@gmail.com',
            'username' => 'rpayanoc1',
            'password' => Hash::make('12345678'),
            'status' => 1,
        ]);
      
  
        $adminRole->syncPermissions($permissions);
        $admin_1->assignRole($adminRole);
        $admin_2->assignRole($adminRole);
        $admin_3->assignRole($adminRole);
        $admin_4->assignRole($adminRole);
        $admin_5->assignRole($adminRole);
        $admin_6->assignRole($adminRole);
        $admin_7->assignRole($adminRole);
        $admin_8->assignRole($adminRole);
        $admin_9->assignRole($adminRole);
        $admin_10->assignRole($adminRole);
        $admin_11->assignRole($adminRole);
        $admin_12->assignRole($adminRole);
    }
}
