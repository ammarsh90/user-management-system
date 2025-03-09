@extends('layouts.admin')

@section('title', 'خطط الاشتراك')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> إضافة خطة جديدة
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>المدة (بالشهور)</th>
                        <th>السعر</th>
                        <th>الوصف</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr>
                        <td>{{ $plan->id }}</td>
                        <td>{{ $plan->name }}</td>
                        <td>{{ $plan->duration_months }}</td>
                        <td>{{ $plan->price }}</td>
                        <td>{{ Str::limit($plan->description, 50) }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.subscription-plans.edit', $plan->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $plan->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            
                            <!-- Modal for delete confirmation -->
                            <div class="modal fade" id="deleteModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من حذف خطة الاشتراك "{{ $plan->name }}"؟
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.subscription-plans.destroy', $plan->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">حذف</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection