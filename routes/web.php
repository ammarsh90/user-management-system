<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

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
});