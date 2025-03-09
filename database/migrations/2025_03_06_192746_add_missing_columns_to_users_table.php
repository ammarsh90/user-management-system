<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة username إذا لم يكن موجودًا
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->after('id')->nullable()->unique();
            }
            
            // إضافة باقي الأعمدة إذا لم تكن موجودة
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'reseller', 'user'])->default('user')->after('email');
            }
            
            if (!Schema::hasColumn('users', 'credits')) {
                $table->decimal('credits', 10, 2)->default(0.00)->after('role');
            }
            
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive', 'banned'])->default('active')->after('credits');
            }
            
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login');
            }
            
            if (!Schema::hasColumn('users', 'hwid')) {
                $table->string('hwid')->nullable()->after('last_login_ip');
            }
            
            if (!Schema::hasColumn('users', 'hwid_reset_at')) {
                $table->timestamp('hwid_reset_at')->nullable()->after('hwid');
            }
            
            if (!Schema::hasColumn('users', 'hwid_auto_reset_hours')) {
                $table->integer('hwid_auto_reset_hours')->default(168)->after('hwid_reset_at');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'role', 'credits', 'status', 'last_login',
                'last_login_ip', 'hwid', 'hwid_reset_at', 'hwid_auto_reset_hours'
            ]);
        });
    }
};