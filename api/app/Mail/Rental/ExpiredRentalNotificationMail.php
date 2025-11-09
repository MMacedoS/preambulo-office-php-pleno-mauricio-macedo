<?php

namespace App\Mail\Rental;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpiredRentalNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aviso: Sua locação está em atraso',
            to: [$this->emailData['usuario_email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rental.expired-notification',
            with: [
                'dados' => $this->emailData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
