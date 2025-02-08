<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->string('entity_type');  // نوع الكيان المرتبط (مثل `customer`, `product`)
            $table->string('name');                     // اسم الحقل المخصص
            $table->enum('type', ['text', 'number', 'select', 'checkbox', 'date', 'boolean']); // نوع الحقل
            $table->text('default_value')->nullable();  // القيمة الافتراضية للحقل
            $table->text('option_value')->nullable();   // القيم الممكنة في حالة `select`
            $table->text('grid_value')->nullable();     // قيم الشبكة إذا كانت موجودة
            $table->boolean('is_table')->default(false); // هل الحقل يظهر في جدول؟
            $table->boolean('is_invoice')->default(false); // هل الحقل يظهر في الفواتير؟
            $table->boolean('is_required')->default(false);  // هل الحقل إجباري؟
            $table->boolean('is_admin')->default(false);     // هل الحقل للمسؤول فقط؟
            $table->boolean('is_disable')->default(false);   // هل الحقل معطل؟
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade'); // معرف المستأجر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            //
        });
    }
};
