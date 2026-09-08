<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public string $mailSubject,
        public string $mailBody,
        public ?string $bannerUrl = null,
        public ?string $bannerImage = null,
        public ?string $preheader = null,
        public ?string $ctaText = null,
        public ?string $ctaUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address', 'support@edvoratech.com'), config('mail.from.name', 'Edvora Tech')),
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broadcast',
            with: [
                'recipient'   => $this->recipient,
                'subject'     => $this->mailSubject,
                'body'        => $this->mailBody,
                'bannerUrl'   => $this->bannerUrl,
                'bannerImage' => $this->bannerImage,
                'preheader'   => $this->preheader,
                'ctaText'     => $this->ctaText,
                'ctaUrl'      => $this->ctaUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
