<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TelegramController;
use App\Http\Controllers\Admin\SystemLogController;

// جميع مسارات المشرف مع middleware للتحقق من الصلاحيات
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // لوحة التحكم الرئيسية
    Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');
    
    // إدارة المستخدمين
    Route::resource('users', UserController::class);
    Route::post('/users/{id}/reset-hwid', [UserController::class, 'resetHWID'])->name('users.reset-hwid');
    Route::post('/users/{id}/add-credits', [UserController::class, 'addCredits'])->name('users.add-credits');
    Route::post('/users/{id}/deduct-credits', [UserController::class, 'deductCredits'])->name('users.deduct-credits');
    
    // إدارة الاشتراكات
    Route::resource('subscription-plans', SubscriptionController::class);
    Route::get('/subscriptions', [SubscriptionController::class, 'indexSubscriptions'])->name('subscriptions.index');
    
    // إعدادات تلغرام
    Route::resource('telegram', TelegramController::class);
    
    // سجلات النظام
    Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');
});