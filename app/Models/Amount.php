<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amount extends Model
{
    protected $table = 'category_supplier';
    protected $fillable = [
        'supplier_id',
        'category_id',
        'description',
        'amount',
        'date_init',
    ];

    public function suppliers(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id','id');
    }

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id','id');
    }
}
