<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\PaymentPlan;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentPlanObserver
{
    /**
     * Handle the PaymentPlan "created" event.
     */
    
    public function created(PaymentPlan $paymentPlan): void
    {
        $periodName = $paymentPlan->period->name;

if (preg_match('/^(\d{4})-(\d)$/', $periodName, $matches)) {
    $year = $matches[1];
    $semester = (int)$matches[2];

    $startMonth = $semester === 1 ? 1 : 7;
    $startDate = Carbon::createFromDate($year, $startMonth, 1);
} else {
    throw new \Exception("Formato de periodo inválido: $periodName");
}
        if (!$paymentPlan->payment_type) {
            for ($i = 0; $i < $paymentPlan->duration; $i++) {
                Payment::create([
                    'customer_id' => $paymentPlan->customer_id,
                    'payment_plan_id' => $paymentPlan->id,
                    'discount_id' => 1,
                    'amount' => $paymentPlan->amount,
                    'payment_date' => $startDate->copy()->addMonths($i),
                    'payment_method' => 'efectivo',
                    'reference' => '--' . strtoupper(Str::random(10)) . '--',
                    'status' => 'pendiente',
                ]);
            }
        } else {
            for ($i = 0; $i < $paymentPlan->duration; $i++) {
                Payment::create([
                    'customer_id' => $paymentPlan->customer_id,
                    'payment_plan_id' => $paymentPlan->id,
                    'discount_id' => 1,
                    'amount' => $paymentPlan->amount,
                    'payment_date' => $startDate->copy()->addYear($i),
                    'payment_method' => 'efectivo',
                    'reference' => '--' . strtoupper(Str::random(10)) . '--',
                    'status' => 'pendiente',
                ]);
            }
        }
    }

    /**
     * Handle the PaymentPlan "updated" event.
     */
    public function updated(PaymentPlan $paymentPlan): void
    {
        // Solo si el monto fue modificado
    if ($paymentPlan->isDirty('amount')) {
        // Solo actualizar los pagos que están pendientes
        $paymentPlan->payments()
            ->where('status', 'pendiente')
            ->update([
                'amount' => $paymentPlan->amount,
            ]);
    }
    }

    /**
     * Handle the PaymentPlan "deleted" event.
     */
    public function deleted(PaymentPlan $paymentPlan): void
    {
        //
    }

    /**
     * Handle the PaymentPlan "restored" event.
     */
    public function restored(PaymentPlan $paymentPlan): void
    {
        //
    }

    /**
     * Handle the PaymentPlan "force deleted" event.
     */
    public function forceDeleted(PaymentPlan $paymentPlan): void
    {
        //
    }
}
