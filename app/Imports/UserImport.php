<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row){
            User::create([
                'name' => $row['nombre'],
                'email' => $row['email'],
                'username' => $row['usuario'],
                'password' => bcrypt($row['contrasena']),
                'status' => strtolower($row['estado']) === 'activo' ? true : false,
            ]);
        }
    }
}
