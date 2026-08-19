<x-mail::message>
# Hello {{ $user->name }},

Your teacher account on **Edvora Tech** has been reviewed and is now active.

<x-mail::panel>
You can now log in to your dashboard to create courses, manage students, and run live sessions.
</x-mail::panel>

## Getting started

- **Create Courses** — Build and publish your own courses
- **Manage Students** — Review enrollment requests and track student progress
- **Host Live Classes** — Schedule and run live sessions
- **Track Performance** — View ratings, reviews, and attendance reports

<x-mail::button :url="config('app.url') . '/teacher/dashboard'" color="primary">
Go to Dashboard
</x-mail::button>

If you have any questions, contact our support team.

Regards,<br>
**The Edvora Tech Team**

---
*This email was sent to {{ $user->email }} regarding your teacher account on Edvora Tech.*
</x-mail::message>
