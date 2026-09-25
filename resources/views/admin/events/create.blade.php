@extends('layouts.app')

@section('title', 'Create Event - Admin - Edvora Tech')

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
                <h2 class="fw-bold mb-1" style="color: #1a1a2e;">Create New Event</h2>
                <p class="text-muted mb-0">Add a special event to the platform</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Events
            </a>
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
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

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
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type" class="form-label">Event Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type" name="type" required onchange="toggleEventTypeFields()">
                                <option value="">Select Type</option>
                                <option value="seminar" {{ old('type') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                <option value="webinar" {{ old('type') == 'webinar' ? 'selected' : '' }}>Webinar</option>
                                <option value="conference" {{ old('type') == 'conference' ? 'selected' : '' }}>Conference</option>
                                <option value="meetup" {{ old('type') == 'meetup' ? 'selected' : '' }}>Meetup</option>
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
                                <option value="online" {{ old('event_mode', 'online') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="in-person" {{ old('event_mode') == 'in-person' ? 'selected' : '' }}>In-Person</option>
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
                                      id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Describe what attendees will learn and experience.</div>
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
                                <option value="Cyber Security" {{ old('event_topic') == 'Cyber Security' ? 'selected' : '' }}>Cyber Security</option>
                                <option value="Web Development" {{ old('event_topic') == 'Web Development' ? 'selected' : '' }}>Web Development</option>
                                <option value="AI & Machine Learning" {{ old('event_topic') == 'AI & Machine Learning' ? 'selected' : '' }}>AI & Machine Learning</option>
                                <option value="Data Science" {{ old('event_topic') == 'Data Science' ? 'selected' : '' }}>Data Science</option>
                                <option value="Cloud Computing" {{ old('event_topic') == 'Cloud Computing' ? 'selected' : '' }}>Cloud Computing</option>
                                <option value="DevOps" {{ old('event_topic') == 'DevOps' ? 'selected' : '' }}>DevOps</option>
                                <option value="Mobile Development" {{ old('event_topic') == 'Mobile Development' ? 'selected' : '' }}>Mobile Development</option>
                                <option value="Blockchain" {{ old('event_topic') == 'Blockchain' ? 'selected' : '' }}>Blockchain</option>
                                <option value="Networking" {{ old('event_topic') == 'Networking' ? 'selected' : '' }}>Networking</option>
                                <option value="Database Management" {{ old('event_topic') == 'Database Management' ? 'selected' : '' }}>Database Management</option>
                                <option value="UI/UX Design" {{ old('event_topic') == 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                <option value="Software Engineering" {{ old('event_topic') == 'Software Engineering' ? 'selected' : '' }}>Software Engineering</option>
                                <option value="Other" {{ old('event_topic') == 'Other' ? 'selected' : '' }}>Other</option>
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
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional - leave empty for single-day events</div>
                        </div>
                    </div>

                    <!-- Event Time - For ALL Event Types -->
                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="event_start_time" class="form-label">
                                    <i class="bi bi-clock me-2 text-primary"></i>Event Start Time
                                </label>
                                <input type="time" class="form-control @error('event_start_time') is-invalid @enderror"
                                       id="event_start_time" name="event_start_time" value="{{ old('event_start_time') }}">
                                @error('event_start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">e.g., 14:00 (2:00 PM)</div>
                            </div>
                            <div class="col-md-6">
                                <label for="event_end_time" class="form-label">
                                    <i class="bi bi-clock-history me-2 text-primary"></i>Event End Time
                                </label>
                                <input type="time" class="form-control @error('event_end_time') is-invalid @enderror"
                                       id="event_end_time" name="event_end_time" value="{{ old('event_end_time') }}">
                                @error('event_end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">e.g., 16:00 (4:00 PM)</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration</label>
                            <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                   id="duration" name="duration" value="{{ old('duration') }}"
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
                                   id="presenter" name="presenter" value="{{ old('presenter') }}"
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
                                   id="location" name="location" value="{{ old('location') }}"
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
                                   id="max_attendees" name="max_attendees" value="{{ old('max_attendees') }}"
                                   min="1" placeholder="e.g., 100">
                            @error('max_attendees')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty for unlimited</div>
                        </div>
                    </div>

                    <div class="col-12" id="google_maps_container" style="display: none;">
                        <div class="mb-3">
                            <label for="google_maps_url" class="form-label">
                                <i class="bi bi-geo-alt me-2"></i>Google Maps URL <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('google_maps_url') is-invalid @enderror"
                                   id="google_maps_url" name="google_maps_url" value="{{ old('google_maps_url') }}"
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
                            <label for="thumbnail" class="form-label">Upload Thumbnail</label>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                   id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this)">
                            @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Recommended size: 600x400 pixels, max 2MB</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="image-preview" id="imagePreview">
                            <span><i class="bi bi-image me-2"></i>Image Preview</span>
                        </div>
                    </div>

                    <!-- Invitation Card -->
                    <div class="col-12">
                        <hr class="my-2">
                        <h5 class="fw-bold mb-3 mt-3" style="color: #1F8FFF;">
                            <i class="bi bi-envelope-paper me-2"></i>Invitation Card (For In-Person Events)
                        </h5>
                        <p class="text-muted small">
                            Choose how attendees will receive their invitation after registration.
                            Confirmation emails are sent automatically to all registered users.
                        </p>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="invitation_card_type" class="form-label">Card Type</label>
                            <select class="form-select @error('invitation_card_type') is-invalid @enderror"
                                    id="invitation_card_type" name="invitation_card_type" onchange="toggleInvitationFields()">
                                <option value="">Registration Form Only (No Download)</option>
                                <option value="image" {{ old('invitation_card_type') == 'image' ? 'selected' : '' }}>Custom Image Card</option>
                                <option value="pdf" {{ old('invitation_card_type') == 'pdf' ? 'selected' : '' }}>Auto-Generated PDF Card</option>
                            </select>
                            @error('invitation_card_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6" id="invitation_file_container" style="display: none;">
                        <div class="mb-3">
                            <label for="invitation_card_file" class="form-label">Upload Card Image</label>
                            <input type="file" class="form-control @error('invitation_card_file') is-invalid @enderror"
                                   id="invitation_card_file" name="invitation_card_file"
                                   accept="image/*">
                            @error('invitation_card_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload invitation card image (max 5MB)</div>
                        </div>
                    </div>

                    <!-- Multiple Presenters Section - For ALL Event Types -->
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <div class="section-icon"><i class="bi bi-people-fill"></i></div>
                                Speakers / Presenters
                                <small class="text-muted ms-2 fs-6">(You can add multiple speakers)</small>
                            </h5>

                            <!-- Existing Teachers -->
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-person-check me-2 text-primary"></i>Select Existing Teachers</span>
                                </label>
                                <div class="row g-3">
                                    @foreach($teachers as $teacher)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check presenter-card p-3 border rounded hover-shadow">
                                            <input class="form-check-input" type="checkbox" name="presenters[existing][]" id="presenter_{{ $teacher->id }}" value="{{ $teacher->id }}" data-name="{{ $teacher->user->name ?? 'Unknown' }}" data-email="{{ $teacher->user->email ?? '' }}" data-specialization="{{ $teacher->specialization }}">
                                            <label class="form-check-label w-100 ms-2" for="presenter_{{ $teacher->id }}">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-person fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block">{{ $teacher->user->name ?? 'Unknown' }}</strong>
                                                        <small class="text-muted">{{ $teacher->specialization ?? 'General' }}</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Custom Speakers -->
                            <div class="mb-3">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-person-plus me-2 text-primary"></i>Additional Guest Speakers</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustomPresenter()">
                                        <i class="bi bi-plus-lg me-1"></i>Add Speaker
                                    </button>
                                </label>
                                <div id="custom_presenters_container">
                                    <!-- Custom presenter rows will be added here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Workshop Specific Details -->
                    <div class="col-12" id="workshop_section" style="display: none;">
                        <hr class="my-4">
                        <div class="workshop-section">
                            <h5 class="form-section-title">
                                <div class="section-icon"><i class="bi bi-tools"></i></div>
                                Workshop Details
                            </h5>

                            <!-- Workshop Time -->
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label for="workshop_start_time" class="form-label">
                                        <i class="bi bi-clock me-2 text-primary"></i>Workshop Start Time
                                    </label>
                                    <input type="time" class="form-control" name="workshop_start_time" id="workshop_start_time">
                                    <div class="form-text">Daily start time for the workshop</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="workshop_end_time" class="form-label">
                                        <i class="bi bi-clock-history me-2 text-primary"></i>Workshop End Time
                                    </label>
                                    <input type="time" class="form-control" name="workshop_end_time" id="workshop_end_time">
                                    <div class="form-text">Daily end time for the workshop</div>
                                </div>
                            </div>

                            <!-- Workshop Schedule (Days Only) -->
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-calendar-week me-2"></i>Workshop Days & Topics</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addScheduleRow()">
                                        <i class="bi bi-plus-lg me-1"></i>Add Day
                                    </button>
                                </label>
                                <div id="schedule_container">
                                    <!-- Schedule rows will be added here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Outcomes - For ALL Event Types -->
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <div class="section-icon"><i class="bi bi-lightbulb"></i></div>
                                Event Outcomes
                            </h5>

                            <!-- What You'll Learn - Dynamic Items -->
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-lightbulb me-2 text-warning"></i>What You'll Learn</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLearningItem()">
                                        <i class="bi bi-plus-lg me-1"></i>Add Item
                                    </button>
                                </label>
                                <div id="learning_items_container">
                                    <div class="learning-item input-group mb-2">
                                        <span class="input-group-text"><i class="bi bi-check-circle text-primary"></i></span>
                                        <input type="text" class="form-control" name="what_you_will_learn[]" placeholder="e.g., How to Hack Systems" required>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeLearningItem(this)" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-text">Add specific topics/skills participants will learn. Click "Add Item" to add more.</div>
                            </div>

                            <!-- Completion Outcome -->
                            <div class="mb-3">
                                <label for="workshop_completion_outcome" class="form-label">
                                    <i class="bi bi-award me-2 text-primary"></i>What You Will Achieve
                                </label>
                                <textarea class="form-control" name="workshop_completion_outcome" id="workshop_completion_outcome" rows="3" placeholder="Describe what participants will achieve after completing this event...&#10;- Certificate of Completion&#10;- Practical skills in...&#10;- Portfolio project...&#10;- Job readiness..."></textarea>
                                <div class="form-text">Describe the outcomes, certificates, skills gained after event completion.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Features -->
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <div class="section-icon"><i class="bi bi-star-fill"></i></div>
                                Event Features
                            </h5>
                            <p class="text-muted small mb-3">Select features available for this event</p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="event_features[]" id="feature_certificate" value="certificate">
                                        <label class="form-check-label" for="feature_certificate">
                                            <i class="bi bi-award me-2 text-warning"></i>Certificate of Completion
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="event_features[]" id="feature_recording" value="recording">
                                        <label class="form-check-label" for="feature_recording">
                                            <i class="bi bi-camera-video me-2 text-danger"></i>Recorded Sessions
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="event_features[]" id="feature_materials" value="materials">
                                        <label class="form-check-label" for="feature_materials">
                                            <i class="bi bi-file-earmark-text me-2 text-success"></i>Digital Materials
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="event_features[]" id="feature_networking" value="networking">
                                        <label class="form-check-label" for="feature_networking">
                                            <i class="bi bi-people me-2 text-info"></i>Networking Opportunity
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="event_features[]" id="feature_live" value="live">
                                        <label class="form-check-label" for="feature_live">
                                            <i class="bi bi-broadcast me-2 text-primary"></i>Live Streaming
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="event_features[]" id="feature_qa" value="qa">
                                        <label class="form-check-label" for="feature_qa">
                                            <i class="bi bi-question-circle me-2 text-secondary"></i>Q&A Session
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructor Email Notification -->
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="form-section border-primary" style="border: 2px solid #1F8FFF;">
                            <h5 class="form-section-title">
                                <div class="section-icon"><i class="bi bi-envelope-fill"></i></div>
                                Instructor Email Notification
                            </h5>
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notify_instructor" name="notify_instructor" value="1" checked>
                                        <label class="form-check-label fw-bold" for="notify_instructor">
                                            Send email notification to instructor
                                        </label>
                                        <div class="form-text">An email will be sent to the instructor with event details</div>
                                    </div>
                                </div>

                                <!-- Email field for custom instructor -->
                                <div class="col-md-6" id="custom_instructor_email_container" style="display: none;">
                                    <label for="custom_presenter_email" class="form-label">
                                        <i class="bi bi-envelope me-2 text-primary"></i>Instructor Email Address
                                    </label>
                                    <input type="email" class="form-control" name="custom_presenter[email]" id="custom_presenter_email" placeholder="instructor@example.com">
                                    <div class="form-text">Required for new instructors to send notification</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-gradient btn-lg px-5">
                                <i class="bi bi-check-lg me-2"></i>Create Event
                            </button>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-lg me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="{{ asset('assets/js/admin/events-create.js') }}"></script>
@endsection
