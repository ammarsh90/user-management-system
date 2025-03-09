<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\LoginHistory;
use App\Models\SystemLog;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات بسيطة (آمنة) حتى لو كانت بعض الجداول غير موجودة
        $users_count = User::count();
        $active_subscriptions = 0; // UserSubscription::where('status', 'active')->count();
        $total_credits = User::sum('credits');
        $hwid_resets = 0; // SystemLog::where('action', 'like', '%hwid_reset%')->count();
        
        // أحدث المستخدمين
        $recent_users = User::orderBy('created_at', 'desc')->take(5)->get();
        
        // أحدث عمليات الدخول
        $recent_logins = collect([]); // قائمة فارغة في حالة عدم توفر الجدول
        try {
            $recent_logins = LoginHistory::with('user')
                ->where('status', 'success')
                ->orderBy('login_time', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // تجاهل الخطأ
        }
        
        return view('admin.dashboard', compact(
            'users_count',
            'active_subscriptions',
            'total_credits',
            'hwid_resets',
            'recent_users',
            'recent_logins'
        ));
    }
}