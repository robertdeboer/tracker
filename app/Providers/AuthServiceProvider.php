<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /**
         * Add the ability to use super admins
         */
        Gate::before(
            function (User $user, $ability) {
                return $user->hasRole(Roles::SUPER_ADMIN) ? true : null;
            }
        );
    }
}
