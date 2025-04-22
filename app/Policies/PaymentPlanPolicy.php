<?php

namespace App\Policies;

use App\Models\PaymentPlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver plan_pago');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PaymentPlan $paymentPlan): bool
    {
        return $user->can('ver plan_pago');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear plan_pago');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PaymentPlan $paymentPlan): bool
    {
        return $user->can('editar plan_pago');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PaymentPlan $paymentPlan): bool
    {
        return $user->can('eliminar plan_pago');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PaymentPlan $paymentPlan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PaymentPlan $paymentPlan): bool
    {
        return false;
    }
}
