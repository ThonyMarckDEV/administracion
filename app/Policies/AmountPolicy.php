<?php

namespace App\Policies;

use App\Models\Amount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AmountPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver amounts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Amount $amount): bool
    {
        return $user->can('ver amounts');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('crear amounts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Amount $amount): bool
    {
        return $user->can('editar amounts');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Amount $amount): bool
    {
        return $user->can('eliminar amounts');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Amount $amount): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Amount $amount): bool
    {
        return false;
    }
}
