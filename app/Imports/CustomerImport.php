<?php

namespace App\Imports;

use App\Models\ClientType;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validación mínima para evitar errores
            if (
                !isset($row['nombre'], $row['codigo'], $row['email'], $row['tipo_de_cliente'], $row['estado'])
                || empty($row['nombre']) 
                || empty($row['codigo']) 
                || empty($row['email']) 
                || empty($row['tipo_de_cliente']) 
                || empty($row['estado'])
            ) {
                Log::warning('Fila ignorada por datos incompletos', $row->toArray());
                continue;
            }

            // Buscar ClientType por ID (ajusta si usas nombre)
            $clientType = ClientType::find($row['tipo_de_cliente']);
            if (!$clientType) {
                Log::warning('Tipo de cliente no encontrado', ['tipo_de_cliente' => $row['tipo_de_cliente']]);
                continue;
            }

            // Interpretar estado
            $estado = strtolower(trim($row['estado']));
            $state = in_array($estado, ['true', '1', 'si', 'sí', 'activo'], true);

            try {
                Customer::create([
                    'name'           => $row['nombre'],
                    'codigo'         => $row['codigo'],
                    'email'          => $row['email'],
                    'dni'            => !empty($row['dni']) ? $row['dni'] : null,
                    'ruc'            => !empty($row['ruc']) ? $row['ruc'] : null,
                    'client_type_id' => $clientType->id,
                    'state'          => $state,
                ]);
            } catch (\Throwable $e) {
                Log::error('Error al crear cliente', [
                    'fila' => $row->toArray(),
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}