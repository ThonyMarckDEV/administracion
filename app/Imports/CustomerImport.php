<?php

namespace App\Imports;

use App\Models\ClientType;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row){
            $clientType = ClientType::where('name', $row['tipo_de_cliente'])->first();
            Customer::create([
                'name' => $row['nombre'],
                'codigo' => $row['codigo'],
                'state' => strtolower($row['estado']) === 'activo' ? true : false,
                'client_type_id' => $clientType->id,
            ]);
        }
    }
}
