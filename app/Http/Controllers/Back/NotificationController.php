<?php

namespace App\Http\Controllers\Back;
use App\Http\Controllers\Controller;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;

class NotificationController extends Controller
{
    public static function sendNotification($title, $message, $fcmId)
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')));

        $messaging = $factory->createMessaging();

        $deviceToken = $fcmId;

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $message))
            ->withData([
                'sound' => 'default'
            ])
            ->withAndroidConfig(AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    'color' => '#0082a3',
                    'icon' => 'notification_icon'
                ],
            ]));

        try {
            $messaging->send($message);
            return 'Notification sent successfully';
        } catch (\Throwable $e) {
            return 'Error sending notification: ' . $e->getMessage();
        }
    }
}