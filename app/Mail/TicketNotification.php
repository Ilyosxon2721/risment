<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public string $actionType; // 'created', 'replied', 'closed'

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, string $actionType = 'created')
    {
        $this->ticket = $ticket;
        $this->actionType = $actionType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->actionType) {
            'created' => "Новый тикет #{$this->ticket->id}: {$this->ticket->subject}",
            'replied' => "Ответ на тикет #{$this->ticket->id}",
            'closed' => "Тикет #{$this->ticket->id} закрыт",
            default => "Тикет #{$this->ticket->id}",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
