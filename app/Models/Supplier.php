<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;
    protected $table = 'suppliers'; // Nombre de la tabla
    protected $fillable = [
        'name',
        'ruc',
        'email',
        'address',
        'state',
    ];

    public function amounts(): HasMany{
        return $this->hasMany(Amount::class);
    }

    public function Categories(): BelongsToMany{
        return $this->belongsToMany(Category::class, 'category_supplier')
            ->using(Amount::class)
            ->withPivot('description', 'amount')
            ->withTimestamps();
    }
}
