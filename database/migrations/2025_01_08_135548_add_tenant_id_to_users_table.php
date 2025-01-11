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
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل tenant_id
            $table->unsignedBigInteger('tenant_id')->after('id');

            // إضافة الفهرس الفريد على tenant_id و email
            $table->unique(['tenant_id', 'email'], 'unique_tenant_email');

            // حذف الفهرس القديم على email
            $table->dropUnique('users_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إعادة الفهرس الفريد على email
            $table->unique('email');

            // حذف الفهرس الفريد على tenant_id و email
            $table->dropUnique('unique_tenant_email');

            // حذف حقل tenant_id
            $table->dropColumn('tenant_id');
        });
    }
};
