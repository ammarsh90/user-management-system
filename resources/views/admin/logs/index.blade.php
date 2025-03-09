@extends('layouts.admin')

@section('title', 'سجلات النظام')

@section('content')
<div class="mb-3">
    <div class="btn-group" role="group">
        <a href="{{ route('admin.logs.index') }}" class="btn btn-primary active">سجلات النظام</a>
        <a href="{{ route('admin.logs.login-history') }}" class="btn btn-outline-primary">سجل تسجيل الدخول</a>
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
                        <th>الإجراء</th>
                        <th>الوصف</th>
                        <th>عنوان IP</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>
                            @if($log->user)
                                <a href="{{ route('admin.users.show', $log->user_id) }}">
                                    {{ $log->user->username }}
                                </a>
                            @else
                                <span class="text-muted">غير معروف</span>
                            @endif
                        </td>
                        <td>
                            @switch(explode('_', $log->action)[0])
                                @case('user')
                                    <span class="badge bg-primary">المستخدمين</span>
                                    @break
                                @case('subscription')
                                    <span class="badge bg-success">الاشتراكات</span>
                                    @break
                                @case('hwid')
                                    <span class="badge bg-warning">HWID</span>
                                    @break
                                @case('credits')
                                    <span class="badge bg-info">الكريدت</span>
                                    @break
                                @case('telegram')
                                    <span class="badge bg-secondary">تلغرام</span>
                                    @break
                                @default
                                    <span class="badge bg-dark">{{ $log->action }}</span>
                            @endswitch
                        </td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection