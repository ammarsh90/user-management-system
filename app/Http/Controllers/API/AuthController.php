<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginLog;
use App\Models\HwidReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\TelegramService;

class AuthController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'hwid' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // محاولة المصادقة
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // تسجيل محاولة الدخول الفاشلة
            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                LoginLog::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'hwid' => $request->hwid,
                    'successful' => false,
                    'source' => 'app'
                ]);

                // إرسال إشعار تلجرام لمحاولة دخول فاشلة
                $this->telegramService->sendNotification('login_failed', [
                    'user' => $user->name,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'hwid' => $request->hwid
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = Auth::user();

        // التحقق من تفعيل الحساب
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is inactive'
            ], 403);
        }

        // التحقق من وجود اشتراك فعال
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have an active subscription'
            ], 403);
        }

        // التحقق من HWID
        if ($user->hwid && $user->hwid !== $request->hwid) {
            // محاولة إعادة تعيين HWID تلقائيًا
            if ($user->canResetHwid()) {
                // تسجيل إعادة التعيين
                HwidReset::create([
                    'user_id' => $user->id,
                    'old_hwid' => $user->hwid,
                    'new_hwid' => $request->hwid,
                    'reset_type' => 'automatic'
                ]);

                // تحديث HWID والوقت
                $user->hwid = $request->hwid;
                $user->hwid_last_reset = now();
                $user->save();

                // إرسال إشعار تلجرام
                $this->telegramService->sendNotification('hwid_reset', [
                    'user' => $user->name,
                    'email' => $user->email,
                    'old_hwid' => $user->hwid,
                    'new_hwid' => $request->hwid,
                    'reset_type' => 'automatic'
                ]);
            } else {
                // لا يمكن إعادة التعيين حاليًا
                return response()->json([
                    'status' => 'error',
                    'message' => 'HWID mismatch and reset not available',
                    'hours_until_reset' => $user->timeUntilHwidReset()
                ], 403);
            }
        } else if (!$user->hwid) {
            // تعيين HWID لأول مرة
            $user->hwid = $request->hwid;
            $user->save();
        }

        // تحديث معلومات آخر تسجيل دخول
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        // إنشاء سجل الدخول
        LoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'hwid' => $request->hwid,
            'successful' => true,
            'source' => 'app'
        ]);

        // إرسال إشعار تلجرام لتسجيل الدخول الناجح
        $this->telegramService->sendNotification('login_success', [
            'user' => $user->name,
            'email' => $user->email,
            'ip' => $request->ip(),
            'hwid' => $request->hwid
        ]);

        // إنشاء رمز API
        $token = $user->createToken('auth-token')->plainTextToken;

        // الحصول على معلومات الاشتراك
        $subscription = $user->activeSubscription()->with('type')->first();

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'subscription' => [
                    'type' => $subscription->type->name,
                    'end_date' => $subscription->end_date->format('Y-m-d'),
                    'days_left' => now()->diffInDays($subscription->end_date, false)
                ]
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        // حذف الرمز الحالي
        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    public function checkSubscription(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'status' => 'error',
                'has_active_subscription' => false
            ]);
        }

        $subscription = $user->activeSubscription()->with('type')->first();

        return response()->json([
            'status' => 'success',
            'has_active_subscription' => true,
            'subscription' => [
                'type' => $subscription->type->name,
                'start_date' => $subscription->start_date->format('Y-m-d'),
                'end_date' => $subscription->end_date->format('Y-m-d'),
                'days_left' => now()->diffInDays($subscription->end_date, false)
            ]
        ]);
    }

    public function resetHwid(Request $request)
    {
        $user = $request->user();
        $newHwid = $request->hwid;

        if (!$newHwid) {
            return response()->json([
                'status' => 'error',
                'message' => 'New HWID is required'
            ], 422);
        }

        if (!$user->canResetHwid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'HWID reset not available yet',
                'hours_until_reset' => $user->timeUntilHwidReset()
            ], 403);
        }

        // تسجيل إعادة التعيين
        HwidReset::create([
            'user_id' => $user->id,
            'old_hwid' => $user->hwid,
            'new_hwid' => $newHwid,
            'reset_type' => 'manual'
        ]);

        // تحديث HWID والوقت
        $user->hwid = $newHwid;
        $user->hwid_last_reset = now();
        $user->save();

        // إرسال إشعار تلجرام
        $this->telegramService->sendNotification('hwid_reset', [
            'user' => $user->name,
            'email' => $user->email,
            'old_hwid' => $user->hwid,
            'new_hwid' => $newHwid,
            'reset_type' => 'manual'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'HWID reset successfully'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'current_password' => 'required_with:new_password',
            'new_password' => 'sometimes|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // التحقق من كلمة المرور الحالية
        if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Current password is incorrect'
            ], 403);
        }

        // تحديث المعلومات
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ]);
    }
}