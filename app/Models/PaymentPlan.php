<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPlan extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentPlanFactory> */
    use HasFactory;
    
    protected $fillable = [
        'service_id',
        'customer_id',
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

    public function customer():BelongsTo{
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function period():BelongsTo{
        return $this->belongsTo(Period::class, 'period_id', 'id');
    }

    public function payments(): HasMany{
        return $this->hasMany(Payment::class, 'payment_plan_id', 'id');
    }

    
}
