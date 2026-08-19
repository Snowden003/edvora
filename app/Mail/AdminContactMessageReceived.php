<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name ?: $this->contactMessage->email)],
            subject: 'New Contact Message: ' . $this->contactMessage->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.contact-message-received',
            with: ['contactMessage' => $this->contactMessage],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
