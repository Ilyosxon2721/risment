<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $chatId;
    protected bool $enabled;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', '');
        $this->chatId = config('services.telegram.chat_id', '');
        $this->enabled = config('services.telegram.enabled', false);
    }

    /**
     * Send a message to Telegram
     */
    public function send(string $message): bool
    {
        if (!$this->enabled || empty($this->botToken) || empty($this->chatId)) {
            Log::info('Telegram notification skipped: not configured');
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                Log::info('Telegram notification sent successfully');
                return true;
            }

            Log::error('Telegram notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Telegram notification exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send notification about new ticket
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
     * Send notification about new lead
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
     * Send notification about new payment
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
