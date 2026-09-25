@extends('layouts.app')

@section('title', 'Edit Event - Admin - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/admin/events-create.css') }}" rel="stylesheet" />
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
            <a href="/admin/courses" class="nav-link">
                <i class="bi bi-book"></i>Courses
            </a>
            <a href="/admin/teachers" class="nav-link">
                <i class="bi bi-person-video3"></i>Teachers
            </a>
            <a href="{{ route('admin.events.index') }}" class="nav-link active">
                <i class="bi bi-calendar-event"></i>Events
            </a>
            <span class="nav-link text-muted opacity-50 d-flex align-items-center justify-content-between" style="cursor: not-allowed;">
                <span><i class="bi bi-envelope"></i>Messages</span>
                <span class="badge bg-warning text-dark font-normal" style="font-size: 10px;">⭐ به زودی</span>
            </span>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1 p-4" style="background: #f5f7fa; min-height: calc(100vh - 60px);">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #1a1a2e;">Edit Event</h2>
                <p class="text-muted mb-0">Update event details</p>
            </div>
            @php
                $actualCount = $event->getActualRegisteredCount();
            @endphp
            <div class="d-flex gap-2">
                <div class="stats-badge">
                    <i class="bi bi-people-fill text-primary"></i>
                    <span>{{ $actualCount }} registered</span>
                </div>
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Form -->
        <div class="form-card">
            <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Basic Information -->
                    <div class="col-12">
                        <h5 class="fw-bold mb-3" style="color: #1F8FFF;">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Event Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type" class="form-label">Event Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="workshop" {{ old('type', $event->type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                <option value="webinar" {{ old('type', $event->type) == 'webinar' ? 'selected' : '' }}>Webinar</option>
                                <option value="conference" {{ old('type', $event->type) == 'conference' ? 'selected' : '' }}>Conference</option>
                                <option value="meetup" {{ old('type', $event->type) == 'meetup' ? 'selected' : '' }}>Meetup</option>
                                <option value="seminar" {{ old('type', $event->type) == 'seminar' ? 'selected' : '' }}>Seminar</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="event_mode" class="form-label">Event Mode *</label>
                            <select class="form-select @error('event_mode') is-invalid @enderror"
                                    id="event_mode" name="event_mode" required onchange="toggleLocationFields()">
                                <option value="online" {{ old('event_mode', $event->event_mode) == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="in-person" {{ old('event_mode', $event->event_mode) == 'in-person' ? 'selected' : '' }}>In-Person</option>
                            </select>
                            @error('event_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Event Topic/Subject -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="event_topic" class="form-label">
                                <i class="bi bi-tag me-2 text-primary"></i>Event Topic / Subject
                            </label>
                            <select class="form-select @error('event_topic') is-invalid @enderror"
                                    id="event_topic" name="event_topic">
                                <option value="">Select Topic</option>
                                <option value="Cyber Security" {{ old('event_topic', $event->event_topic) == 'Cyber Security' ? 'selected' : '' }}>Cyber Security</option>
                                <option value="Web Development" {{ old('event_topic', $event->event_topic) == 'Web Development' ? 'selected' : '' }}>Web Development</option>
                                <option value="AI & Machine Learning" {{ old('event_topic', $event->event_topic) == 'AI & Machine Learning' ? 'selected' : '' }}>AI & Machine Learning</option>
                                <option value="Data Science" {{ old('event_topic', $event->event_topic) == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                <option value="Cloud Computing" {{ old('event_topic', $event->event_topic) == 'Cloud Computing' ? 'selected' : '' }}>Cloud Computing</option>
                                <option value="DevOps" {{ old('event_topic', $event->event_topic) == 'DevOps' ? 'selected' : '' }}>DevOps</option>
                                <option value="Mobile Development" {{ old('event_topic', $event->event_topic) == 'Mobile Development' ? 'selected' : '' }}>Mobile Development</option>
                                <option value="Blockchain" {{ old('event_topic', $event->event_topic) == 'Blockchain' ? 'selected' : '' }}>Blockchain</option>
                                <option value="Networking" {{ old('event_topic', $event->event_topic) == 'Networking' ? 'selected' : '' }}>Networking</option>
                                <option value="Database Management" {{ old('event_topic', $event->event_topic) == 'Database Management' ? 'selected' : '' }}>Database Management</option>
                                <option value="UI/UX Design" {{ old('event_topic', $event->event_topic) == 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                <option value="Software Engineering" {{ old('event_topic', $event->event_topic) == 'Software Engineering' ? 'selected' : '' }}>Software Engineering</option>
                                <option value="Other" {{ old('event_topic', $event->event_topic) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('event_topic')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the main topic/subject of this event.</div>
                        </div>
                    </div>

                    <!-- Date & Location -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fw-bold mb-3 mt-3" style="color: #1F8FFF;">
                            <i class="bi bi-calendar3 me-2"></i>Date & Location
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date & Time *</label>
                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date"
                                   value="{{ old('start_date', $event->start_date?->format('Y-m-d\TH:i')) }}" required>
                            @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date & Time</label>
                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date"
                                   value="{{ old('end_date', $event->end_date?->format('Y-m-d\TH:i')) }}">
                            @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration</label>
                            <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                   id="duration" name="duration"
                                   value="{{ old('duration', $event->duration) }}"
                                   placeholder="e.g., 2 hours, 3 days">
                            @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">e.g., 2 hours, 1 day, 3 days</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="presenter" class="form-label">Presenter / Speaker</label>
                            <input type="text" class="form-control @error('presenter') is-invalid @enderror"
                                   id="presenter" name="presenter"
                                   value="{{ old('presenter', $event->presenter) }}"
                                   placeholder="e.g., Dr. John Smith">
                            @error('presenter')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror"
                                   id="location" name="location"
                                   value="{{ old('location', $event->location) }}"
                                   placeholder="e.g., San Francisco, CA or Online">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_attendees" class="form-label">Maximum Attendees</label>
                            <input type="number" class="form-control @error('max_attendees') is-invalid @enderror"
                                   id="max_attendees" name="max_attendees"
                                   value="{{ old('max_attendees', $event->max_attendees) }}"
                                   min="1" placeholder="e.g., 100">
                            @error('max_attendees')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="registered_count" class="form-label">Registered Count</label>
                            @php
                                $actualCount = $event->getActualRegisteredCount();
                            @endphp
                            <input type="number" class="form-control @error('registered_count') is-invalid @enderror"
                                   id="registered_count" name="registered_count"
                                   value="{{ old('registered_count', $actualCount) }}"
                                   min="0" placeholder="0" readonly>
                            <div class="form-text text-muted">Auto-calculated from registrations table ({{ $actualCount }} actual registrations)</div>
                            @error('registered_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12" id="google_maps_container" style="{{ $event->event_mode == 'in-person' ? 'display: block;' : 'display: none;' }}">
                        <div class="mb-3">
                            <label for="google_maps_url" class="form-label">
                                <i class="bi bi-geo-alt me-2"></i>Google Maps URL <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('google_maps_url') is-invalid @enderror"
                                   id="google_maps_url" name="google_maps_url"
                                   value="{{ old('google_maps_url', $event->google_maps_url) }}"
                                   placeholder="https://www.google.com/maps/embed?pb=...">
                            @error('google_maps_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Required for in-person events. Paste Google Maps embed URL.
                                <a href="https://www.google.com/maps" target="_blank" class="text-primary">Open Google Maps</a>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fw-bold mb-3 mt-3" style="color: #1F8FFF;">
                            <i class="bi bi-image me-2"></i>Event Thumbnail
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Change Thumbnail</label>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                   id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this)">
                            @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty to keep current image. Max 2MB</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="image-preview" id="imagePreview">
                            @if($event->thumbnail)
                            <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}">
                            @else
                            <span><i class="bi bi-image me-2"></i>No Image</span>
                            @endif
                        </div>
                    </div>

                    <!-- Invitation Card -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fw-bold mb-3 mt-3" style="color: #1F8FFF;">
                            <i class="bi bi-envelope-paper me-2"></i>Invitation Card (For In-Person Events)
                        </h5>
                        <p class="text-muted small">
                            For <strong>In-Person events</strong>, the system automatically generates a PDF invitation card.<br>
                            You can also add a custom text email or upload a custom image if needed.
                        </p>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="invitation_card_type" class="form-label">Card Type</label>
                            <select class="form-select @error('invitation_card_type') is-invalid @enderror"
                                    id="invitation_card_type" name="invitation_card_type" onchange="toggleInvitationFields()">
                                <option value="">No Card</option>
                                <option value="text" {{ old('invitation_card_type', $event->invitation_card_type) == 'text' ? 'selected' : '' }}>Text Email</option>
                                <option value="image" {{ old('invitation_card_type', $event->invitation_card_type) == 'image' ? 'selected' : '' }}>Custom Image</option>
                            </select>
                            @error('invitation_card_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12" id="invitation_text_container" style="{{ old('invitation_card_type', $event->invitation_card_type) == 'text' ? 'display: block;' : 'display: none;' }}">
                        <div class="mb-3">
                            <label for="invitation_card_content" class="form-label">Card Content (Text)</label>
                            <textarea class="form-control @error('invitation_card_content') is-invalid @enderror"
                                      id="invitation_card_content" name="invitation_card_content" rows="4"
                                      placeholder="Enter invitation message. You can use {{ '{name}' }}, {{ '{event}' }}, {{ '{date}' }}, {{ '{location}' }} as placeholders.">{{ old('invitation_card_content', $event->invitation_card_content) }}</textarea>
                            @error('invitation_card_content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Available placeholders: <code>{name}</code> (attendee), <code>{event}</code> (event title), <code>{date}</code> (event date), <code>{location}</code> (event location)
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="invitation_file_container" style="{{ old('invitation_card_type', $event->invitation_card_type) == 'image' ? 'display: block;' : 'display: none;' }}">
                        <div class="mb-3">
                            <label for="invitation_card_file" class="form-label">Upload Card Image</label>
                            <input type="file" class="form-control @error('invitation_card_file') is-invalid @enderror"
                                   id="invitation_card_file" name="invitation_card_file"
                                   accept="image/*">
                            @error('invitation_card_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload custom invitation image (max 5MB)</div>
                        </div>
                        @if($event->invitation_card_file)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $event->invitation_card_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>View Current Card
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Event Outcomes -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fw-bold mb-3 mt-3" style="color: #1F8FFF;">
                            <i class="bi bi-lightbulb me-2"></i>Event Outcomes
                        </h5>
                    </div>

                    <!-- What You'll Learn - Dynamic Items -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-lightbulb me-2 text-warning"></i>What You'll Learn</span>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLearningItem()">
                                    <i class="bi bi-plus-lg me-1"></i>Add Item
                                </button>
                            </label>
                            <div id="learning_items_container">
                                @if(isset($event->workshop_details['what_you_will_learn']) && is_array($event->workshop_details['what_you_will_learn']))
                                    @foreach($event->workshop_details['what_you_will_learn'] as $index => $item)
                                    <div class="learning-item input-group mb-2">
                                        <span class="input-group-text"><i class="bi bi-check-circle text-primary"></i></span>
                                        <input type="text" class="form-control" name="what_you_will_learn[]" value="{{ $item }}" placeholder="e.g., How to Hack Systems" required>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeLearningItem(this)" {{ count($event->workshop_details['what_you_will_learn']) <= 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                @elseif(isset($event->workshop_details['what_you_will_learn']) && !empty($event->workshop_details['what_you_will_learn']))
                                    <!-- Legacy text format - convert to single item -->
                                    <div class="learning-item input-group mb-2">
                                        <span class="input-group-text"><i class="bi bi-check-circle text-primary"></i></span>
                                        <input type="text" class="form-control" name="what_you_will_learn[]" value="{{ $event->workshop_details['what_you_will_learn'] }}" placeholder="e.g., How to Hack Systems" required>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeLearningItem(this)" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="learning-item input-group mb-2">
                                        <span class="input-group-text"><i class="bi bi-check-circle text-primary"></i></span>
                                        <input type="text" class="form-control" name="what_you_will_learn[]" placeholder="e.g., How to Hack Systems" required>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeLearningItem(this)" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="form-text">Add specific topics/skills participants will learn. Click "Add Item" to add more.</div>
                        </div>
                    </div>

                    <!-- What You Will Achieve -->
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="workshop_completion_outcome" class="form-label">
                                <i class="bi bi-award me-2 text-primary"></i>What You Will Achieve
                            </label>
                            <textarea class="form-control @error('workshop_completion_outcome') is-invalid @enderror"
                                      name="workshop_completion_outcome" id="workshop_completion_outcome" rows="3"
                                      placeholder="Describe what participants will achieve after completing this event...&#10;- Certificate of Completion&#10;- Practical skills in...&#10;- Portfolio project...&#10;- Job readiness...">{{ old('workshop_completion_outcome', $event->workshop_details['completion_outcome'] ?? '') }}</textarea>
                            @error('workshop_completion_outcome')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Describe the outcomes, certificates, skills gained after completion.</div>
                        </div>
                    </div>

                    <!-- Event Status Management -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fw-bold mb-3 mt-3" style="color: #dc3545;">
                            <i class="bi bi-shield-exclamation me-2"></i>Event Management
                        </h5>
                    </div>

                    <!-- Status Toggle -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="bi bi-toggle-on me-2 text-primary"></i>Event Status
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', $event->status) == 'active' ? 'selected' : '' }}>
                                    Active - Open for Registration
                                </option>
                                <option value="closed" {{ old('status', $event->status) == 'closed' ? 'selected' : '' }}>
                                    Closed - Registration Disabled
                                </option>
                                <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled - Event Cancelled
                                </option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <strong>Active:</strong> Users can register | <strong>Closed:</strong> Registration disabled | <strong>Cancelled:</strong> Event cancelled
                            </div>
                        </div>
                    </div>

                    <!-- Current Status Badge -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <div class="mt-2">
                                @if($event->status == 'active')
                                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Active</span>
                                @elseif($event->status == 'closed')
                                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-x-circle me-1"></i>Closed</span>
                                @else
                                    <span class="badge bg-danger fs-6"><i class="bi bi-x-octagon me-1"></i>Cancelled</span>
                                @endif
                            </div>
                            @if($event->isFull())
                                <div class="mt-2">
                                    <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-triangle me-1"></i>Capacity Full</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-12">
                        <hr class="my-2">
                        <div class="d-flex gap-3 mt-4 flex-wrap">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg me-2"></i>Update Event
                            </button>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Back to Events
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-lg ms-auto" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                <i class="bi bi-trash me-2"></i>Delete Event
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Delete Event Modal -->
            <div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Delete Event</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">Are you sure you want to delete the event <strong>{{ $event->title }}</strong>?</p>
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle me-2"></i>
                                This action will permanently delete the event and all its registrations. This cannot be undone!
                            </div>
                            @if($event->getActualRegisteredCount() > 0)
                            <div class="alert alert-info">
                                <i class="bi bi-people me-2"></i>
                                <strong>{{ $event->getActualRegisteredCount() }}</strong> registered users will be affected.
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Yes, Delete Event
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="{{ asset('assets/js/admin/events-create.js') }}"></script>
@endsection
