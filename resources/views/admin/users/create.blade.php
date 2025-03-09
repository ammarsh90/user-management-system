@extends('layouts.admin')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="role" class="form-label">الدور</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>مستخدم</option>
                        <option value="reseller" {{ old('role') == 'reseller' ? 'selected' : '' }}>بائع</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مشرف</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">الحالة</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>محظور</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="credits" class="form-label">الرصيد (الكريدت)</label>
                    <input type="number" step="0.01" class="form-control @error('credits') is-invalid @enderror" id="credits" name="credits" value="{{ old('credits', 0) }}" required>
                    @error('credits')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="hwid_auto_reset_hours" class="form-label">ساعات إعادة تعيين HWID التلقائية</label>
                    <input type="number" class="form-control @error('hwid_auto_reset_hours') is-invalid @enderror" id="hwid_auto_reset_hours" name="hwid_auto_reset_hours" value="{{ old('hwid_auto_reset_hours', 168) }}" required>
                    <div class="form-text">القيمة الافتراضية هي 168 ساعة (7 أيام)</div>
                    @error('hwid_auto_reset_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection