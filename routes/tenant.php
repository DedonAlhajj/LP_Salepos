<?php

declare(strict_types=1);

use App\Http\Controllers\AuthTenant\TenantAuthenticatedSessionController;
use App\Http\Controllers\AuthTenant\TenantRegisteredUserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/dash', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', function () {
            return view('tenant.dashboard'); // قم بإنشاء ملف العرض الخاص بلوحة التحكم
        })->name('tenant.dashboard');

    });
    require __DIR__ . '/authTenant.php';
});
