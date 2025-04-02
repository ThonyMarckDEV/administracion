<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'codigo',
        'client_type_id',
        'state',
    ];

    protected $casts = [
        'state' => 'boolean',
    ];
}
