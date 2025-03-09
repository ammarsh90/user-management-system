<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| مسارات واجهة برمجة التطبيقات للتواصل مع تطبيق سطح المكتب
|
*/

// مسارات عامة (لا تتطلب مصادقة)
Route::post('/login', [AuthController::class, 'login']);

// مسارات محمية (تتطلب مصادقة بواسطة Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // معلومات المستخدم الحالي
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // تسجيل الخروج
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // التحقق من حالة الاشتراك
    Route::get('/check-subscription', [AuthController::class, 'checkSubscription']);
    
    // إعادة تعيين HWID
    Route::post('/reset-hwid', [AuthController::class, 'resetHwid']);
    
    // تحديث معلومات الملف الشخصي
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);
});