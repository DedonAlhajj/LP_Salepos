<?php

declare(strict_types=1);

use App\Http\Controllers\AuthTenant\TenantAuthenticatedSessionController;
use App\Http\Controllers\AuthTenant\TenantRegisteredUserController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\RoleController;
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
    Route::get('/', function () {
        return 'Welcome to your store! Tenant ID: ' . tenant('id');
    });

    Route::group(['middleware' => ['common']], function () {

        Route::middleware('auth:web')->group(function () {

            //Route::get('/dashboard', [HomeController::class, 'index'])->name('tenant.dashboard');

            Route::controller(HomeController::class)->group(function () {
                Route::get('/dashboard', 'index')->name('tenant.dashboard');
                Route::get('switch-theme/{theme}', 'switchTheme')->name('switchTheme');
            });

            Route::resource('role',RoleController::class);
            Route::controller(RoleController::class)->group(function () {
                Route::get('role/permission/{id}', 'permission')->name('role.permission');
                Route::post('role/set_permission', 'setPermission')->name('role.setPermission');
            });

        });
        require __DIR__ . '/authTenant.php';
    });

});
