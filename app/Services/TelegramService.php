<?php
// app/Services/TelegramService.php

namespace App\Services;

use App\Models\TelegramSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * إرسال إشعار إلى مجموعة تلجرام المحددة.
     *
     * @param string $eventType نوع الحدث (login, registration, subscription, hwid_reset, etc.)
     * @param array $data البيانات المستخدمة في رسالة الإشعار
     * @return bool نجاح أو فشل عملية الإرسال
     */
    public function sendNotification(string $eventType, array $data): bool
    {
        // الحصول على إعدادات تلجرام للحدث المحدد
        $settings = TelegramSetting::where('event_type', $eventType)
            ->where('is_active', true)
            ->first();

        // إذا لم تكن الإعدادات موجودة أو نشطة، يتم الخروج
        if (!$settings) {
            return false;
        }

        // استخراج الرمز وID المجموعة
        $botToken = $settings->bot_token;
        $chatId = $settings->chat_id;

        // تحضير الرسالة باستخدام القالب
        $message = $this->prepareMessage($settings->message_template, $data);

        try {
            // إرسال الرسالة باستخدام واجهة برمجة تطبيقات تلجرام
            $response = Http::get("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            // التحقق من نجاح الإرسال
            if ($response->successful() && $response->json('ok')) {
                return true;
            }

            // تسجيل الخطأ إذا فشل الإرسال
            Log::error('Telegram notification failed', [
                'event_type' => $eventType,
                'response' => $response->json(),
                'chat_id' => $chatId
            ]);

            return false;
        } catch (\Exception $e) {
            // تسجيل الاستثناء
            Log::error('Telegram notification exception', [
                'event_type' => $eventType,
                'message' => $e->getMessage(),
                'chat_id' => $chatId
            ]);

            return false;
        }
    }

    /**
     * تحضير رسالة الإشعار باستخدام قالب ومعلومات.
     *
     * @param string $template قالب الرسالة
     * @param array $data البيانات للاستبدال في القالب
     * @return string الرسالة النهائية
     */
    private function prepareMessage(string $template, array $data): string
    {
        // إذا كان القالب فارغًا، استخدم قالبًا افتراضيًا
        if (empty($template)) {
            $template = $this->getDefaultTemplate();
        }

        // استبدال المتغيرات في القالب بالقيم الفعلية
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        // إضافة طابع زمني
        $timestamp = now()->format('Y-m-d H:i:s');
        $template = str_replace('{timestamp}', $timestamp, $template);

        return $template;
    }

    /**
     * الحصول على قالب افتراضي للرسائل.
     *
     * @return string القالب الافتراضي
     */
    private function getDefaultTemplate(): string
    {
        return "⚡ <b>Notification</b> ⚡\n\n"
            . "Time: {timestamp}\n"
            . "User: {user}\n"
            . "Email: {email}\n"
            . "Action: {action}\n"
            . "------------------\n";
    }

    /**
     * إنشاء إعدادات تلجرام افتراضية.
     */
    public function createDefaultSettings(): void
    {
        $defaultEvents = [
            'login_success' => [
                'template' => "🟢 <b>Login Success</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "IP: {ip}\n"
                    . "HWID: {hwid}"
            ],
            'login_failed' => [
                'template' => "🔴 <b>Login Failed</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "IP: {ip}\n"
                    . "HWID: {hwid}"
            ],
            'registration' => [
                'template' => "👤 <b>New User Registration</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "IP: {ip}"
            ],
            'subscription_created' => [
                'template' => "🎫 <b>Subscription Created</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "Type: {subscription_type}\n"
                    . "Duration: {duration} days\n"
                    . "End Date: {end_date}\n"
                    . "Seller: {seller}"
            ],
            'subscription_status_changed' => [
                'template' => "🔄 <b>Subscription Status Changed</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "Type: {subscription_type}\n"
                    . "Status: {status}\n"
                    . "Admin: {admin}"
            ],
            'subscription_extended' => [
                'template' => "⏱️ <b>Subscription Extended</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "Type: {subscription_type}\n"
                    . "Days Added: {days_added}\n"
                    . "New End Date: {new_end_date}\n"
                    . "Admin: {admin}"
            ],
            'hwid_reset' => [
                'template' => "🔑 <b>HWID Reset</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "Old HWID: {old_hwid}\n"
                    . "New HWID: {new_hwid}\n"
                    . "Reset Type: {reset_type}"
            ],
            'credit_added' => [
                'template' => "💰 <b>Credit Added</b>\n\n"
                    . "Time: {timestamp}\n"
                    . "User: {user}\n"
                    . "Email: {email}\n"
                    . "Amount: {amount}\n"
                    . "Current Balance: {balance}\n"
                    . "Added By: {admin}"
            ],
        ];

        // إنشاء إعدادات افتراضية إذا لم تكن موجودة
        foreach ($defaultEvents as $eventType => $data) {
            TelegramSetting::firstOrCreate(
                ['event_type' => $eventType],
                [
                    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
                    'chat_id' => env('TELEGRAM_CHAT_ID', ''),
                    'is_active' => true,
                    'message_template' => $data['template']
                ]
            );
        }
    }
}