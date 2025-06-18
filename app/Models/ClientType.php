<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientType extends Model
{
    /** @use HasFactory<\Database\Factories\ClientTypeFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
    ]; 
    protected $casts = [
        'state' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
 
    public function Customers():HasMany{
        return $this->hasMany(Customer::class); 
    }
}
