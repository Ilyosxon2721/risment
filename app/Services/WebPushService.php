<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    /**
     * Send push notification to a user's subscriptions.
     */
    public function sendToUser(User $user, array $payload): void
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        foreach ($subscriptions as $subscription) {
            try {
                $this->sendNotification($subscription, $payload);
            } catch (\Exception $e) {
                Log::warning('Push notification failed', [
                    'user_id' => $user->id,
                    'endpoint' => $subscription->endpoint,
                    'error' => $e->getMessage(),
                ]);

                // Remove invalid subscriptions (410 Gone or 404)
                if (str_contains($e->getMessage(), '410') || str_contains($e->getMessage(), '404')) {
                    $subscription->delete();
                }
            }
        }
    }

    /**
     * Send push notification using Web Push protocol.
     * Uses the web-push-php library if available, otherwise queues for manual processing.
     */
    protected function sendNotification(PushSubscription $subscription, array $payload): void
    {
        // Check if minishlink/web-push is available
        if (class_exists(\Minishlink\WebPush\WebPush::class)) {
            $this->sendViaWebPush($subscription, $payload);
            return;
        }

        // Fallback: log the notification for manual processing
        Log::info('Push notification queued (web-push library not installed)', [
            'endpoint' => $subscription->endpoint,
            'payload' => $payload,
        ]);
    }

    protected function sendViaWebPush(PushSubscription $subscription, array $payload): void
    {
        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];

        $webPush = new \Minishlink\WebPush\WebPush($auth);

        $webPush->sendOneNotification(
            \Minishlink\WebPush\Subscription::create([
                'endpoint' => $subscription->endpoint,
                'publicKey' => $subscription->public_key,
                'authToken' => $subscription->auth_token,
                'contentEncoding' => $subscription->content_encoding ?? 'aesgcm',
            ]),
            json_encode($payload)
        );
    }
}
