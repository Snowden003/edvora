<x-mail::message>
# Hello {{ $user->name }},

Thank you for submitting your teacher application on **Edvora Tech**. We have received your application and our team will review it shortly.

<x-mail::panel>
**Step 1 — Application Received**
Your profile and documents have been submitted successfully.

**Step 2 — Under Review**
Our team will review your qualifications and expertise. This usually takes 1–2 business days.

**Step 3 — Email Notification**
You will receive an email once a decision has been made on your application.

**Step 4 — Start Teaching**
Once your account is activated, you can log in and start creating courses.
</x-mail::panel>

If you have any questions, please contact our support team.

<x-mail::button :url="config('app.url')" color="primary">
Visit Edvora Tech
</x-mail::button>

Regards,<br>
**The Edvora Tech Team**

---
*This email was sent to {{ $user->email }} because a teacher application was submitted on Edvora Tech.*
</x-mail::message>
