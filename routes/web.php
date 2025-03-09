<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TelegramController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\HwidController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// إضافة مسارات إعادة تعيين كلمة المرور بشكل صريح
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// مسارات المشرف
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // لوحة التحكم الرئيسية
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // إدارة المستخدمين
    Route::resource('users', UserController::class);
    
    // هذه المسارات ستضاف لاحقاً مع إضافة المتحكمات الخاصة بها
    /*
    Route::post('/users/{id}/reset-hwid', [UserController::class, 'resetHWID'])->name('users.reset-hwid');
    Route::post('/users/{id}/add-credits', [UserController::class, 'addCredits'])->name('users.add-credits');
    Route::post('/users/{id}/deduct-credits', [UserController::class, 'deductCredits'])->name('users.deduct-credits');
    */
    Route::resource('users', UserController::class);
    Route::post('/users/{id}/reset-hwid', [HwidController::class, 'resetHWID'])->name('users.reset-hwid');
    Route::post('/users/{id}/add-credits', [UserController::class, 'addCredits'])->name('users.add-credits');
    Route::post('/users/{id}/deduct-credits', [UserController::class, 'deductCredits'])->name('users.deduct-credits');
    
    // إدارة الاشتراكات
    Route::resource('subscription-plans', SubscriptionController::class);
    Route::get('/subscriptions', [SubscriptionController::class, 'indexSubscriptions'])->name('subscriptions.index');
    // في ملف routes/web.php
Route::post('/subscriptions/activate', [SubscriptionController::class, 'activateSubscription'])->name('subscriptions.activate');
Route::post('/subscriptions/extend', [SubscriptionController::class, 'extendSubscription'])->name('subscriptions.extend');
    // إعدادات تلغرام
   // Route::resource('telegram', TelegramController::class);
    Route::resource('telegram', TelegramController::class);
    Route::post('/telegram/{id}/toggle', [TelegramController::class, 'toggleStatus'])->name('telegram.toggle');
    Route::post('/telegram/test-notification', [TelegramController::class, 'testNotification'])->name('telegram.test');
    
    // سجلات النظام
    // سجلات النظام
    Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');
    Route::get('/logs/login-history', [SystemLogController::class, 'loginHistory'])->name('logs.login-history');
    Route::get('/logs/hwid-resets', [SystemLogController::class, 'hwidResets'])->name('logs.hwid-resets');
    Route::get('/logs/transactions', [SystemLogController::class, 'transactions'])->name('logs.transactions');


 /*
    // نمط للمسارات المتبقية
    Route::get('/subscription-plans', function() { 
        return view('admin.subscription-plans.index'); 
    })->name('subscription-plans.index');
    
    Route::get('/subscriptions', function() { 
        return view('admin.subscriptions.index'); 
    })->name('subscriptions.index');
        Route::get('/telegram', function() { 
        return view('admin.telegram.index'); 
    })->name('telegram.index');
    

    Route::get('/logs', function() { 
        return view('admin.logs.index'); 
    })->name('logs.index');
    */
});