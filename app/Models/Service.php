<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services'; // Nombre de la tabla

    protected $fillable = [
        'name',
        'costo',
        'fecha_inicio',
        'estado',
    ];

    protected $casts = [
        'costo' => 'decimal:2', // Asegura que el costo tenga 2 decimales
        'fecha_inicio' => 'date', // Convierte automáticamente a Carbon
    ];

    /**
     * Mutador para formatear la fecha de inicio.
     */
    protected function fechaInicio(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-m-d'),
        );
    }
}
