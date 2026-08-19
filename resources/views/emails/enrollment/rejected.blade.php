<x-mail::message>
# Hello, {{ $student->name }}

We regret to inform you that your enrollment request for **{{ $course->title }}** has not been approved at this time.

<x-mail::panel>
**Reason from the instructor:**

{{ $reason }}
</x-mail::panel>

Don't be discouraged! You can explore other courses or reach out to the instructor for more information.

<x-mail::button :url="config('app.url') . '/courses'">
Browse Courses
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
