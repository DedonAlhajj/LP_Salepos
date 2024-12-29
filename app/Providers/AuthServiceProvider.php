<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\SuperUser;
use App\Policies\AdminPolicy;
use App\Policies\CheckUserPolicy;
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
       // SuperUser::class => AdminPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        {
            $this->registerPolicies();

            // تسجيل السياسة العامة
            Gate::define('is-admin', [AdminPolicy::class, 'isAdmin']);
            Gate::define('is-user', [AdminPolicy::class, 'isUser']);
        }
    }
}
