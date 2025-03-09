@extends('layouts.admin')

@section('title', 'سجل تسجيل الدخول')

@section('content')
<div class="mb-3">
    <div class="btn-group" role="group">
        <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-primary">سجلات النظام</a>
        <a href="{{ route('admin.logs.login-history') }}" class="btn btn-primary active">سجل تسجيل الدخول</a>
        <a href="{{ route('admin.logs.hwid-resets') }}" class="btn btn-outline-primary">سجل إعادة تعيين HWID</a>
        <a href="{{ route('admin.logs.transactions') }}" class="btn btn-outline-primary">سجل المعاملات المالية</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>المستخدم</th>
                        <th>عنوان IP</th>
                        <th>المصدر</th>
                        <th>HWID</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loginHistory as $login)
                    <tr>
                        <td>{{ $login->id }}</td>
                        <td>
                            @if($login->user)
                                <a href="{{ route('admin.users.show', $login->user_id) }}">
                                    {{ $login->user->username }}
                                </a>
                            @else
                                <span class="text-muted">غير معروف</span>
                            @endif
                        </td>
                        <td>{{ $login->ip_address }}</td>
                        <td>
                            @if($login->source == 'web')
                                <span class="badge bg-primary">ويب</span>
                            @else
                                <span class="badge bg-info">تطبيق</span>
                            @endif
                        </td>
                        <td><small>{{ Str::limit($login->hwid, 20) }}</small></td>
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
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $loginHistory->links() }}
        </div>
    </div>
</div>
@endsection