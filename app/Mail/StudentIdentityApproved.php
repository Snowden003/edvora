<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentIdentityApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Your Identity Has Been Verified - Edvora',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student.identity-approved',
            with: ['user' => $this->user],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
