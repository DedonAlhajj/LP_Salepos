<?php

use App\Http\Controllers\Central\FeatureController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\PackageController;
use App\Http\Controllers\Central\PaymentController;
use App\Http\Controllers\Central\RegistrationController;
use App\Http\Controllers\Central\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        Route::group(['middleware' => ['common']], function () {

            ///////////////guest rout ////////////////////////
            ///
            Route::prefix('register')->name('Central.register.')->group(function () {
                Route::get('/', [RegistrationController::class, 'showForm'])->name('form');
                Route::post('/', [RegistrationController::class, 'store'])->name('storeT');

            });
            Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('Central.payment.form');

            /// /////////////////////////////////////////////////
            ///
            Route::middleware('auth:super_users')->group(function () {
                Route::get('/super-admin/dashboard', [HomeController::class, 'index'])->name('super.dashboard');
                Route::controller(HomeController::class)->group(function () {
                    Route::get('switch-theme/{theme}', 'switchTheme')->name('switchTheme');
                    Route::get('language_switch/{locale}', [LanguageController::class, 'switchLanguage']);
                });
                // مسارات أخرى للمستخدمين المركزيين

                Route::controller(UserController::class)->group(function () {
                    Route::get('user/profile/{id}', 'profile')->name('user.profile');
                    Route::put('user/update_profile/{id}', 'profileUpdate')->name('user.profileUpdate');
                    Route::put('user/changepass/{id}', 'changePassword')->name('user.password');
                    Route::get('user/genpass', 'generatePassword');
                    Route::post('user/deletebyselection', 'deleteBySelection');
                    Route::get('user/notification', 'notificationUsers')->name('user.notification');
                    Route::get('user/all', 'allUsers')->name('user.all');
                });
                Route::resource('user', UserController::class);

            });

            // مسارات الحزم
            Route::prefix('packages')->name('Central.packages.')->group(function () {

                // عرض قائمة الحزم وتفاصيل الحزمة (متاحة لأي مستخدم)
                Route::middleware('throttle:60,1')->group(function () {
                    Route::get('/', [PackageController::class, 'index'])->name('index');
                    Route::get('/{package}', [PackageController::class, 'show'])->name('show');
                });

                // مسارات تتطلب تسجيل الدخول كـ "admin" فقط
                Route::middleware(['auth:super_users', 'can:is-admin'])->group(function () {
                    Route::get('/create', [PackageController::class, 'create'])->name('create'); // إنشاء باقة جديدة
                    Route::post('/', [PackageController::class, 'store'])->name('store'); // تخزين الباقة
                    Route::get('/{package}/edit', [PackageController::class, 'edit'])->name('edit'); // تعديل الباقة
                    Route::patch('/{package}', [PackageController::class, 'update'])->name('update'); // تحديث الباقة
                    Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy'); // حذف الباقة

                });
            });

            Route::middleware(['auth:super_users', 'can:is-admin'])->group(function () {
                //////////features///////////
                Route::prefix('features')->name('Central.features.')->group(function () {

                    Route::get('/create', [FeatureController::class, 'create'])->name('create');
                    Route::post('/', [FeatureController::class, 'store'])->name('store');
                    Route::get('/{feature}/edit', [FeatureController::class, 'edit'])->name('edit');
                    Route::patch('/{feature}', [FeatureController::class, 'update'])->name('update');
                    Route::delete('/{feature}', [FeatureController::class, 'destroy'])->name('destroy');
                });


                //////////////pay/////////
                Route::prefix('payments')->name('Central.payments.')->group(function () {

                    Route::get('/', [PaymentController::class, 'index'])->name('index');
                    Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
                    Route::post('/{id}/update-status', [PaymentController::class, 'updateStatus'])->name('update-status');

                });





                Route::get('language_switch/{locale}', [LanguageController::class, 'switchLanguage']);

                Route::resource('role', RoleController::class);
                Route::controller(RoleController::class)->group(function () {
                    Route::get('role/permission/{id}', 'permission')->name('role.permission');
                    Route::post('role/set_permission', 'setPermission')->name('role.setPermission');
                });

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

        require __DIR__ . '/auth.php'; // مسارات المصادقة المركزية
        });
    });
}
