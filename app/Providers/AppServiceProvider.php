<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Observers\PaymentObserver;
use App\Observers\PaymentPlanObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // User::observe(UserObserver::class);
        PaymentPlan::observe(PaymentPlanObserver::class);
        Payment::observe(PaymentObserver::class);

        if (env('APP_ENV') === 'local') {
          URL::forceScheme('https');
        }
    }
}
