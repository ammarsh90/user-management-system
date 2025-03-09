@extends('layouts.admin')

@section('title', 'إدارة الاشتراكات')

@section('content')
<div class="mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriptionModal">
        <i class="fas fa-plus-circle"></i> تفعيل اشتراك جديد
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>المستخدم</th>
                        <th>الخطة</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                        <th>البائع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                @if(isset($subscriptions) && $subscriptions->count() > 0)
                <tbody>
                    @foreach($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->id }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $subscription->user_id) }}">
                                {{ $subscription->user->username }}
                            </a>
                        </td>
                        <td>{{ $subscription->plan->name }}</td>
                        <td>{{ $subscription->start_date->format('Y-m-d') }}</td>
                        <td>{{ $subscription->end_date->format('Y-m-d') }}</td>
                        <td>
                            @if($subscription->status == 'active')
                                <span class="badge bg-success">نشط</span>
                            @elseif($subscription->status == 'expired')
                                <span class="badge bg-warning">منتهي</span>
                            @else
                                <span class="badge bg-danger">ملغي</span>
                            @endif
                        </td>
                        <td>{{ $subscription->reseller ? $subscription->reseller->username : 'مدير النظام' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-warning" onclick="extendSubscription({{ $subscription->id }})">
                                    <i class="fas fa-calendar-plus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @else
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">
                            <p class="my-3 text-muted">لا توجد اشتراكات حالياً</p>
                        </td>
                    </tr>
                </tbody>
                @endif
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($subscriptions) && $subscriptions->count() > 0)
        <div class="mt-4">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Subscription Modal -->
<div class="modal fade" id="addSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفعيل اشتراك جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subscriptions.activate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">المستخدم</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">اختر المستخدم</option>
                            @foreach(\App\Models\User::where('role', 'user')->orderBy('username')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="plan_id" class="form-label">خطة الاشتراك</label>
                        <select class="form-select" id="plan_id" name="plan_id" required>
                            <option value="">اختر الخطة</option>
                            @foreach(\App\Models\SubscriptionPlan::orderBy('name')->get() as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->duration_months }} شهر - {{ $plan->price }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تفعيل الاشتراك</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extend Subscription Modal -->
<div class="modal fade" id="extendSubscriptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تمديد الاشتراك</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subscriptions.extend') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="subscription_id" name="subscription_id">
                    <div class="mb-3">
                        <label for="months" class="form-label">عدد الأشهر</label>
                        <input type="number" class="form-control" id="months" name="months" required min="1" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تمديد الاشتراك</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function extendSubscription(id) {
        // تعيين رقم الاشتراك للنموذج
        document.getElementById('subscription_id').value = id;
        // فتح النافذة المنبثقة
        var modal = new bootstrap.Modal(document.getElementById('extendSubscriptionModal'));
        modal.show();
    }
</script>
@endsection