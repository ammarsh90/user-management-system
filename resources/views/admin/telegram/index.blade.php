@extends('layouts.admin')

@section('title', 'إعدادات إشعارات تلغرام')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.telegram.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> إضافة إعداد جديد
    </a>
    
    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testNotificationModal">
        <i class="fas fa-check-circle"></i> اختبار الإشعارات
    </button>
</div>

<div class="alert alert-info">
    <h5>تعليمات إشعارات تلغرام</h5>
    <p>1. قم بإنشاء بوت تلغرام باستخدام <a href="https://t.me/BotFather" target="_blank">BotFather</a> واحصل على رمز الوصول (Bot Token).</p>
    <p>2. أضف رمز البوت إلى ملف <code>.env</code>: <code>TELEGRAM_BOT_TOKEN=your_token_here</code></p>
    <p>3. أضف البوت إلى المجموعة/القناة التي تريد إرسال الإشعارات إليها.</p>
    <p>4. احصل على معرّف المحادثة (Chat ID) باستخدام <a href="https://t.me/getidsbot" target="_blank">GetIDs Bot</a>.</p>
    <p>5. أضف إعدادات الإشعارات أدناه باستخدام معرّف المحادثة.</p>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>معرّف المحادثة</th>
                        <th>نوع الإشعار</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $setting)
                    <tr>
                        <td>{{ $setting->id }}</td>
                        <td>{{ $setting->name }}</td>
                        <td><code>{{ $setting->chat_id }}</code></td>
                        <td>
                            @switch($setting->event_type)
                                @case('login')
                                    <span class="badge bg-primary">تسجيل الدخول</span>
                                    @break
                                @case('registration')
                                    <span class="badge bg-success">التسجيل الجديد</span>
                                    @break
                                @case('subscription')
                                    <span class="badge bg-info">الاشتراكات</span>
                                    @break
                                @case('hwid_reset')
                                    <span class="badge bg-warning">إعادة تعيين HWID</span>
                                    @break
                                @case('credit')
                                    <span class="badge bg-secondary">الكريدت</span>
                                    @break
                                @case('system')
                                    <span class="badge bg-danger">أحداث النظام</span>
                                    @break
                                @default
                                    <span class="badge bg-dark">{{ $setting->event_type }}</span>
                            @endswitch
                        </td>
                        <td>
                            <form action="{{ route('admin.telegram.toggle', $setting->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $setting->is_active ? 'btn-success' : 'btn-secondary' }}">
                                    @if($setting->is_active)
                                        <i class="fas fa-check-circle"></i> نشط
                                    @else
                                        <i class="fas fa-times-circle"></i> غير نشط
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.telegram.edit', $setting->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $setting->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            
                            <!-- Modal for delete confirmation -->
                            <div class="modal fade" id="deleteModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من حذف إعداد تلغرام "{{ $setting->name }}"؟
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.telegram.destroy', $setting->id) }}" method="POST">
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

<!-- Test Notification Modal -->
<div class="modal fade" id="testNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اختبار إشعارات تلغرام</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.telegram.test') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>أدخل معرّف المحادثة (Chat ID) الذي تريد اختبار إرسال الإشعارات إليه.</p>
                    
                    <div class="mb-3">
                        <label for="chat_id" class="form-label">معرّف المحادثة</label>
                        <input type="text" class="form-control" id="chat_id" name="chat_id" required>
                        <div class="form-text">يمكنك استخدام معرّف محادثة موجود من الجدول أعلاه.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">اختبار</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection