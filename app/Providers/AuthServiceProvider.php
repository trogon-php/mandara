<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Example: \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Super Admin bypass (optional)
        Gate::before(function (User $user, string $ability) {
            if ($user->role_id === Role::ADMIN) {
                return true;
            }
        });

        // Dynamically register all grouped permissions
        foreach (config('permissions') as $module => $actions) {
            foreach ($actions as $action => $allowedRoles) {
                $ability = "$module/$action"; // e.g. "courses/create"

                Gate::define($ability, function (User $user) use ($allowedRoles) {
                    return in_array($user->role_id, $allowedRoles);
                });
            }
        }
    }
}
