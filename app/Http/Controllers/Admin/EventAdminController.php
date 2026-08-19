<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InstructorEventAssigned;
use App\Models\Event;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventAdminController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('start_date', 'desc')->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $teachers = Teacher::with('user')->get();
        return view('admin.events.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'location' => 'nullable|string|max:255',
            'google_maps_url' => ['nullable', 'string', 'max:2000', 'regex:/^https:\/\/www\.google\.com\/maps\/embed/i'],
            'type' => 'required|in:workshop,webinar,conference,meetup,seminar',
            'event_mode' => 'required|in:online,in-person',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'nullable|string|max:50',
            'presenter' => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'invitation_card_type' => 'nullable|in:text,image,pdf',
            'invitation_card_content' => 'nullable|string',
            'invitation_card_file' => 'nullable|file|max:5120',
            'workshop_details' => 'nullable|array',
            'workshop_details.instructor_type' => 'nullable|in:existing,custom',
            'workshop_details.instructor_id' => 'nullable|integer',
            'workshop_details.instructor_name' => 'nullable|string|max:255',
            'workshop_details.instructor_bio' => 'nullable|string',
            'workshop_details.completion_outcome' => 'nullable|string',
            'workshop_schedules' => 'nullable|array',
            'workshop_schedules.*.day' => 'required_with:workshop_schedules|string|max:50',
            'workshop_schedules.*.date' => 'required_with:workshop_schedules|date',
            'workshop_schedules.*.start_time' => 'required_with:workshop_schedules',
            'workshop_schedules.*.end_time' => 'required_with:workshop_schedules',
            'workshop_schedules.*.topics' => 'nullable|string',
        ]);

        // Validation: if in-person, google_maps_url is required
        if ($validated['event_mode'] === 'in-person' && empty($validated['google_maps_url'])) {
            return redirect()->back()->withErrors(['google_maps_url' => 'Google Maps URL is required for in-person events.'])->withInput();
        }

        $validated['slug'] = Str::slug($validated['title']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('events', 'public');
        }

        // Handle invitation card file upload
        if ($request->hasFile('invitation_card_file')) {
            $validated['invitation_card_file'] = $request->file('invitation_card_file')->store('invitations', 'public');
        }

        // Set default values
        $validated['registered_count'] = 0;

        // Process multiple presenters
        $allPresenters = [];

        // Existing teachers
        if ($request->has('presenters.existing')) {
            foreach ($request->presenters['existing'] as $teacherId) {
                $teacher = Teacher::with('user')->find($teacherId);
                if ($teacher && $teacher->user) {
                    $allPresenters[] = [
                        'type' => 'existing',
                        'id' => $teacher->id,
                        'name' => $teacher->user->name,
                        'email' => $teacher->user->email,
                        'specialization' => $teacher->specialization,
                    ];
                }
            }
        }

        // Custom presenters
        if ($request->has('presenters.custom')) {
            foreach ($request->presenters['custom'] as $index => $customPresenter) {
                if (!empty($customPresenter['name']) && !empty($customPresenter['email'])) {
                    $photoPath = null;
                    if ($request->hasFile("presenters.custom.{$index}.photo")) {
                        $photoPath = $request->file("presenters.custom.{$index}.photo")->store('instructors', 'public');
                    }

                    $allPresenters[] = [
                        'type' => 'custom',
                        'name' => $customPresenter['name'],
                        'email' => $customPresenter['email'],
                        'expertise' => $customPresenter['expertise'] ?? null,
                        'experience' => $customPresenter['experience'] ?? null,
                        'bio' => $customPresenter['bio'] ?? null,
                        'photo' => $photoPath,
                    ];
                }
            }
        }

        // Store presenters in workshop_details
        $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
            'presenters' => $allPresenters,
        ]);

        // Store workshop time in workshop_details
        if ($request->filled('workshop_start_time') || $request->filled('workshop_end_time')) {
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'workshop_time' => [
                    'start_time' => $request->workshop_start_time,
                    'end_time' => $request->workshop_end_time,
                ],
            ]);
        }

        // Store event time for ALL event types (workshop, webinar, conference, meetup)
        if ($request->filled('event_start_time') || $request->filled('event_end_time')) {
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'event_time' => [
                    'start_time' => $request->event_start_time,
                    'end_time' => $request->event_end_time,
                ],
            ]);
        }

        // Store seminar details
        if ($request->filled('seminar_details')) {
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'seminar_details' => $request->seminar_details,
            ]);
        }

        // Store what you will learn (array of items)
        if ($request->has('what_you_will_learn')) {
            $learningItems = array_filter($request->what_you_will_learn, function($item) {
                return !empty(trim($item));
            });
            if (!empty($learningItems)) {
                $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                    'what_you_will_learn' => array_values($learningItems),
                ]);
            }
        }

        // Store completion outcome
        if ($request->filled('workshop_completion_outcome')) {
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'completion_outcome' => $request->workshop_completion_outcome,
            ]);
        }

        // Store event features in workshop_details
        if ($request->has('event_features')) {
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'event_features' => $request->event_features,
            ]);
        }

        $event = Event::create($validated);

        // Handle workshop schedules
        if ($request->has('workshop_schedules') && $event->type === 'workshop') {
            foreach ($request->workshop_schedules as $schedule) {
                $event->workshopSchedules()->create($schedule);
            }
        }

        // Send email notification to instructor if enabled
        if ($request->boolean('notify_instructor')) {
            $this->sendInstructorNotification($event, $request);
        }

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully!');
    }

    public function edit(Event $event)
    {
        $teachers = Teacher::all();
        $event->load('workshopSchedules');
        return view('admin.events.edit', compact('event', 'teachers'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'location' => 'nullable|string|max:255',
            'google_maps_url' => ['nullable', 'string', 'max:2000', 'regex:/^https:\/\/www\.google\.com\/maps\/embed/i'],
            'type' => 'required|in:workshop,webinar,conference,meetup,seminar',
            'event_mode' => 'required|in:online,in-person',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration' => 'nullable|string|max:50',
            'presenter' => 'nullable|string|max:255',
            'max_attendees' => 'nullable|integer|min:1',
            'registered_count' => 'nullable|integer|min:0',
            'status' => 'required|in:active,closed,cancelled',
            'invitation_card_type' => 'nullable|in:text,image,pdf',
            'invitation_card_content' => 'nullable|string',
            'invitation_card_file' => 'nullable|file|max:5120',
            'workshop_details' => 'nullable|array',
            'workshop_details.instructor_type' => 'nullable|in:existing,custom',
            'workshop_details.instructor_id' => 'nullable|integer',
            'workshop_details.instructor_name' => 'nullable|string|max:255',
            'workshop_details.instructor_bio' => 'nullable|string',
            'workshop_details.completion_outcome' => 'nullable|string',
            'workshop_schedules' => 'nullable|array',
            'workshop_schedules.*.day' => 'required_with:workshop_schedules|string|max:50',
            'workshop_schedules.*.topics' => 'nullable|string',
            'workshop_schedules.*.description' => 'nullable|string',
            'workshop_start_time' => 'nullable',
            'workshop_end_time' => 'nullable',
            'workshop_completion_outcome' => 'nullable|string',
            'event_start_time' => 'nullable',
            'event_end_time' => 'nullable',
            'seminar_start_time' => 'nullable',
            'seminar_end_time' => 'nullable',
            'seminar_details' => 'nullable|string',
        ]);

        // Validation: if in-person, google_maps_url is required
        if ($validated['event_mode'] === 'in-person' && empty($validated['google_maps_url'])) {
            return redirect()->back()->withErrors(['google_maps_url' => 'Google Maps URL is required for in-person events.'])->withInput();
        }

        // Update slug if title changed
        if ($event->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('events', 'public');
        }

        // Handle invitation card file upload
        if ($request->hasFile('invitation_card_file')) {
            $validated['invitation_card_file'] = $request->file('invitation_card_file')->store('invitations', 'public');
        }

        // Handle custom presenter photo upload
        if ($request->hasFile('custom_presenter.photo')) {
            $validated['custom_presenter']['photo'] = $request->file('custom_presenter.photo')->store('instructors', 'public');
        }

        // Store presenter info in workshop_details
        if ($request->filled('presenter_id')) {
            $teacher = Teacher::find($request->presenter_id);
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'presenter_type' => 'existing',
                'presenter_id' => $request->presenter_id,
                'presenter_name' => $teacher->name,
                'presenter_specialization' => $teacher->specialization,
            ]);
        } elseif ($request->filled('custom_presenter.name')) {
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'presenter_type' => 'custom',
                'custom_presenter' => $validated['custom_presenter'] ?? [],
            ]);
        }

        // Store what you will learn (array of items)
        if ($request->has('what_you_will_learn')) {
            $learningItems = array_filter($request->what_you_will_learn, function($item) {
                return !empty(trim($item));
            });
            if (!empty($learningItems)) {
                $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                    'what_you_will_learn' => array_values($learningItems),
                ]);
            }
        }

        // Store completion outcome
        if ($request->filled('workshop_completion_outcome')) {
            $validated['workshop_details'] = array_merge($validated['workshop_details'] ?? [], [
                'completion_outcome' => $request->workshop_completion_outcome,
            ]);
        }

        $event->update($validated);

        // Handle workshop schedules - delete old and create new
        if ($request->has('workshop_schedules') && $event->type === 'workshop') {
            $event->workshopSchedules()->delete();
            foreach ($request->workshop_schedules as $schedule) {
                $event->workshopSchedules()->create($schedule);
            }
        }

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully!');
    }

    private function sendInstructorNotification(Event $event, Request $request)
    {
        // Get event features
        $eventFeatures = [];
        $featureLabels = [
            'certificate' => 'Certificate of Completion',
            'recording' => 'Recorded Sessions',
            'materials' => 'Digital Materials',
            'networking' => 'Networking Opportunity',
            'live' => 'Live Streaming',
            'qa' => 'Q&A Session',
        ];

        if ($request->has('event_features')) {
            foreach ($request->event_features as $feature) {
                if (isset($featureLabels[$feature])) {
                    $eventFeatures[] = $featureLabels[$feature];
                }
            }
        }

        // Send to existing teachers
        if ($request->has('presenters.existing')) {
            foreach ($request->presenters['existing'] as $teacherId) {
                $teacher = Teacher::with('user')->find($teacherId);
                if ($teacher && $teacher->user) {
                    $instructorEmail = $teacher->user->email;
                    $instructorName = $teacher->user->name;
                    $instructorType = 'existing';

                    Mail::to($instructorEmail)->send(new InstructorEventAssigned($event, $instructorName, $instructorType, $eventFeatures));
                }
            }
        }

        // Send to custom presenters
        if ($request->has('presenters.custom')) {
            foreach ($request->presenters['custom'] as $customPresenter) {
                if (!empty($customPresenter['email']) && !empty($customPresenter['name'])) {
                    $instructorEmail = $customPresenter['email'];
                    $instructorName = $customPresenter['name'];
                    $instructorType = 'custom';

                    Mail::to($instructorEmail)->send(new InstructorEventAssigned($event, $instructorName, $instructorType, $eventFeatures));
                }
            }
        }
    }

    public function destroy(Event $event)
    {
        // Delete thumbnail file if exists
        if ($event->thumbnail) {
            Storage::disk('public')->delete($event->thumbnail);
        }

        // Delete invitation card file if exists
        if ($event->invitation_card_file) {
            Storage::disk('public')->delete($event->invitation_card_file);
        }

        // Delete presenter photos from workshop_details
        if (isset($event->workshop_details['presenters'])) {
            foreach ($event->workshop_details['presenters'] as $presenter) {
                if (isset($presenter['photo']) && $presenter['photo']) {
                    Storage::disk('public')->delete($presenter['photo']);
                }
            }
        }

        // Delete related registrations
        $event->registrations()->delete();

        // Delete workshop schedules
        $event->workshopSchedules()->delete();

        // Delete the event
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event and all related data deleted successfully!');
    }

    /**
     * Download event registrations as PDF
     */
    public function downloadRegistrationsPdf(Event $event)
    {
        // Load registrations with user details
        $registrations = $event->registrations()
            ->with('user')
            ->where('status', 'registered')
            ->orderBy('created_at', 'desc')
            ->get();

        // Generate PDF
        $pdf = \PDF::loadView('admin.events.registrations-pdf', [
            'event' => $event,
            'registrations' => $registrations,
            'actualCount' => $registrations->count(),
            'generatedAt' => now()->format('F d, Y H:i:s'),
        ]);

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $filename = 'event-registrations-' . $event->slug . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
