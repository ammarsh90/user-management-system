<?php
// app/Http/Controllers/Admin/HwidController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HwidReset;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Carbon\Carbon;

class HwidController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * عرض قائمة عمليات إعادة تعيين HWID.
     */
    public function index()
    {
        $user = auth()->user();
        
        // إذا كان المستخدم مشرفًا، عرض جميع عمليات إعادة التعيين
        if ($user->isAdmin()) {
            $resets = HwidReset::with(['user', 'resetBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } 
        // إذا كان المستخدم بائعًا، عرض عمليات إعادة التعيين للمستخدمين الذين قام بإنشاء اشتراكات لهم
        else if ($user->isSeller()) {
            $userIds = $user->sellerSubscriptions()->pluck('user_id')->unique();
            
            $resets = HwidReset::whereIn('user_id', $userIds)
                ->with(['user', 'resetBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            abort(403, 'Unauthorized');
        }

        return view('admin.hwid-resets.index', compact('resets'));
    }

    /**
     * إعادة تعيين HWID لمستخدم معين.
     */
    public function resetHwid(Request $request, User $user)
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