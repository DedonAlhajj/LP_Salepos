<?php

namespace App\Providers;

use App\Interfaces\PaymentGatewayInterface;
use App\Services\Payment\MyFatoorahPaymentService;
use Illuminate\Support\Facades\Config;
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
        // فرض HTTPS في بيئة الإنتاج
       /* if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }*/

       // $centralDomains = config('tenancy.central_domains');



        Paginator::useBootstrap();


    }






}
