<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;
    protected $table = 'categories'; // Nombre de la tabla
    protected $fillable = [
        'name',
        'status',
    ];

    public function suppliers(): BelongsToMany{
        return $this->belongsToMany(Supplier::class, 'category_supplier')
            ->using(Amount::class)
            ->withPivot('description', 'amount')
            ->withTimestamps();
    }
}