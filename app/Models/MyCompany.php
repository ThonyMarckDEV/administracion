<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MyCompany extends Model
{
    protected $table = 'mycompany';
    use HasFactory;

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