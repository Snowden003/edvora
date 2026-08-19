<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMessageToTeacher extends Mailable
{
    use Queueable, SerializesModels;

    public User $teacher;
    public string $subjectLine;
    public string $messageBody;

    /**
     * Create a new message instance.
     */
    public function __construct(User $teacher, string $subject, string $body)
    {
        $this->teacher     = $teacher;
        $this->subjectLine = $subject;
        $this->messageBody = $body;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.message-to-teacher',
            with: [
                'teacher' => $this->teacher,
                'subject' => $this->subjectLine,
                'body'    => $this->messageBody,
            ],
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
