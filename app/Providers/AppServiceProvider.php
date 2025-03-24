<?php

namespace App\Providers;

use App\Interfaces\PaymentGatewayInterface;
use App\Services\Payment\MyFatoorahPaymentService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, MyFatoorahPaymentService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // التأكد من أن الجداول موجودة لتجنب أي خطأ عند تشغيل `migrate`
        if (Schema::hasTable('general_settings')) {
            $timezone = Cache::rememberForever('app_timezone', function () {
                return \App\Models\GeneralSetting::query()->value('timezone') ?? 'UTC';
            });

            Config::set('app.timezone', $timezone);
            date_default_timezone_set($timezone);
        }

        // فرض HTTPS في بيئة الإنتاج
        if (app()->environment('local')) {
            URL::forceScheme('https');
        }

       // $centralDomains = config('tenancy.central_domains');



        Paginator::useBootstrap();


    }






}
