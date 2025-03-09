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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration_months');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // بعد إنشاء الجدول، أضف القيد الخارجي في جدول user_subscriptions
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
        });
        
        Schema::dropIfExists('subscription_plans');
    }
};