<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $chatId;
    protected bool $enabled;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token') ?: config('services.telegram.bot_token', '');
        $this->chatId = config('services.telegram.chat_id', '');
        $this->enabled = config('services.telegram.enabled', false);
    }

    /**
     * Send a message to the default admin Telegram chat.
     */
    public function send(string $message): bool
    {
        if (!$this->enabled || empty($this->botToken) || empty($this->chatId)) {
            Log::info('Telegram notification skipped: not configured');
            return false;
        }

        return $this->sendMessage($this->chatId, $message);
    }

    /**
     * Send a message to a specific chat_id via Telegram Bot API.
     */
    public function sendMessage(string $chatId, string $message): bool
    {
        $token = $this->botToken;

        if (empty($token) || empty($chatId)) {
            Log::info('Telegram sendMessage skipped: missing token or chat_id');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                Log::info('Telegram message sent', ['chat_id' => $chatId]);
                return true;
            }

            Log::error('Telegram message failed', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Telegram message exception', [
                'chat_id' => $chatId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a formatted notification to a company's Telegram chat.
     * Checks if the company has the given event type enabled.
     */
    public function sendNotification(Company $company, string $eventType, string $message): bool
    {
        $chatId = $company->telegram_chat_id;
        if (empty($chatId)) {
            return false;
        }

        $settings = $company->telegram_settings ?? [];
        $enabled = $settings['enabled'] ?? false;
        $events = $settings['events'] ?? [];

        if (!$enabled || !($events[$eventType] ?? false)) {
            return false;
        }

        $formatted = $this->formatNotification($eventType, $message);
        return $this->sendMessage($chatId, $formatted);
    }

    /**
     * Format a notification message with emoji based on event type.
     */
    protected function formatNotification(string $eventType, string $message): string
    {
        $config = self::eventTypeConfig();
        $icon = $config[$eventType]['icon'] ?? '🔔';
        $title = $config[$eventType]['title'] ?? __('Notification');

        return "{$icon} <b>{$title}</b>\n\n{$message}\n\n🏢 RISMENT";
    }

    /**
     * Send a test notification to verify the connection.
     */
    public function sendTestNotification(string $chatId): bool
    {
        $message = "✅ <b>" . __('Test notification from RISMENT') . "</b>\n\n";
        $message .= __('Telegram notifications are configured successfully!') . "\n\n";
        $message .= "🏢 RISMENT";

        return $this->sendMessage($chatId, $message);
    }

    /**
     * Get available event types with labels.
     */
    public static function eventTypes(): array
    {
        $config = self::eventTypeConfig();
        $result = [];
        foreach ($config as $key => $data) {
            $result[$key] = $data['label'];
        }
        return $result;
    }

    /**
     * Event type configuration: icon, title (for message header), label (for settings UI).
     */
    protected static function eventTypeConfig(): array
    {
        return [
            'new_invoice' => [
                'icon'  => '📄',
                'title' => __('New Invoice'),
                'label' => __('New invoice issued'),
            ],
            'payment_received' => [
                'icon'  => '💰',
                'title' => __('Payment Received'),
                'label' => __('Payment received'),
            ],
            'ticket_reply' => [
                'icon'  => '💬',
                'title' => __('Ticket Reply'),
                'label' => __('Reply to your ticket'),
            ],
            'shipment_status' => [
                'icon'  => '📦',
                'title' => __('Shipment Status Update'),
                'label' => __('Shipment status changed'),
            ],
            'subscription_expiring' => [
                'icon'  => '⚠️',
                'title' => __('Subscription Expiring'),
                'label' => __('Subscription expiring soon'),
            ],
        ];
    }

    // ── Admin notification helpers (existing) ───────────────────────────

    /**
     * Send notification about new ticket (to admin chat)
     */
    public function notifyNewTicket($ticket): bool
    {
        $message = "🎫 <b>Новый тикет #{$ticket->id}</b>\n\n";
        $message .= "📋 <b>Тема:</b> {$ticket->subject}\n";
        $message .= "🏢 <b>Компания:</b> {$ticket->company->name}\n";
        $message .= "👤 <b>Пользователь:</b> {$ticket->user->name}\n";
        $message .= "⚡ <b>Приоритет:</b> " . $this->formatPriority($ticket->priority) . "\n\n";
        $message .= "🔗 <a href=\"" . url('/admin/tickets/' . $ticket->id . '/edit') . "\">Открыть в админке</a>";

        return $this->send($message);
    }

    /**
     * Send notification about new lead (to admin chat)
     */
    public function notifyNewLead($lead): bool
    {
        $message = "📥 <b>Новая заявка!</b>\n\n";
        $message .= "👤 <b>Имя:</b> {$lead->name}\n";

        if ($lead->company_name) {
            $message .= "🏢 <b>Компания:</b> {$lead->company_name}\n";
        }
        if ($lead->phone) {
            $message .= "📞 <b>Телефон:</b> {$lead->phone}\n";
        }
        if ($lead->email) {
            $message .= "📧 <b>Email:</b> {$lead->email}\n";
        }
        $message .= "📍 <b>Источник:</b> {$lead->source}\n\n";
        $message .= "🔗 <a href=\"" . url('/admin/leads/' . $lead->id . '/edit') . "\">Открыть в админке</a>";

        return $this->send($message);
    }

    /**
     * Send notification about new payment (to admin chat)
     */
    public function notifyNewPayment($payment): bool
    {
        $amount = number_format($payment->amount, 0, '', ' ');

        $message = "💰 <b>Новый платёж!</b>\n\n";
        $message .= "🏢 <b>Компания:</b> {$payment->company->name}\n";
        $message .= "💵 <b>Сумма:</b> {$amount} UZS\n";
        $message .= "💳 <b>Метод:</b> {$payment->payment_method}\n";
        $message .= "📄 <b>Счёт:</b> #{$payment->invoice_id}\n";

        return $this->send($message);
    }

    /**
     * Format priority for display
     */
    protected function formatPriority(string $priority): string
    {
        return match ($priority) {
            'high' => '🔴 Высокий',
            'medium' => '🟡 Средний',
            'low' => '🟢 Низкий',
            default => $priority,
        };
    }
}
