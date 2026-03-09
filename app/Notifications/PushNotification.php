<?php

namespace App\Notifications;

use App\Services\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $body,
        protected string $url = '/cabinet/dashboard',
        protected array $actions = [],
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
        ];
    }

    /**
     * Send as web push notification.
     */
    public function sendPush($notifiable): void
    {
        app(WebPushService::class)->sendToUser($notifiable, [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'actions' => $this->actions,
        ]);
    }
}
