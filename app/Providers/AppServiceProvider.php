<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends AuthServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Permission::class => PermissionPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
    ];

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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        $this->registerPolicies();

        Gate::define('viewApiDocs', function (?User $user): bool {
            return (bool) $user?->is_super_admin;
        });
    }
}
