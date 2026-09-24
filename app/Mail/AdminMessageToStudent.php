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

    public User $student;
    public string $messageSubject;
    public string $messageBody;

    public function __construct(
        User $student,
        string $subject,
        string $body
    ) {
        $this->student = $student;
        $this->messageSubject = $subject;
        $this->messageBody = $body;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->messageSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.message-to-student',
            with: [
                'student' => $this->student,
                'subject' => $this->messageSubject,
                'body'    => $this->messageBody,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
