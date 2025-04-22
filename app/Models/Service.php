<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services'; // Nombre de la tabla

    protected $fillable = [
        'name',
        'cost',
        'ini_date',
        'state',
    ];

    protected $casts = [
        'cost' => 'decimal:2', // Asegura que el costo tenga 2 decimales
        'ini_date' => 'date', // Convierte automáticamente a Carbon
        'state' => 'boolean'
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

    public function paymentPlans():HasMany{
        return $this->hasMany(PaymentPlan::class);
    }
}
