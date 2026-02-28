<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class PulseServiceProvider extends \Laravel\Pulse\PulseServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('super_admin');
        });
    }
}
