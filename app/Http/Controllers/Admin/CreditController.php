<?php
// app/Http/Controllers/Admin/CreditController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Credit;
use App\Models\CreditTransaction;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramService;

class CreditController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * عرض قائمة البائعين وأرصدتهم.
     */
    public function index()
    {
        // التأكد من أن المستخدم لديه دور المشرف
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // الحصول على دور البائع
        $sellerRole = Role::where('name', 'seller')->first();
        
        if (!$sellerRole) {
            return redirect()->back()->with('error', 'Seller role not found');
        }

        // الحصول على جميع البائعين مع أرصدتهم
        $sellers = User::whereHas('roles', function ($query) use ($sellerRole) {
                $query->where('role_id', $sellerRole->id);
            })
            ->with('credit')
            ->paginate(15);

        return view('admin.credits.index', compact('sellers'));
    }

    /**
     * عرض نموذج إضافة رصيد.
     */
    public function create()
    {
        // التأكد من أن المستخدم لديه دور المشرف
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // الحصول على دور البائع
        $sellerRole = Role::where('name', 'seller')->first();
        
        if (!$sellerRole) {
            return redirect()->back()->with('error', 'Seller role not found');
        }

        // الحصول على جميع البائعين
        $sellers = User::whereHas('roles', function ($query) use ($sellerRole) {
                $query->where('role_id', $sellerRole->id);
            })
            ->with('credit')
            ->get();

        return view('admin.credits.create', compact('sellers'));
    }

    /**
     * تخزين معاملة رصيد جديدة.
     */
    public function store(Request $request)
    {
        // التأكد من أن المستخدم لديه دور المشرف
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string|max:255',
        ]);

        $seller = User::findOrFail($request->user_id);
        $amount = (float) $request->amount;
        $admin = auth()->user();

        DB::beginTransaction();
        try {
            // الحصول على أو إنشاء سجل الرصيد للبائع
            $credit = Credit::firstOrCreate(
                ['user_id' => $seller->id],
                ['balance' => 0]
            );

            // تحديث الرصيد
            $credit->balance += $amount;
            $credit->save();

            // إنشاء سجل معاملة
            CreditTransaction::create([
                'user_id' => $seller->id,
                'amount' => $amount,
                'type' => 'add',
                'description' => $request->description ?: 'Added by admin',
                'admin_id' => $admin->id,
            ]);

            DB::commit();

            // إرسال إشعار تلجرام
            $this->telegramService->sendNotification('credit_added', [
                'user' => $seller->name,
                'email' => $seller->email,
                'amount' => $amount,
                'balance' => $credit->balance,
                'admin' => $admin->name
            ]);

            return redirect()->route('admin.credits.index')
                ->with('success', 'Credit added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل رصيد ومعاملات بائع معين.
     */
    public function show(User $user)
    {
        // التأكد من أن المستخدم لديه دور المشرف
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // التأكد من أن المستخدم هو بائع
        if (!$user->isSeller()) {
            abort(404, 'User is not a seller');
        }

        // الحصول على معلومات الرصيد
        $credit = Credit::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        // الحصول على سجل المعاملات
        $transactions = CreditTransaction::where('user_id', $user->id)
            ->with(['subscription', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // الحصول على إحصائيات الاشتراكات
        $subscriptionStats = $user->sellerSubscriptions()
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN is_active = 1 AND end_date > NOW() THEN 1 ELSE 0 END) as active_count')
            ->first();

        return view('admin.credits.show', compact('user', 'credit', 'transactions', 'subscriptionStats'));
    }
}