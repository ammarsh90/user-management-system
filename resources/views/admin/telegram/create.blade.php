@extends('layouts.admin')

@section('title', 'إضافة إعداد تلغرام جديد')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.telegram.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> العودة للقائمة
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.telegram.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    <div class="form-text">اسم وصفي لتمييز هذا الإعداد (مثال: إشعارات المشرفين)</div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="chat_id" class="form-label">معرّف المحادثة (Chat ID)</label>
                    <input type="text" class="form-control @error('chat_id') is-invalid @enderror" id="chat_id" name="chat_id" value="{{ old('chat_id') }}" required>
                    <div class="form-text">معرّف المحادثة أو المجموعة أو القناة على تلغرام</div>
                    @error('chat_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="event_type" class="form-label">نوع الإشعار</label>
                    <select class="form-select @error('event_type') is-invalid @enderror" id="event_type" name="event_type" required>
                        <option value="">اختر نوع الإشعار</option>
                        @foreach($eventTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('event_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('event_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            تفعيل الإشعارات
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.telegram.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection