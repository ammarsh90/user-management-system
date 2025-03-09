@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users fa-2x"></i> المستخدمين</h5>
                <p class="card-text display-6">{{ $users_count }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-key fa-2x"></i> الاشتراكات النشطة</h5>
                <p class="card-text display-6">{{ $active_subscriptions }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-money-bill fa-2x"></i> إجمالي الكريدت</h5>
                <p class="card-text display-6">{{ $total_credits }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-unlock-alt fa-2x"></i> عمليات HWID</h5>
                <p class="card-text display-6">{{ $hwid_resets }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">آخر المستخدمين المسجلين</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->status == 'active')
                                        <span class="badge bg-success">نشط</span>
                                    @elseif($user->status == 'inactive')
                                        <span class="badge bg-warning">غير نشط</span>
                                    @else
                                        <span class="badge bg-danger">محظور</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">آخر عمليات الدخول</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>IP</th>
                                <th>المصدر</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($recent_logins) > 0)
                                @foreach($recent_logins as $login)
                                <tr>
                                    <td>{{ $login->user->username }}</td>
                                    <td>{{ $login->ip_address }}</td>
                                    <td>
                                        @if($login->source == 'web')
                                            <span class="badge bg-primary">ويب</span>
                                        @else
                                            <span class="badge bg-info">تطبيق</span>
                                        @endif
                                    </td>
                                    <td>{{ $login->login_time->format('Y-m-d H:i') }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد عمليات دخول مسجلة</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection