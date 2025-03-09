<?php
// app/Http/Controllers/UserProfileController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\HwidReset;
use App\Models\LoginLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\TelegramService;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * عرض صفحة الملف الشخصي للمستخدم.
     */
    public function index()
    {
        $user = auth()->user();
        
        // الحصول على معلومات الاشتراك
        $subscription = $user->activeSubscription()->with('type', 'seller')->first();
        
        // الحصول على آخر 10 عمليات تسجيل دخول
        $loginLogs = LoginLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // الحصول على عمليات إعادة تعيين HWID
        $hwidResets = HwidReset::where('user_id', $user->id)
            ->with('resetBy')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // معلومات عن وقت إعادة تعيين HWID التالي
        $timeUntilHwidReset = $user->timeUntilHwidReset();
        
        return view('profile.index', compact('user', 'subscription', 'loginLogs', 'hwidResets', 'timeUntilHwidReset'));
    }

    /**
     * تحديث المعلومات الشخصية للمستخدم.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('profile')
            ->with('success', 'Profile updated successfully');
    }

    /**
     * تحديث كلمة مرور المستخدم.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // التحقق من صحة كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The current password is incorrect'])
                ->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile')
            ->with('success', 'Password updated successfully');
    }

    /**
     * إعادة تعيين HWID للمستخدم.
     */
    public function resetHwid(Request $request)
    {
        $admin = auth()->user();
        
        // التحقق من صلاحيات المستخدم (إما مشرف أو بائع)
        if (!$admin->isAdmin() && !$admin->isSeller()) {
            abort(403, 'Unauthorized');
        }

        // إذا كان المستخدم بائعًا، يجب التحقق من أنه أنشأ اشتراكًا للمستخدم
        if ($admin->isSeller()) {
            $hasSubscription = $admin->sellerSubscriptions()
                ->where('user_id', $user->id)
                ->exists();
                
            if (!$hasSubscription) {
                return redirect()->back()
                    ->with('error', 'You can only reset HWID for users with subscriptions created by you');
            }
        }

        // التحقق من وجود اشتراك نشط للمستخدم
        if (!$user->hasActiveSubscription()) {
            return redirect()->back()
                ->with('error', 'User does not have an active subscription');
        }

        $request->validate([
            'force' => 'sometimes|boolean',
        ]);

        $force = $request->has('force') && $request->force && $admin->isAdmin();
        
        // إذا لم يكن هناك تجاوز من قِبل المشرف، تحقق من إمكانية إعادة التعيين
        if (!$force && $user->hwid_last_reset) {
            $resetDelay = SystemSetting::where('key', 'hwid_reset_delay_hours')->first();
            $delayHours = $resetDelay ? (int)$resetDelay->value : 168; // افتراضي: 7 أيام
            
            $hoursElapsed = Carbon::now()->diffInHours($user->hwid_last_reset);
            
            if ($hoursElapsed < $delayHours) {
                $hoursRemaining = $delayHours - $hoursElapsed;
                return redirect()->back()
                    ->with('error', "HWID reset not available yet. {$hoursRemaining} hours remaining.");
            }
        }

        // تسجيل عملية إعادة التعيين
        HwidReset::create([
            'user_id' => $user->id,
            'old_hwid' => $user->hwid,
            'new_hwid' => null, // سيتم تعيينه عند تسجيل الدخول التالي
            'reset_type' => $force ? 'forced' : 'manual',
            'reset_by' => $admin->id,
        ]);

        // مسح HWID وتسجيل وقت إعادة التعيين
        $user->hwid = null;
        $user->hwid_last_reset = now();
        $user->save();

        // إرسال إشعار تلجرام
        $this->telegramService->sendNotification('hwid_reset', [
            'user' => $user->name,
            'email' => $user->email,
            'old_hwid' => $user->hwid ?: 'None',
            'new_hwid' => 'Pending',
            'reset_type' => $force ? 'forced' : 'manual',
            'reset_by' => $admin->name
        ]);

        return redirect()->back()
            ->with('success', 'HWID reset successfully. User will need to log in again to set a new HWID.');
    }
}