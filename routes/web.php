<?php

use App\Http\Controllers\Central\FeatureController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\PackageController;
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

        Route::middleware('auth:super_users')->group(function () {
            Route::get('/super-admin/dashboard',[HomeController::class, 'index'])->name('super.dashboard');
            // مسارات أخرى للمستخدمين المركزيين
        });


        Route::middleware('auth:super_users')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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


        });

        require __DIR__ . '/auth.php'; // مسارات المصادقة المركزية
    });
}
