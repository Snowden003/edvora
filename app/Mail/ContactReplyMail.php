<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->contactMessage->subject . ' — Edvora Tech',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
            with: [
                'senderName'     => $this->contactMessage->name,
                'originalMessage'=> $this->contactMessage->message,
                'adminReply'     => $this->contactMessage->admin_reply,
                'subject'        => $this->contactMessage->subject,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
