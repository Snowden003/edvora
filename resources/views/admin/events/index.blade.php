@extends('layouts.app')

@section('title', 'Manage Events - Admin - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/admin/events-index.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
    <!-- Admin Sidebar -->
    <div class="admin-sidebar flex-shrink-0" style="width: 260px; min-height: calc(100vh - 60px);">
        <div class="p-4 border-bottom border-secondary">
            <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Panel</h5>
            <small class="text-white-50">Secure Access Only</small>
        </div>
        <nav class="nav flex-column py-3">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="bi bi-speedometer2"></i>Dashboard
            </a>
            <a href="{{ route('filament.admin.resources.courses.index') }}" class="nav-link">
                <i class="bi bi-book"></i>Courses
            </a>
            <a href="{{ route('filament.admin.resources.teacher-applications.index') }}" class="nav-link">
                <i class="bi bi-person-video3"></i>Teacher Applications
            </a>
            <a href="{{ route('admin.events.index') }}" class="nav-link active">
                <i class="bi bi-calendar-event"></i>Events
            </a>
            <a href="{{ route('filament.admin.resources.contact-messages.index') }}" class="nav-link">
                <i class="bi bi-envelope"></i>Messages
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1 p-4" style="background: #f5f7fa; min-height: calc(100vh - 60px);">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #1a1a2e;">Manage Events</h2>
                <p class="text-muted mb-0">Create, edit and manage special events</p>
            </div>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Create New Event
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card events">
                    <div class="stats-number">{{ $events->total() }}</div>
                    <div class="stats-label">Total Events</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card upcoming">
                    <div class="stats-number">{{ \App\Models\Event::where('start_date', '>=', now())->count() }}</div>
                    <div class="stats-label">Upcoming Events</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card past">
                    <div class="stats-number">{{ \App\Models\Event::where('start_date', '<', now())->count() }}</div>
                    <div class="stats-label">Past Events</div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Events Table -->
        <div class="events-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Mode</th>
                            <th>Presenter</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Location</th>
                            <th>Attendees</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $event->thumbnail ? asset('storage/' . $event->thumbnail) : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=80&h=50&fit=crop' }}"
                                         alt="{{ $event->title }}" class="event-thumbnail">
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $event->title }}</h6>
                                        <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-type badge-{{ $event->type }}">
                                    {{ ucfirst($event->type) }}
                                </span>
                            </td>
                            <td>
                                @if($event->event_mode == 'online')
                                <span class="badge bg-info">Online</span>
                                @else
                                <span class="badge bg-warning text-dark">In-Person</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $event->presenter ?: 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="fw-medium">{{ $event->start_date?->format('M d, Y') }}</div>
                                    @if($event->end_date)
                                    <div class="text-muted">to {{ $event->end_date->format('M d, Y') }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $event->duration ?: 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $event->location ?? 'Online' }}
                                    @if($event->google_maps_url)
                                    <i class="bi bi-map ms-1 text-primary" title="Has Google Maps"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @php
                                    $actualCount = $event->getActualRegisteredCount();
                                @endphp
                                <div class="small">
                                    <span class="fw-medium">{{ $actualCount }}</span>
                                    @if($event->max_attendees)
                                    <span class="text-muted">/ {{ $event->max_attendees }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.events.registrations.pdf', $event) }}" class="btn btn-sm btn-outline-success action-btn" title="Download Registrations PDF">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary action-btn" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger action-btn" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                    No events found. Create your first event!
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </main>
</div>
@endsection
