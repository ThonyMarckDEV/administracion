<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amount extends Model
{
    protected $table = 'category_supplier';
    protected $fillable = [
        'category_id',
        'supplier_id',
        'description',
        'amount',
        'date_init',
        'serie_assigned',
        'correlative_assigned',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date_init' => 'datetime',
        'serie_assigned' => 'string',
        'correlative_assigned' => 'integer',
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
