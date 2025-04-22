<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'payment_plan_id',
        'discount_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'status',
    ];
    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function customer(): BelongsTo{
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function paymentPlan(): BelongsTo{
        return $this->belongsTo(PaymentPlan::class,'payment_plan_id', 'id');
    }

    public function discount(): BelongsTo{
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    // HASMANYTHROUGH
    public function paymentPlanService(): HasManyThrough{
        return $this->hasManyThrough(Service::class, PaymentPlan::class, 'id', 'id', 'payment_plan_id', 'service_id');
    }
}
