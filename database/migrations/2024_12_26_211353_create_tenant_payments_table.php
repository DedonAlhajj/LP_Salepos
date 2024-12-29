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
        Schema::create('tenant_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->float('amount', 8, 2);
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('currency', 3)->default('USD'); // العملة
            $table->enum('paying_method', ['credit_card', 'paypal', 'bank_transfer', 'cash', 'check'])->default('credit_card'); // طريقة الدفع
            $table->string('transaction_id')->nullable(); // معرف المعاملة من بوابة الدفع
            $table->string('reference_number')->nullable(); // الرقم المرجعي للمعاملة
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending'); // حالة الدفع
            $table->dateTime('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_payments');
    }
};
