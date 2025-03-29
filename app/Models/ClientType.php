<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    /** @use HasFactory<\Database\Factories\ClientTypeFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
    ];
}
