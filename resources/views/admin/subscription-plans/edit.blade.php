@extends('layouts.admin')

@section('title', 'تعديل خطة اشتراك: ' . $plan->name)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> العودة للقائمة
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.subscription-plans.update', $plan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم الخطة</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="duration_months" class="form-label">المدة (بالشهور)</label>
                    <input type="number" class="form-control @error('duration_months') is-invalid @enderror" id="duration_months" name="duration_months" value="{{ old('duration_months', $plan->duration_months) }}" required min="1">
                    @error('duration_months')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="price" class="form-label">السعر</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $plan->price) }}" required min="0">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $plan->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection