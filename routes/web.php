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



foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/welcome', function () {
            return view('welcome');
        });
        Route::group(['middleware' => ['common']], function () {

            ///////////////guest rout ////////////////////////
            ///
            Route::prefix('register')->name('Central.register.')->group(function () {
                Route::get('/', [RegistrationController::class, 'showForm'])->name('form');
                Route::post('/', [RegistrationController::class, 'store'])->name('storeT');

            });
            Route::get('/payment/choose', [PaymentController::class, 'choose'])->name('payment.choose');
            Route::get('/payment-success', [PaymentController::class, 'success'])->name('payment.success');
            Route::get('/payment-failed', [PaymentController::class, 'failed'])->name('payment.failed');
            Route::post('/payment/checkout', [PaymentController::class, 'paymentProcess'])->name('payment.process');
            Route::post('/payment/checkout2', [PaymentController::class, 'renewOrUpgradeProcess'])->name('payment.subscription');

            /// /////////////////////////////////////////////////
            ///
            Route::middleware('auth:super_users')->group(function () {
                Route::get('/super-admin/dashboard', [HomeController::class, 'index'])->name('super.dashboard');
                Route::controller(HomeController::class)->group(function () {
                    //Route::get('switch-theme/{theme}', 'switchTheme')->name('switchTheme1');
                    Route::get('language_switch/{locale}', [LanguageController::class, 'switchLanguage']);
                });
                // مسارات أخرى للمستخدمين المركزيين

                Route::controller(UserController::class)->group(function () {
                    Route::get('user/profile/{id}', 'profile')->name('user.profile1');
                    Route::put('user/update_profile/{id}', 'profileUpdate')->name('user.profileUpdate1');
                    Route::put('user/changepass/{id}', 'changePassword')->name('user.password1');
                    Route::get('user/genpass', 'generatePassword');
                    Route::post('user/deletebyselection', 'deleteBySelection');
                    Route::get('user/notification', 'notificationUsers')->name('user.notification1');
                    Route::get('user/all', 'allUsers')->name('user.all1');
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
                    Route::get('/create/new', [PackageController::class, 'create'])->name('create');
                    Route::post('/', [PackageController::class, 'store'])->name('store'); // تخزين الباقة
                    Route::get('/{package}/edit', [PackageController::class, 'edit'])->name('edit'); // تعديل الباقة
                    Route::patch('/{package}', [PackageController::class, 'update'])->name('update'); // تحديث الباقة
                    Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy'); // حذف الباقة

                });
            });

            Route::middleware(['auth:super_users', 'can:is-admin'])->group(function () {
                //////////features///////////
                Route::prefix('features')->name('Central.features.')->group(function () {
                    Route::get('/', [FeatureController::class, 'index'])->name('index');
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


            });

        require __DIR__ . '/auth.php'; // مسارات المصادقة المركزية
        });
    });
}
