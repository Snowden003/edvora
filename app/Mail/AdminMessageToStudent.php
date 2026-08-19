<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMessageToStudent extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $student,
        public string $subject,
        public string $body
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.message-to-student',
            with: [
                'student' => $this->student,
                'subject' => $this->subject,
                'body'    => $this->body,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
