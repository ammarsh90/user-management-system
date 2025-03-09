<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Transaction;
use App\Models\LoginHistory;
use App\Models\SystemLog;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use App\Models\HwidReset; 
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,reseller,user',
            'status' => 'required|in:active,inactive,banned',
            'credits' => 'required|numeric|min:0',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'credits' => $request->credits,
            'hwid_auto_reset_hours' => $request->hwid_auto_reset_hours ?? 168,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified resource.
     
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
*/
public function show($id)
{
    $user = User::findOrFail($id);
    $subscriptions = UserSubscription::where('user_id', $id)->latest()->paginate(5);
    $transactions = Transaction::where('user_id', $id)->latest()->paginate(5);
    $loginHistory = LoginHistory::where('user_id', $id)->latest()->paginate(5);
    
    return view('admin.users.show', compact('user', 'subscriptions', 'transactions', 'loginHistory'));
}

public function edit($id)
{
    $user = User::findOrFail($id);
    return view('admin.users.edit', compact('user'));
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,reseller,user',
            'status' => 'required|in:active,inactive,banned',
            'credits' => 'required|numeric|min:0',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $userData = [
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
            'credits' => $request->credits,
            'hwid_auto_reset_hours' => $request->hwid_auto_reset_hours ?? $user->hwid_auto_reset_hours,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }
    public function resetHWID(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->hwid = null;
        $user->hwid_last_reset = now();
        $user->save();

        // سجل عملية إعادة التعيين (اختياري)
        HwidReset::create([
            'user_id' => $user->id,
            'reset_by' => User::id(), // أو المعرف المناسب
            'reset_type' => 'admin',
            'old_hwid' => $user->hwid,
        ]);

        return redirect()->back()->with('success', 'HWID has been reset successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // منع حذف المستخدم المشرف الوحيد
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'لا يمكن حذف المشرف الوحيد في النظام');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }
}