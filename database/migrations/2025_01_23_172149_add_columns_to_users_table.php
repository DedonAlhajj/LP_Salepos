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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('password');
            $table->string('company_name')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('company_name');
            $table->boolean('is_deleted')->default(false)->after('is_active');
            $table->foreignId('biller_id')->constrained('billers')->cascadeOnDelete()->after('is_deleted');
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete()->after('biller_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
