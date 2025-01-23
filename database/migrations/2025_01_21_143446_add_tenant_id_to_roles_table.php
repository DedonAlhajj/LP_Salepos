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
        Schema::table('roles', function (Blueprint $table) {
            // إضافة عمود tenant_id
            $table->unsignedBigInteger('tenant_id')->after('id');

            // إضافة فهرس فريد يجمع بين tenant_id و name و guard_name
            $table->unique(['tenant_id', 'name', 'guard_name'], 'unique_roles_tenant');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('unique_roles_tenant');
            $table->dropColumn('tenant_id');
        });
    }


};
