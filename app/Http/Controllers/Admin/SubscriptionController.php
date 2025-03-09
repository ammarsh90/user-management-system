<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\SystemLog;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::all();
        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $plan = SubscriptionPlan::create([
            'name' => $request->name,
            'duration_months' => $request->duration_months,
            'price' => $request->price,
            'description' => $request->description
        ]);

        // Log action
        $this->logSystemAction($request->user()->id, 'subscription_plan_create', "Subscription plan {$plan->name} created", $request->ip());

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'تم إنشاء خطة الاشتراك بنجاح!');
    }

    public function edit($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        return view('admin.subscription-plans.edit', compact('plan'));
    }
    
    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'duration_months' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);
    
        $plan->name = $request->name;
        $plan->duration_months = $request->duration_months;
        $plan->price = $request->price;
        $plan->description = $request->description;
        $plan->save();
    
        // Log action
        $this->logSystemAction($request->user()->id, 'subscription_plan_update', "Subscription plan {$plan->name} updated", $request->ip());
    
        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'تم تحديث خطة الاشتراك بنجاح!');
    }
    
    public function destroy(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        // Check if there are any subscriptions using this plan
        $usageCount = UserSubscription::where('plan_id', $id)->count();
        if ($usageCount > 0) {
            return redirect()
                ->route('admin.subscription-plans.index')
                ->with('error', 'لا يمكن حذف الخطة لأنها مستخدمة في ' . $usageCount . ' اشتراك!');
        }
        
        $name = $plan->name;
        $plan->delete();
    
        // Log action
        $this->logSystemAction($request->user()->id, 'subscription_plan_delete', "Subscription plan {$name} deleted", $request->ip());
    
        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'تم حذف خطة الاشتراك بنجاح!');
    }
    
    public function indexSubscriptions()
    {
        $subscriptions = UserSubscription::with(['user', 'plan', 'reseller'])->latest()->paginate(15);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }
    
    public function showSubscription($id)
    {
        $subscription = UserSubscription::with(['user', 'plan', 'reseller'])->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }
    
    public function activateSubscription(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);
        
        $user = User::findOrFail($request->user_id);
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $admin = $request->user();
        
        // Calculate subscription dates
        $now = Carbon::now();
        $endDate = $now->copy()->addMonths($plan->duration_months);
        
        // Create subscription
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'reseller_id' => null, // Admin activated
            'start_date' => $now,
            'end_date' => $endDate,
            'status' => 'active'
        ]);
        
        // Log action
        $this->logSystemAction($admin->id, 'subscription_activate', "Subscription activated for user {$user->username}", $request->ip());
        
        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'تم تفعيل الاشتراك بنجاح!');
    }
    
    private function logSystemAction($userId, $action, $description, $ipAddress)
    {
        SystemLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ipAddress
        ]);
    }
}