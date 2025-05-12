<?php

namespace App\Imports;

use App\Models\Amount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AmountImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Validar que los campos necesarios existan y no estén vacíos
                if (
                    empty($row['categoria']) ||
                    empty($row['proveedor']) ||
                    empty($row['ruc']) ||
                    empty($row['descripcion']) ||
                    empty($row['monto']) ||
                    empty($row['fecha_inicio'])
                ) {
                    continue; // Saltar fila incompleta
                }

                /* Normalizar estado
                $estado = strtolower(trim($row['estado']));
                if (!in_array($estado, ['activo', 'inactivo', 'pendiente'])) {
                    continue; // Saltar si el estado no es válido
                }*/

                // Formatear la fecha
                $rawDate = $row['fecha_inicio'];
                if (is_numeric($rawDate)) {
                    // Fecha como número serial de Excel
                $fecha = Carbon::instance(Date::excelToDateTimeObject($rawDate))->format('Y-m-d');
                } else {
                    // Fecha como texto
                    $fecha = Carbon::parse(trim($rawDate))->format('Y-m-d');
                }

                // Crear los egresos
                Amount::create([
                    'category_id' => $row['categoria'],  // Asegúrate de que estos coincidan
                    'supplier_id' => $row['proveedor'],  // Asegúrate de que estos coincidan
                    'ruc' => $row['ruc'],
                    'description' => $row['descripcion'],
                    'amount' => (float) $row['monto'],
                    'date_init' => $fecha,
                ]);

            } catch (\Exception $e) {
                // Saltar fila si ocurre error (puedes loguear si quieres)
                continue;
            }
        }
    }
}
