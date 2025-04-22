<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPlan extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentPlanFactory> */
    use HasFactory;
    
    protected $fillable = [
        'service_id',
        'period_id',
        'payment_type',
        'amount',
        'duration',
        'state',
    ];

    protected $casts = [
        'payment_type' => 'boolean', /** Se trabajara con Anual y mensual */
        'state' => 'boolean',
    ];

    public function service():BelongsTo{
        return $this->belongsTo(Service::class,'service_id', 'id');
    }

    public function period():BelongsTo{
        return $this->belongsTo(Period::class, 'period_id', 'id');
    }
}
