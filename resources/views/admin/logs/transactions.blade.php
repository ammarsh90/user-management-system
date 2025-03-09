@extends('layouts.admin')

@section('title', 'سجل المعاملات المالية')

@section('content')
<div class="mb-3">
    <div class="btn-group" role="group">
        <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-primary">سجلات النظام</a>
        <a href="{{ route('admin.logs.login-history') }}" class="btn btn-outline-primary">سجل تسجيل الدخول</a>
        <a href="{{ route('admin.logs.hwid-resets') }}" class="btn btn-outline-primary">سجل إعادة تعيين HWID</a>
        <a href="{{ route('admin.logs.transactions') }}" class="btn btn-primary active">سجل المعاملات المالية</a>
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
                        <th>المبلغ</th>
                        <th>نوع المعاملة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>
                            @if($transaction->user)
                                <a href="{{ route('admin.users.show', $transaction->user_id) }}">
                                    {{ $transaction->user->username }}
                                </a>
                            @else
                                <span class="text-muted">غير معروف</span>
                            @endif
                        </td>
                        <td>{{ $transaction->amount }} {{ $transaction->currency }}</td>
                        <td>
                            @if($transaction->type == 'credit')
                                <span class="badge bg-success">إضافة</span>
                            @elseif($transaction->type == 'debit')
                                <span class="badge bg-danger">خصم</span>
                            @else
                                <span class="badge bg-info">غير محدد</span>
                            @endif
                        </td>
                        <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
