<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyCompany extends Model
{
    protected $table = 'mycompany';

    protected $fillable = [
        'ruc',
        'razon_social',
        'nombre_comercial',
        'ubigueo',
        'departamento',
        'provincia',
        'distrito',
        'urbanizacion',
        'direccion',
        'cod_local',
    ];
}