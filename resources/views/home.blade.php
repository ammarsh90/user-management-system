@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- القائمة الجانبية للمستخدم -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>لوحة المستخدم</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('home') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt me-2"></i>الرئيسية
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i>الملف الشخصي
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2"></i>الاشتراكات
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>سجل النشاط
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-credit-card me-2"></i>المعاملات المالية
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-cog me-2"></i>الإعدادات
                    </a>
                    <a href="{{ route('logout') }}" class="list-group-item list-group-item-action text-danger" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

            <!-- معلومات المستخدم المختصرة -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body text-center">
                    <div class="avatar mb-3">
                        <span class="avatar-text rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 40px;">
                            {{ substr(Auth::user()->username, 0, 1) }}
                        </span>
                    </div>
                    <h5 class="card-title">{{ Auth::user()->username }}</h5>
                    <p class="card-text text-muted">{{ Auth::user()->email }}</p>
                    <div class="d-flex justify-content-center align-items-center mt-2">
                        <span class="badge bg-success me-2">
                            @if(Auth::user()->role == 'admin')
                                مشرف
                            @elseif(Auth::user()->role == 'reseller')
                                بائع
                            @else
                                مستخدم
                            @endif
                        </span>
                        <span class="badge bg-info">
                            @if(Auth::user()->role == 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none">لوحة المشرف</a>
                            @else
                                عضو منذ {{ Auth::user()->created_at->format('Y-m-d') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="col-md-9">
            <!-- معلومات الترحيب -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4 class="card-title mb-3">مرحباً، {{ Auth::user()->username }}!</h4>
                    <p class="card-text">مرحباً بك في نظام إدارة المستخدمين. استخدم لوحة التحكم للوصول إلى جميع الميزات والإعدادات الخاصة بحسابك.</p>
                    
                    @if(Auth::user()->hasActiveSubscription())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            لديك اشتراك نشط حتى: <strong>{{ Auth::user()->activeSubscription->end_date->format('Y-m-d') }}</strong>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ليس لديك اشتراك نشط. <a href="#" class="alert-link">اشترك الآن</a> للاستفادة من جميع الميزات.
                        </div>
                    @endif
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-primary text-white rounded p-3">
                                    <i class="fas fa-wallet fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">الرصيد الحالي</h6>
                                    <h4 class="mb-0">{{ Auth::user()->credits }} كريدت</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-success text-white rounded p-3">
                                    <i class="fas fa-fingerprint fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">حالة HWID</h6>
                                    <h4 class="mb-0">
                                        @if(Auth::user()->hwid)
                                            <span class="text-success">مفعل</span>
                                        @else
                                            <span class="text-warning">غير مفعل</span>
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- آخر النشاطات -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>آخر النشاطات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>النشاط</th>
                                    <th>الوقت</th>
                                    <th>عنوان IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($loginHistory) && count($loginHistory) > 0)
                                    @foreach($loginHistory as $log)
                                    <tr>
                                        <td>
                                            @if($log->status == 'success')
                                                <span class="text-success"><i class="fas fa-sign-in-alt me-1"></i> تسجيل دخول ناجح</span>
                                            @else
                                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i> محاولة دخول فاشلة</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->login_time->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center py-3">لا توجد نشاطات مسجلة</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- معلومات الاشتراك -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-key me-2"></i>معلومات الاشتراك</h5>
                        </div>
                        <div class="card-body">
                            @if(Auth::user()->hasActiveSubscription())
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>الباقة:</strong> {{ Auth::user()->activeSubscription->plan->name }}</p>
                                        <p><strong>تاريخ البدء:</strong> {{ Auth::user()->activeSubscription->start_date->format('Y-m-d') }}</p>
                                        <p><strong>تاريخ الانتهاء:</strong> {{ Auth::user()->activeSubscription->end_date->format('Y-m-d') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>الحالة:</strong> 
                                            @if(Auth::user()->activeSubscription->status == 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-warning">{{ Auth::user()->activeSubscription->status }}</span>
                                            @endif
                                        </p>
                                        <p><strong>الأيام المتبقية:</strong> {{ Auth::user()->activeSubscription->remaining_days }} يوم</p>
                                        <p><strong>البائع:</strong> 
                                            @if(Auth::user()->activeSubscription->reseller)
                                                {{ Auth::user()->activeSubscription->reseller->username }}
                                            @else
                                                مدير النظام
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                    <a href="#" class="btn btn-primary">
                                        <i class="fas fa-sync-alt me-1"></i> تجديد الاشتراك
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="mb-4">
                                        <i class="fas fa-key fa-4x text-muted"></i>
                                    </div>
                                    <h5 class="mb-3">ليس لديك اشتراك نشط حالياً</h5>
                                    <p class="text-muted mb-4">اشترك في إحدى باقاتنا للاستفادة من جميع ميزات النظام</p>
                                    <a href="#" class="btn btn-primary btn-lg">
                                        <i class="fas fa-shopping-cart me-1"></i> تصفح باقات الاشتراك
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection