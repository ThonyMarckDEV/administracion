<?php

namespace App\Observers;

use App\Models\Record;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Create a record when a user is created
        $userLogin = Auth::user();
        Record::create([
            'description' => "usuario {$user->name} fue creado por {$userLogin->name}",
        ]);

        Log::info("User {$user->name} created");
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $userLogin = Auth::user();
        Record::create([
            'description' => "Usuario {$user->name} fue actualizado por {$userLogin->name}",
        ]);
        Log::info("User {$user->name} updated");
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Record::create([
            'description' => "User {$user->name} deleted",
        ]);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
