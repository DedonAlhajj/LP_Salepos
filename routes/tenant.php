<?php

declare(strict_types=1);

use App\Http\Controllers\AuthTenant\TenantAuthenticatedSessionController;
use App\Http\Controllers\AuthTenant\TenantRegisteredUserController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\SettingController;
use App\Http\Controllers\Tenant\UserController;
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

        Route::group(['middleware' => 'auth:web'], function() {
            Route::controller(HomeController::class)->group(function () {
                Route::get('home', 'home');
            });
        });
        Route::group(['middleware' => ['auth:web', 'active']], function() {


            Route::controller(HomeController::class)->group(function () {
                Route::get('/dashboard', 'index')->name('tenant.dashboard');
                Route::get('switch-theme/{theme}', 'switchTheme')->name('switchTheme');
            });

            Route::resource('role',RoleController::class);
            Route::controller(RoleController::class)->group(function () {
                Route::get('role/permission/{id}', 'permission')->name('role.permission');
                Route::post('role/set_permission', 'setPermission')->name('role.setPermission');
            });

            Route::controller(UserController::class)->group(function () {
                Route::get('user/profile/{user}', 'profile')->name('user.profile');
                Route::put('user/update_profile/{user}', 'profileUpdate')->name('user.profileUpdate');
                Route::put('user/changepass/{id}', 'changePassword')->name('user.password');
                Route::post('user/deletebyselection', 'deleteBySelection');
                Route::get('user/notification', 'notificationUsers')->name('user.notification');
                Route::get('user/all', 'allUsers')->name('user.all');
                Route::get('user/Trashed', 'indexTrashed')->name('user.Trashed');
                Route::post('users/{id}restore', 'restore')->name('users.restore');

            });
            Route::resource('user', UserController::class);


            Route::controller(CustomerController::class)->group(function () {
                Route::post('importcustomer', 'importCustomer')->name('customer.import');
                Route::get('customer/getDeposit/{id}', 'getDeposit');
                Route::post('customer/add_deposit', 'addDeposit')->name('customer.addDeposit');
                Route::post('customer/update_deposit', 'updateDeposit')->name('customer.updateDeposit');
                Route::post('customer/deleteDeposit', 'deleteDeposit')->name('customer.deleteDeposit');
                Route::post('customer/deletebyselection', 'deleteBySelection');
                Route::get('customer/lims_customer_search', 'limsCustomerSearch')->name('customer.search');
                Route::post('customers/clear-due', 'clearDue')->name('customer.clearDue');
                Route::post('customers/customer-data', 'customerData');
                Route::get('customers/all', 'customersAll')->name('customer.all');
            });
            Route::resource('customer', CustomerController::class)->except('show');


            Route::controller(BillerController::class)->group(function () {
                Route::post('importbiller', 'importBiller')->name('biller.import');
                Route::post('biller/deletebyselection', 'deleteBySelection');
                Route::get('biller/lims_biller_search', 'limsBillerSearch')->name('biller.search');
            });
            Route::resource('biller', BillerController::class);


            Route::controller(SupplierController::class)->group(function () {
                Route::post('importsupplier', 'importSupplier')->name('supplier.import');
                Route::post('supplier/deletebyselection', 'deleteBySelection');
                Route::post('suppliers/clear-due', 'clearDue')->name('supplier.clearDue');
                Route::get('suppliers/all', 'suppliersAll')->name('supplier.all');
            });
            Route::resource('supplier', SupplierController::class)->except('show');


            Route::controller(SettingController::class)->group(function () {
                Route::prefix('setting')->group(function () {
                    Route::get('general_setting', 'generalSetting')->name('setting.general');
                    Route::post('general_setting_store', 'generalSettingStore')->name('setting.generalStore');

                    Route::get('reward-point-setting', 'rewardPointSetting')->name('setting.rewardPoint');
                    Route::post('reward-point-setting_store', 'rewardPointSettingStore')->name('setting.rewardPointStore');

                    Route::get('general_setting/change-theme/{theme}', 'changeTheme');
                    Route::get('mail_setting', 'mailSetting')->name('setting.mail');
                    Route::get('sms_setting', 'smsSetting')->name('setting.sms');
                    Route::get('createsms', 'createSms')->name('setting.createSms');
                    Route::post('sendsms', 'sendSMS')->name('setting.sendSms');
                    Route::get('hrm_setting', 'hrmSetting')->name('setting.hrm');
                    Route::post('hrm_setting_store', 'hrmSettingStore')->name('setting.hrmStore');
                    Route::post('mail_setting_store', 'mailSettingStore')->name('setting.mailStore');
                    Route::post('sms_setting_store', 'smsSettingStore')->name('setting.smsStore');
                    Route::get('pos_setting', 'posSetting')->name('setting.pos');
                    Route::post('pos_setting_store', 'posSettingStore')->name('setting.posStore');
                    Route::get('empty-database', 'emptyDatabase')->name('setting.emptyDatabase');
                });
                Route::get('backup', 'backup')->name('setting.backup');
            });

        });
        require __DIR__ . '/authTenant.php';
    });

});
