<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'document_type',
        'serie_assigned',
        'correlative_assigned',
        'sunat',
    ];

    protected $casts = [
        'document_type' => 'string', // B or F
        'sunat' => 'string', // anulado or enviado
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}