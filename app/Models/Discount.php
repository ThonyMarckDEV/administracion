<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;
    //protected $table = 'discounts'; // Nombre de la tabla
    protected $fillable = [
        'description',
        'percentage',
        'state',
    ];


    public function payments():HasMany{
        return $this->hasMany(Payment::class, 'discount_id', 'id');
    }
}