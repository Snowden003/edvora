<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminAiUnresolvedIssue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userQuestion,
        public string $issueSummary,
        public ?string $userContact = null,
        public ?string $userName = null,
        public ?string $userEmail = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address') ?? 'support@edvoratech.com', config('mail.from.name') ?? 'Edvora AI Assistant'),
            subject: '🔔 [Edvora AI] Unresolved User Issue Escalation',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.ai-unresolved-issue',
            with: [
                'userQuestion' => $this->userQuestion,
                'issueSummary' => $this->issueSummary,
                'userContact'  => $this->userContact,
                'userName'     => $this->userName,
                'userEmail'    => $this->userEmail,
                'timestamp'    => now()->format('Y-m-d H:i:s'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
