<?php
// app/Http/Controllers/Admin/SubscriptionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\Credit;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramService;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * عرض جميع الاشتراكات.
     */
    public function index()
    {
        $user = auth()->user();
        
        // إذا كان المستخدم مشرفًا، عرض جميع الاشتراكات
        if ($user->isAdmin()) {
            $subscriptions = Subscription::with(['user', 'type', 'seller'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } 
        // إذا كان المستخدم بائعًا، عرض الاشتراكات التي قام بإنشائها فقط
        else if ($user->isSeller()) {
            $subscriptions = Subscription::with(['user', 'type'])
                ->where('seller_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            abort(403, 'Unauthorized');
        }

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * عرض نموذج إنشاء اشتراك جديد.
     */
    public function create()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['admin', 'seller']);
        })->get();
        
        $subscriptionTypes = SubscriptionType::where('is_active', true)->get();
        
        return view('admin.subscriptions.create', compact('users', 'subscriptionTypes'));
    }

    /**
     * تخزين اشتراك جديد.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_type_id' => 'required|exists:subscription_types,id',
        ]);

        $seller = auth()->user();
        $user = User::findOrFail($request->user_id);
        $subscriptionType = SubscriptionType::findOrFail($request->subscription_type_id);

        // التحقق من وجود رصيد كافٍ للبائع
        if (!$seller->isAdmin()) {
            $credit = Credit::where('user_id', $seller->id)->first();
            
            if (!$credit || $credit->balance < $subscriptionType->credit_cost) {
                return redirect()->back()->with('error', 'You do not have enough credit to create this subscription.');
            }
        }

        // إلغاء أي اشتراكات نشطة سابقة
        Subscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // حساب تاريخ بدء ونهاية الاشتراك
        $startDate = now();
        $endDate = Carbon::now()->addDays($subscriptionType->duration_days);

        DB::beginTransaction();
        try {
            // إنشاء الاشتراك الجديد
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'subscription_type_id' => $subscriptionType->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'seller_id' => $seller->id,
                'is_active' => true,
            ]);

            // خصم الرصيد من البائع (ما عدا المشرف)
            if (!$seller->isAdmin() && $subscriptionType->credit_cost > 0) {
                $credit->balance -= $subscriptionType->credit_cost;
                $credit->save();

                // تسجيل معاملة الرصيد
                CreditTransaction::create([
                    'user_id' => $seller->id,
                    'amount' => -$subscriptionType->credit_cost,
                    'type' => 'subscription',
                    'description' => 'Subscription created for ' . $user->name,
                    'subscription_id' => $subscription->id,
                ]);
            }

            DB::commit();

            // إرسال إشعار تلجرام
            $this->telegramService->sendNotification('subscription_created', [
                'user' => $user->name,
                'email' => $user->email,
                'subscription_type' => $subscriptionType->name,
                'duration' => $subscriptionType->duration_days,
                'end_date' => $endDate->format('Y-m-d'),
                'seller' => $seller->name
            ]);

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Subscription created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الاشتراك.
     */
    public function show(Subscription $subscription)
    {
        $user = auth()->user();
        
        // التحقق من الصلاحيات (المشرف أو البائع الذي أنشأ الاشتراك)
        if (!$user->isAdmin() && $subscription->seller_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * تحديث حالة الاشتراك (تفعيل/إلغاء التفعيل).
     */
    public function updateStatus(Request $request, Subscription $subscription)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user = auth()->user();
        
        // التحقق من الصلاحيات (المشرف أو البائع الذي أنشأ الاشتراك)
        if (!$user->isAdmin() && $subscription->seller_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $subscription->is_active = $request->is_active;
        $subscription->save();

        // إرسال إشعار تلجرام
        $status = $request->is_active ? 'activated' : 'deactivated';
        $this->telegramService->sendNotification('subscription_status_changed', [
            'user' => $subscription->user->name,
            'email' => $subscription->user->email,
            'subscription_type' => $subscription->type->name,
            'status' => $status,
            'admin' => $user->name
        ]);

        return redirect()->back()->with('success', 'Subscription status updated successfully.');
    }

    /**
     * تمديد الاشتراك.
     */
    public function extend(Request $request, Subscription $subscription)
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        
        // التحقق من الصلاحيات (المشرف فقط)
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // تمديد تاريخ انتهاء الاشتراك
        $subscription->end_date = Carbon::parse($subscription->end_date)->addDays($request->days);
        $subscription->save();

        // إرسال إشعار تلجرام
        $this->telegramService->sendNotification('subscription_extended', [
            'user' => $subscription->user->name,
            'email' => $subscription->user->email,
            'subscription_type' => $subscription->type->name,
            'days_added' => $request->days,
            'new_end_date' => $subscription->end_date->format('Y-m-d'),
            'admin' => $user->name
        ]);

        return redirect()->back()->with('success', 'Subscription extended successfully.');
    }

    /**
     * حذف الاشتراك (للمشرفين فقط).
     */
    public function destroy(Subscription $subscription)
    {
        $user = auth()->user();
        
        // التحقق من الصلاحيات (المشرف فقط)
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // حفظ معلومات الاشتراك قبل الحذف
        $userData = [
            'user' => $subscription->user->name,
            'email' => $subscription->user->email,
            'subscription_type' => $subscription->type->name,
            'admin' => $user->name
        ];

        $subscription->delete();

        // إرسال إشعار تلجرام
        $this->telegramService->sendNotification('subscription_deleted', $userData);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted successfully.');
    }
}