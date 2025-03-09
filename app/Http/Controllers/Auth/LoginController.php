<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
      //  return 'name'; // استخدام اسم المستخدم بدلاً من البريد الإلكتروني
        return 'username';
    }
    
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // تسجيل عملية الدخول الناجحة
        $this->logLogin($request, $user->id, 'success');
        
        // تحديث معلومات آخر دخول
        $user->last_login = now();
        $user->last_login_ip = $request->ip();
        $user->save();
        
        // توجيه المستخدم المناسب
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect($this->redirectTo);
    }
    
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // تسجيل محاولة الدخول الفاشلة
       // $this->logLogin($request, null, 'failed');
        
        return parent::sendFailedLoginResponse($request);
    }
    
    /**
     * Log login attempt
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $userId
     * @param  string  $status
     * @return void
     */
    private function logLogin(Request $request, $userId, $status)
    {
        LoginHistory::create([
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'hwid' => null, // التطبيق الويب لا يستخدم HWID
            'login_time' => now(),
            'status' => $status,
            'source' => 'web'
        ]);
    }
}