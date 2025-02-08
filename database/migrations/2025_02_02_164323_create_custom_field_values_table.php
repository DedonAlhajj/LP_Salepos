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
        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_field_id')->constrained()->onDelete('cascade'); // معرف الحقل المخصص
            $table->morphs('entity'); // الحقل مرتبط بأي كيان (مثل Customer, Product)
            $table->text('value');     // القيمة الفعلية للحقل
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade'); // معرف المستأجر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_field_values');
    }
};
