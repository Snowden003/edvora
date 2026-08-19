<x-mail::message>
# New Teacher Registration

A new teacher has registered on **Edvora Tech**.

<x-mail::panel>
**Name:** {{ $user->name }}

**Email:** {{ $user->email }}

**Account status:** {{ ucfirst($user->status) }}

**Registered at:** {{ $user->created_at?->format('M d, Y - H:i') ?? now()->format('M d, Y - H:i') }}
</x-mail::panel>

<x-mail::button :url="url('/admin-panel/teacher-applications')" color="primary">
Open Teacher Applications
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
