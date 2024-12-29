<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('super_user_id')->constrained('super_users')->onDelete('cascade'); // مرجع إلى جدول super_users
            $table->string('name'); // اسم الشركة أو المتجر
            $table->foreignId('package_id')->constrained('packages'); // مرجع إلى جدول packages
            $table->timestamp('subscription_start')->nullable(); // تاريخ بدء الاشتراك
            $table->timestamp('subscription_end')->nullable(); // تاريخ انتهاء الاشتراك
            $table->boolean('is_active')->default(true); // حالة المستأجر
            $table->timestamp('trial_end')->nullable(); // تاريخ انتهاء الفترة التجرئيبية
            $table->timestamps();
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
