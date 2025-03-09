<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        try {
            // جلب آخر سجلات الدخول للمستخدم
            $loginHistory = LoginHistory::where('user_id', $user->id)
                ->orderBy('login_time', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // في حالة وجود خطأ، نعيد مصفوفة فارغة
            $loginHistory = collect([]);
        }
        
        return view('home', compact('loginHistory'));
    }
}