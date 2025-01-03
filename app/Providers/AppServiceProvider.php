<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

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

        $centralDomains = config('tenancy.central_domains');

        if (in_array(request()->getHost(), $centralDomains)) {
            // جلسات المستخدمين المركزيين
            Config::set('session.domain', env('SESSION_DOMAIN_CENTRAL', null));
        } else {
            // جلسات المستأجرين
            Config::set('session.domain', env('SESSION_DOMAIN_TENANTS', null));
        }
    }
}
