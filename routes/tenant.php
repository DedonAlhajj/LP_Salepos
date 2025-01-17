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
/*
Route::middleware(['web', InitializeTenancyByDomain::class])->get('/test-tenant', function () {
    return response()->json([
        'tenant' => tenant() ? tenant()->toArray() : 'No tenant found',
    ]);
});*/
/*logger('Session ID: ' . session()->getId());
        logger('Current domain: ' . $request->getHost());
        $response = $this->initializeTenancy($request, $next, $request->getHost());
        logger('Tenant after initialization: ' . tenant('id'));
        return $response;
        logger('Request passed InitializeTenancyByDomain middleware.');*/
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/check-route', function () {
        return 'Routes are loaded correctly';
    });
    Route::get('/dash', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

    Route::name('tenant.')->group(function () {
        Route::get('Tenant-login1', [TenantAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('tenant-login', function () {
            logger('Route tenant.login.store is being accessed.');
            return app(TenantAuthenticatedSessionController::class)->store(request());
        })->name('login.store');    });
});
