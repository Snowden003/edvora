<x-mail::message>
# New Contact Message

A new message was submitted through the Edvora Tech contact form.

<x-mail::panel>
**From:** {{ $contactMessage->name ?: 'Not provided' }}

**Email:** {{ $contactMessage->email }}

**Phone:** {{ $contactMessage->phone ?: 'Not provided' }}

**Company:** {{ $contactMessage->company_name ?: 'Not provided' }}

**Category:** {{ ucfirst(str_replace('_', ' ', $contactMessage->category)) }}

**Priority:** {{ ucfirst($contactMessage->priority) }}

**Subject:** {{ $contactMessage->subject }}
</x-mail::panel>

### Message

{{ $contactMessage->message }}

<x-mail::button :url="url('/admin-panel/contact-messages')" color="primary">
Open Contact Messages
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
