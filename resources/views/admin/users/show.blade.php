@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم: ' . $user->username)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> العودة للقائمة
    </a>
    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> تعديل
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">معلومات المستخدم</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40%">ID</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th>اسم المستخدم</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th>البريد الإلكتروني</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>الدور</th>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge bg-danger">مشرف</span>
                            @elseif($user->role == 'reseller')
                                <span class="badge bg-warning">بائع</span>
                            @else
                                <span class="badge bg-info">مستخدم</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>الحالة</th>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge bg-success">نشط</span>
                            @elseif($user->status == 'inactive')
                                <span class="badge bg-warning">غير نشط</span>
                            @else
                                <span class="badge bg-danger">محظور</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>الرصيد (الكريدت)</th>
                        <td>{{ $user->credits }}</td>
                    </tr>
                    <tr>
                        <th>HWID</th>
                        <td>{{ $user->hwid ?: 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <th>وقت إعادة تعيين HWID</th>
                        <td>{{ $user->hwid_reset_at ? $user->hwid_reset_at->format('Y-m-d H:i:s') : 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <th>ساعات إعادة تعيين HWID التلقائية</th>
                        <td>{{ $user->hwid_auto_reset_hours }} ساعة</td>
                    </tr>
                    <tr>
                        <th>آخر تسجيل دخول</th>
                        <td>{{ $user->last_login ? $user->last_login->format('Y-m-d H:i:s') : 'لم يسجل الدخول' }}</td>
                    </tr>
                    <tr>
                        <th>عنوان IP لآخر تسجيل دخول</th>
                        <td>{{ $user->last_login_ip ?: 'غير متوفر' }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الإنشاء</th>
                        <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">إدارة HWID</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.reset-hwid', $user->id) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من إعادة تعيين HWID؟')">
                        <i class="fas fa-sync"></i> إعادة تعيين HWID
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">إدارة الرصيد</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.add-credits', $user->id) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="amount" placeholder="المبلغ" required min="0.01">
                        <input type="text" class="form-control" name="description" placeholder="الوصف (اختياري)">
                        <button type="submit" class="btn btn-success">إضافة رصيد</button>
                    </div>
                </form>
                
                <form action="{{ route('admin.users.deduct-credits', $user->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="amount" placeholder="المبلغ" required min="0.01">
                        <input type="text" class="form-control" name="description" placeholder="الوصف (اختياري)">
                        <button type="submit" class="btn btn-warning">خصم رصيد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Subscriptions Table -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">اشتراكات المستخدم</h5>
    </div>
    <div class="card-body">
        @if($subscriptions->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>خطة الاشتراك</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                            <th>البائع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->id }}</td>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $subscriptions->links() }}
            </div>
        @else
            <div class="alert alert-info">لا توجد اشتراكات لهذا المستخدم.</div>
        @endif
    </div>
</div>

<!-- Transactions Table -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">معاملات المستخدم</h5>
    </div>
    <div class="card-body">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>المبلغ</th>
                            <th>النوع</th>
                            <th>الوصف</th>
                            <th>بواسطة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->amount }}</td>
                                <td>
                                    @if($transaction->type == 'credit')
                                        <span class="badge bg-success">إضافة</span>
                                    @else
                                        <span class="badge bg-danger">خصم</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->admin ? $transaction->admin->username : 'النظام' }}</td>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="alert alert-info">لا توجد معاملات لهذا المستخدم.</div>
        @endif
    </div>
</div>

<!-- Login History Table -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">سجل تسجيل الدخول</h5>
    </div>
    <div class="card-body">
        @if($loginHistory->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>عنوان IP</th>
                            <th>معلومات المتصفح</th>
                            <th>HWID</th>
                            <th>المصدر</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loginHistory as $login)
                            <tr>
                                <td>{{ $login->id }}</td>
                                <td>{{ $login->ip_address }}</td>
                                <td><small>{{ Str::limit($login->user_agent, 50) }}</small></td>
                                <td><small>{{ Str::limit($login->hwid, 20) }}</small></td>
                                <td>
                                    @if($login->source == 'web')
                                        <span class="badge bg-primary">ويب</span>
                                    @else
                                        <span class="badge bg-info">تطبيق</span>
                                    @endif
                                </td>
                                <td>
                                    @if($login->status == 'success')
                                        <span class="badge bg-success">ناجح</span>
                                    @else
                                        <span class="badge bg-danger">فاشل</span>
                                    @endif
                                </td>
                                <td>{{ $login->login_time->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $loginHistory->links() }}
            </div>
        @else
            <div class="alert alert-info">لا يوجد سجل تسجيل دخول لهذا المستخدم.</div>
        @endif
    </div>
</div>
@endsection