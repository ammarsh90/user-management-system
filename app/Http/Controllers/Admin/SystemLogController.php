<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemLog;
use App\Models\LoginHistory;
use App\Models\Transaction;

class SystemLogController extends Controller
{
    public function index()
    {
        $logs = SystemLog::with('user')->latest()->paginate(20);
        return view('admin.logs.index', compact('logs'));
    }

    public function loginHistory()
    {
        $loginHistory = LoginHistory::with('user')->latest('login_time')->paginate(20);
        return view('admin.logs.login-history', compact('loginHistory'));
    }

    public function hwidResets()
    {
        $hwidResets = SystemLog::with('user')
            ->where('action', 'like', '%hwid_reset%')
            ->latest()
            ->paginate(20);
        return view('admin.logs.hwid-resets', compact('hwidResets'));
    }

    public function transactions()
    {
        $transactions = Transaction::with(['user', 'admin'])->latest()->paginate(20);
        return view('admin.logs.transactions', compact('transactions'));
    }
}