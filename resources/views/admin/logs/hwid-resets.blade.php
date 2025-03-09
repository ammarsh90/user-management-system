@extends('layouts.admin')

@section('title', 'سجل إعادة تعيين HWID')

@section('content')
<div class="mb-3">
    <div class="btn-group" role="group">
        <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-primary">سجلات النظام</a>
        <a href="{{ route('admin.logs.login-history') }}" class="btn btn-outline-primary">سجل تسجيل الدخول</a>
        <a href="{{ route('admin.logs.hwid-resets') }}" class="btn btn-primary active">سجل إعادة تعيين HWID</a>
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
                        <th>الـ HWID القديم</th>
                        <th>الـ HWID الجديد</th>
                        <th>تم بواسطة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hwidResets as $reset)
                    <tr>
                        <td>{{ $reset->id }}</td>
                        <td>
                            @if($reset->user)
                                <a href="{{ route('admin.users.show', $reset->user_id) }}">
                                    {{ $reset->user->username }}
                                </a>
                            @else
                                <span class="text-muted">غير معروف</span>
                            @endif
                        </td>
                        <td><small>{{ Str::limit($reset->old_hwid, 20) }}</small></td>
                        <td><small>{{ Str::limit($reset->new_hwid ?? 'قيد الانتظار', 20) }}</small></td>
                        <td>
                            @if($reset->resetBy)
                                <a href="{{ route('admin.users.show', $reset->reset_by) }}">
                                    {{ $reset->resetBy->username }}
                                </a>
                            @else
                                <span class="text-muted">غير معروف</span>
                            @endif
                        </td>
                        <td>{{ $reset->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $hwidResets->links() }}
        </div>
    </div>
</div>
@endsection
