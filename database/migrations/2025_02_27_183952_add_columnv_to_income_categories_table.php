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
        Schema::table('income_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->after('id')->default(45);
            $table->softDeletes()->after('tenant_id');
            $table->unique(['tenant_id', 'code', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_categories', function (Blueprint $table) {
            //
        });
    }
};
