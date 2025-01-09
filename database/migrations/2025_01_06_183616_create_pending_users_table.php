<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pending_users', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('store_name');
            $table->string('domain')->unique();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('operation_type');
            $table->enum('status',['pending', 'payment_initiated', 'failed', 'paid']);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['domain', 'expires_at']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_users');
    }
};
