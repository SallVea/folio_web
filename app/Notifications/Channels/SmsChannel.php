<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $number = method_exists($notifiable, 'routeNotificationForSms')
            ? $notifiable->routeNotificationForSms($notification)
            : ($notifiable->phone ?? null);

        if (empty($number)) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $driver = config('services.sms.driver', 'log');
        $webhook = config('services.sms.webhook');

        if ($driver === 'webhook' && $webhook) {
            Http::withToken(config('services.sms.token'))
                ->timeout(10)
                ->post($webhook, [
                    'to' => $number,
                    'message' => $message,
                ]);

            return;
        }

        Log::info('SMS terkirim (driver log)', [
            'to' => $number,
            'message' => $message,
        ]);
    }
}
