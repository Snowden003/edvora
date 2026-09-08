<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Mail\EventRegistrationConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $eventsQuery = Event::where('status', 'active')
            ->where(function ($q) {
                $q->where('start_date', '>=', now())
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('end_date')
                          ->where('end_date', '>=', now());
                  });
            })
            ->orderBy('start_date');

        $featuredEvent = (clone $eventsQuery)->first();
        $events = (clone $eventsQuery)->get();

        // Database-driven Hero Statistics
        $totalEvents = Event::count();
        $activeEvents = Event::where('status', 'active')->count();
        $displayEventsCount = $activeEvents > 0 ? $activeEvents : $totalEvents;

        $speakersCount = Event::whereNotNull('presenter')
            ->where('presenter', '!=', '')
            ->distinct('presenter')
            ->count('presenter');

        $activeTeachersCount = \App\Models\User::where('role', 'teacher')
            ->where('status', 'active')
            ->whereHas('teacher', fn ($q) => $q->where('is_verified', true))
            ->count();

        $totalSpeakers = max($speakersCount, $activeTeachersCount);

        $registeredUsersCount = EventRegistration::where('status', 'registered')->count();
        $sumRegisteredCount = (int) Event::sum('registered_count');
        $totalAttendees = max($registeredUsersCount, $sumRegisteredCount);

        return view('events.index', compact(
            'featuredEvent',
            'events',
            'displayEventsCount',
            'totalEvents',
            'activeEvents',
            'totalSpeakers',
            'totalAttendees'
        ));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
        return view('events.detail', compact('event'));
    }

    // Map frontend categories to database types
    private function categoryToType($category)
    {
        $mapping = [
            'networking' => 'meetup',
            'bootcamp' => 'workshop', // Map bootcamp to workshop as closest match
        ];
        return $mapping[$category] ?? $category;
    }

    // Map database types to frontend categories
    private function typeToCategory($type)
    {
        $mapping = [
            'meetup' => 'networking',
        ];
        return $mapping[$type] ?? $type;
    }

    public function apiIndex(Request $request)
    {
        $query = Event::where('start_date', '>=', now())
            ->where('status', 'active')
            ->orderBy('start_date');

        if ($request->has('category') && $request->category !== 'all') {
            $dbType = $this->categoryToType($request->category);
            $query->where('type', $dbType);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $events = $query->get()->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'category' => $this->typeToCategory($event->type),
                'date' => $event->start_date?->format('Y-m-d'),
                'endDate' => $event->end_date?->format('Y-m-d'),
                'time' => $event->start_date?->format('H:i'),
                'duration' => $event->duration ?: $this->calculateDuration($event),
                'presenter' => $event->presenter,
                'event_mode' => $event->event_mode,
                'location' => $event->location ?? 'Online',
                'google_maps_url' => $event->google_maps_url,
                'attendees' => $event->registered_count,
                'max_attendees' => $event->max_attendees,
                'image' => $event->thumbnail ? asset('storage/' . $event->thumbnail) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400&h=250&fit=crop',
                'description' => $event->description,
                'featured' => false,
            ];
        });

        return response()->json($events);
    }

    private function calculateDuration($event)
    {
        if (!$event->start_date || !$event->end_date) {
            return 'TBA';
        }

        $diff = $event->start_date->diffInDays($event->end_date);

        if ($diff === 0) {
            $hours = $event->start_date->diffInHours($event->end_date);
            return $hours > 0 ? $hours . ' hours' : '1 hour';
        }

        return $diff + 1 . ' days';
    }

    /**
     * Register user for an event
     */
    public function register(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to register for this event.'
            ], 401);
        }

        $user = Auth::user();

        // Check if event is active and open for registration
        if (!$event->isOpenForRegistration()) {
            $statusMessage = $event->status === 'closed'
                ? 'Registration for this event is currently closed.'
                : 'This event has been cancelled.';
            return response()->json([
                'success' => false,
                'message' => $statusMessage
            ], 400);
        }

        // Check if user already registered for this event - using direct query for reliability
        $existingRegistration = \App\Models\EventRegistration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->where('status', 'registered')
            ->first();

        \Log::info('Registration check', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'existing_registration' => $existingRegistration ? $existingRegistration->toArray() : null
        ]);

        if ($existingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'You have already registered for this event.'
            ], 400);
        }

        // Check if event is full using actual count
        if ($event->isFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this event has reached its maximum capacity.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Create registration record
            EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'registered',
            ]);

            // Increment registered count
            $event->increment('registered_count');

            DB::commit();

            // Send confirmation email to user
            try {
                \Log::info('Attempting to send email to: ' . $user->email);
                Mail::to($user->email)->send(new EventRegistrationConfirmed($event, $user));
                \Log::info('Email sent successfully to: ' . $user->email);
            } catch (\Exception $e) {
                // Log detailed error but don't fail the registration
                \Log::error('Failed to send registration confirmation email', [
                    'user_email' => $user->email,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString()
                ]);
                // Still return success since registration was saved
            }

            // Get actual count from database
            $actualCount = $event->getActualRegisteredCount();

            return response()->json([
                'success' => true,
                'message' => 'You have successfully registered for this event! A confirmation email has been sent to your email address.',
                'registered_count' => $actualCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Event registration failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ], 500);
        }
    }
}
