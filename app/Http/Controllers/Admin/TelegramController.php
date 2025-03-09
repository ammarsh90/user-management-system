<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelegramSetting;
use App\Models\SystemLog;
use App\Services\TelegramService;

class TelegramController extends Controller
{
    public function index()
    {
        $settings = TelegramSetting::all();
        return view('admin.telegram.index', compact('settings'));
    }

    public function create()
    {
        $eventTypes = TelegramSetting::getEventTypes();
        return view('admin.telegram.create', compact('eventTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'chat_id' => 'required|string|max:255',
            'event_type' => 'required|string|in:login,registration,subscription,hwid_reset,credit,system',
            'is_active' => 'boolean'
        ]);

        $setting = TelegramSetting::create([
            'name' => $request->name,
            'chat_id' => $request->chat_id,
            'event_type' => $request->event_type,
            'is_active' => $request->has('is_active')
        ]);

        // Log action
        $this->logSystemAction($request->user()->id, 'telegram_setting_create', "Telegram setting {$setting->name} created", $request->ip());

        return redirect()
            ->route('admin.telegram.index')
            ->with('success', 'تم إنشاء إعداد تلغرام بنجاح!');
    }

    public function edit($id)
    {
        $setting = TelegramSetting::findOrFail($id);
        $eventTypes = TelegramSetting::getEventTypes();
        return view('admin.telegram.edit', compact('setting', 'eventTypes'));
    }

    public function update(Request $request, $id)
    {
        $setting = TelegramSetting::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'chat_id' => 'required|string|max:255',
            'event_type' => 'required|string|in:login,registration,subscription,hwid_reset,credit,system',
            'is_active' => 'boolean'
        ]);

        $setting->name = $request->name;
        $setting->chat_id = $request->chat_id;
        $setting->event_type = $request->event_type;
        $setting->is_active = $request->has('is_active');
        $setting->save();

        // Log action
        $this->logSystemAction($request->user()->id, 'telegram_setting_update', "Telegram setting {$setting->name} updated", $request->ip());

        return redirect()
            ->route('admin.telegram.index')
            ->with('success', 'تم تحديث إعداد تلغرام بنجاح!');
    }

    public function destroy(Request $request, $id)
    {
        $setting = TelegramSetting::findOrFail($id);
        $name = $setting->name;
        $setting->delete();

        // Log action
        $this->logSystemAction($request->user()->id, 'telegram_setting_delete', "Telegram setting {$name} deleted", $request->ip());

        return redirect()
            ->route('admin.telegram.index')
            ->with('success', 'تم حذف إعداد تلغرام بنجاح!');
    }

    public function toggleStatus(Request $request, $id)
    {
        $setting = TelegramSetting::findOrFail($id);
        $setting->is_active = !$setting->is_active;
        $setting->save();

        // Log action
        $status = $setting->is_active ? 'activated' : 'deactivated';
        $this->logSystemAction($request->user()->id, 'telegram_setting_toggle', "Telegram setting {$setting->name} {$status}", $request->ip());

        return redirect()
            ->route('admin.telegram.index')
            ->with('success', 'تم تغيير حالة إعداد تلغرام بنجاح!');
    }

    public function testNotification(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string'
        ]);

        try {
            // Send test notification
            $message = "<b>🔔 اختبار الإشعارات</b>\n\nهذه رسالة اختبار للتأكد من عمل إشعارات تلغرام بشكل صحيح.";
            TelegramService::sendDirectNotification($request->chat_id, $message);
            
            // Log action
            $this->logSystemAction($request->user()->id, 'telegram_test', "Telegram test notification sent to {$request->chat_id}", $request->ip());
            
            return redirect()
                ->back()
                ->with('success', 'تم إرسال إشعار الاختبار بنجاح!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'فشل إرسال إشعار الاختبار: ' . $e->getMessage());
        }
    }

    private function logSystemAction($userId, $action, $description, $ipAddress)
    {
        SystemLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ipAddress
        ]);
    }
}