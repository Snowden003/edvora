@extends('layouts.app')

@section('title', $event->title . ' - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-detail.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-detail-new.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
@endpush

@section('content')
<!-- Header -->
    


    <!-- Modern Event Hero Section -->
    <section class="event-hero-modern">
        <!-- Animated Background Elements -->
        <div class="hero-bg-gradient"></div>
        <div class="hero-grid-pattern"></div>
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-orb orb-3"></div>

        <!-- Floating Icons -->
        <div class="floating-icon" style="top: 10%; left: 5%; animation-delay: 0s;"><i class="fas fa-code"></i></div>
        <div class="floating-icon" style="top: 20%; right: 8%; animation-delay: 1s;"><i class="fas fa-shield-alt"></i></div>
        <div class="floating-icon" style="top: 60%; left: 3%; animation-delay: 2s;"><i class="fas fa-terminal"></i></div>
        <div class="floating-icon" style="bottom: 20%; right: 5%; animation-delay: 3s;"><i class="fas fa-bug"></i></div>
        <div class="floating-icon" style="top: 40%; left: 8%; animation-delay: 1.5s;"><i class="fas fa-laptop-code"></i></div>

        <div class="container position-relative" style="z-index: 10;">
            <!-- Top Badge Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <!-- Event Type Badge -->
                        <div class="hero-badge type-badge">
                            <i class="fas {{ $event->type === 'workshop' ? 'fa-tools' : ($event->type === 'webinar' ? 'fa-video' : ($event->type === 'conference' ? 'fa-users' : 'fa-microphone')) }}"></i>
                            <span>{{ ucfirst($event->type) }}</span>
                        </div>

                        <!-- Event Mode Badge -->
                        <div class="hero-badge mode-badge {{ $event->event_mode }}">
                            <i class="fas {{ $event->event_mode === 'online' ? 'fa-wifi' : 'fa-map-marker-alt' }}"></i>
                            <span>{{ $event->event_mode === 'online' ? 'Online Event' : 'In-Person' }}</span>
                        </div>

                        <!-- Topic Badge -->
                        @if($event->event_topic)
                        <div class="hero-badge topic-badge">
                            <i class="fas fa-hashtag"></i>
                            <span>{{ $event->event_topic }}</span>
                        </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="hero-badge status-badge {{ $event->status }}">
                            <i class="fas fa-circle" style="font-size: 8px;"></i>
                            <span>{{ ucfirst($event->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Hero Content -->
            <div class="row align-items-center g-5">
                <!-- Left: Event Info -->
                <div class="col-lg-7">
                    <div class="hero-content">
                        <h1 class="hero-title">{{ $event->title }}</h1>
                        <p class="hero-subtitle">{{ Str::limit(strip_tags($event->description), 150) }}</p>

                        <!-- Quick Info Grid -->
                        <div class="hero-info-grid">
                            @php
                                $actualCount = $event->getActualRegisteredCount();
                            @endphp
                            <div class="info-card">
                                <div class="info-icon"><i class="fas fa-calendar"></i></div>
                                <div class="info-content">
                                    <span class="info-label">Date</span>
                                    <span class="info-value">{{ $event->start_date?->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="info-card">
                                <div class="info-icon"><i class="fas fa-clock"></i></div>
                                <div class="info-content">
                                    <span class="info-label">Duration</span>
                                    <span class="info-value">{{ $event->duration ?? '1 Day' }}</span>
                                </div>
                            </div>
                            @if(isset($event->workshop_details['event_time']))
                            <div class="info-card">
                                <div class="info-icon"><i class="fas fa-hourglass-start"></i></div>
                                <div class="info-content">
                                    <span class="info-label">Time</span>
                                    <span class="info-value">{{ $event->workshop_details['event_time']['start_time'] ?? '' }}</span>
                                </div>
                            </div>
                            @endif
                            <div class="info-card">
                                <div class="info-icon"><i class="fas {{ $event->event_mode === 'online' ? 'fa-globe' : 'fa-building' }}"></i></div>
                                <div class="info-content">
                                    <span class="info-label">Location</span>
                                    <span class="info-value">{{ $event->location ?? 'Online' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity Progress -->
                        @if($event->max_attendees)
                        <div class="capacity-section">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="capacity-label"><i class="fas fa-users me-2"></i>Registration Status</span>
                                <span class="capacity-text">{{ $actualCount }} / {{ $event->max_attendees }} registered</span>
                            </div>
                            @php
                                $fillPercent = min(100, ($actualCount / $event->max_attendees) * 100);
                                $fillColor = $fillPercent >= 90 ? 'var(--danger)' : ($fillPercent >= 70 ? 'var(--warning)' : 'var(--success)');
                            @endphp
                            <div class="capacity-bar">
                                <div class="capacity-fill" style="width: {{ $fillPercent }}%; --fill-color: {{ $fillColor }};"></div>
                            </div>
                            @if($fillPercent >= 90)
                                <span class="capacity-alert"><i class="fas fa-fire me-1"></i>Almost Full - Register Now!</span>
                            @endif
                        </div>
                        @endif

                        <!-- Mobile CTA (visible on mobile) -->
                        <div class="d-lg-none mt-4">
                            @if($event->isOpenForRegistration())
                                <button type="button" id="registerBtnMobile" class="btn btn-register-hero w-100" data-event-id="{{ $event->id }}">
                                    <span class="btn-text"><i class="fas fa-ticket-alt me-2"></i>Register Now</span>
                                    <span class="btn-loading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Registering...
                                    </span>
                                </button>
                            @else
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-clock me-2"></i>
                                    <span>Registration unavailable - Event postponed</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Event Card -->
                <div class="col-lg-5">
                    <div class="hero-event-card">
                        @if($event->thumbnail)
                            <div class="event-image-wrapper">
                                <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}" class="event-image">
                                <div class="image-overlay">
                                    <div class="overlay-glow"></div>
                                </div>
                            </div>
                        @else
                            <div class="event-image-wrapper placeholder">
                                <div class="placeholder-content">
                                    <i class="fas {{ $event->type === 'workshop' ? 'fa-tools' : ($event->type === 'webinar' ? 'fa-video' : 'fa-calendar-alt') }}"></i>
                                    <span>{{ ucfirst($event->type) }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Event Highlights -->
                        <div class="event-highlights">
                            @if(isset($event->workshop_details['what_you_will_learn']) && is_array($event->workshop_details['what_you_will_learn']))
                            <div class="highlight-item">
                                <i class="fas fa-graduation-cap"></i>
                                <span>{{ count($event->workshop_details['what_you_will_learn']) }} Topics to Learn</span>
                            </div>
                            @endif
                            @if(isset($event->workshop_details['presenters']) && count($event->workshop_details['presenters']) > 0)
                            <div class="highlight-item">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>{{ count($event->workshop_details['presenters']) }} Expert Speaker{{ count($event->workshop_details['presenters']) > 1 ? 's' : '' }}</span>
                            </div>
                            @endif
                            @if(isset($event->workshop_details['completion_outcome']))
                            <div class="highlight-item">
                                <i class="fas fa-award"></i>
                                <span>Certificate on Completion</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Wave -->
        <div class="hero-wave">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Event Content -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Event Overview -->
                    <div class="content-section">
                        <h2 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Event Overview
                        </h2>
                        <div id="eventDescription" class="text-dark">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <!-- Key Speakers / Presenters - Enhanced -->
                    @if(isset($event->workshop_details['presenters']) && count($event->workshop_details['presenters']) > 0)
                    <div class="speaker-enhanced-section">
                        <h2 class="section-title mb-4">
                            <i class="fas fa-microphone-alt text-primary"></i>
                            Meet Your Speakers
                            <span class="badge bg-primary ms-2">{{ count($event->workshop_details['presenters']) }}</span>
                        </h2>
                        <div class="speakers-enhanced-grid">
                            @foreach($event->workshop_details['presenters'] as $index => $presenter)
                            <div class="speaker-enhanced-card" style="animation-delay: {{ $index * 0.1 }}s">
                                <div class="speaker-header">
                                    <div class="speaker-avatar-wrapper">
                                        @if(isset($presenter['photo']) && $presenter['photo'])
                                        <img src="{{ asset('storage/' . $presenter['photo']) }}"
                                            alt="{{ $presenter['name'] }}" class="speaker-enhanced-avatar">
                                        @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($presenter['name']) }}&background=1F8FFF&color=fff&size=200"
                                            alt="{{ $presenter['name'] }}" class="speaker-enhanced-avatar">
                                        @endif
                                        <div class="speaker-verified-badge">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="speaker-header-info">
                                        <h5 class="speaker-enhanced-name">{{ $presenter['name'] }}</h5>
                                        <p class="speaker-enhanced-role">
                                            {{ $presenter['specialization'] ?? $presenter['expertise'] ?? 'Industry Expert' }}
                                        </p>
                                        <div class="speaker-tags">
                                            @if(isset($presenter['experience']))
                                            <span class="speaker-tag"><i class="fas fa-briefcase me-1"></i>{{ $presenter['experience'] }} Years Experience</span>
                                            @endif
                                            @if(isset($presenter['email']))
                                            <span class="speaker-tag"><i class="fas fa-envelope me-1"></i>Contact Available</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="speaker-details">
                                    @if(isset($presenter['bio']) && !empty($presenter['bio']))
                                    <div class="speaker-detail-item">
                                        <div class="speaker-detail-icon">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="speaker-detail-content">
                                            <div class="speaker-detail-label">About</div>
                                            <div class="speaker-detail-value speaker-bio-text">{{ $presenter['bio'] }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(isset($presenter['email']) && !empty($presenter['email']))
                                    <div class="speaker-detail-item">
                                        <div class="speaker-detail-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="speaker-detail-content">
                                            <div class="speaker-detail-label">Email</div>
                                            <div class="speaker-detail-value">{{ $presenter['email'] }}</div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(isset($presenter['type']) && $presenter['type'] === 'existing')
                                    <div class="speaker-detail-item">
                                        <div class="speaker-detail-icon">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <div class="speaker-detail-content">
                                            <div class="speaker-detail-label">Status</div>
                                            <div class="speaker-detail-value">Verified Edvora Instructor</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @elseif($event->presenter)
                    <!-- Single Presenter Field - Enhanced -->
                    <div class="speaker-enhanced-section">
                        <h2 class="section-title mb-4">
                            <i class="fas fa-microphone-alt text-primary"></i>
                            Speaker / Presenter
                        </h2>
                        <div class="speakers-enhanced-grid">
                            <div class="speaker-enhanced-card">
                                <div class="speaker-header">
                                    <div class="speaker-avatar-wrapper">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($event->presenter) }}&background=1F8FFF&color=fff&size=200"
                                            alt="{{ $event->presenter }}" class="speaker-enhanced-avatar">
                                        <div class="speaker-verified-badge">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <div class="speaker-header-info">
                                        <h5 class="speaker-enhanced-name">{{ $event->presenter }}</h5>
                                        <p class="speaker-enhanced-role">Event Speaker</p>
                                        <div class="speaker-tags">
                                            <span class="speaker-tag"><i class="fas fa-microphone me-1"></i>Guest Speaker</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Workshop Schedule Section -->
                    @if($event->type === 'workshop' && $event->workshopSchedules->count() > 0)
                    <div class="content-section">
                        <h2 class="section-title">
                            <i class="fas fa-calendar-week"></i>
                            Workshop Schedule
                        </h2>
                        <div class="workshop-schedule-timeline">
                            @foreach($event->workshopSchedules as $index => $schedule)
                            <div class="schedule-item">
                                <div class="schedule-day-badge">Day {{ $index + 1 }}</div>
                                <div class="schedule-content">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <h5 class="fw-bold mb-1">{{ $schedule->day ?? 'Day ' . ($index + 1) }}</h5>
                                        @if($schedule->date)
                                        <span class="schedule-date">
                                            <i class="fas fa-calendar-day me-1"></i>{{ \Carbon\Carbon::parse($schedule->date)->format('M d, Y') }}
                                        </span>
                                        @endif
                                    </div>
                                    @if($schedule->start_time && $schedule->end_time)
                                    <div class="schedule-time mb-2">
                                        <i class="fas fa-clock me-1 text-primary"></i>
                                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                    </div>
                                    @endif
                                    @if($schedule->topics)
                                    <div class="schedule-topics">
                                        <strong><i class="fas fa-book-open me-1 text-primary"></i>Topics:</strong>
                                        <p class="mb-0 mt-1">{!! nl2br(e($schedule->topics)) !!}</p>
                                    </div>
                                    @endif
                                    @if($schedule->description)
                                    <div class="schedule-description mt-2">
                                        <p class="mb-0 text-muted small">{!! nl2br(e($schedule->description)) !!}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- What You'll Learn / Completion Outcome -->
                    @if(isset($event->workshop_details['completion_outcome']))
                    <div class="content-section">
                        <h2 class="section-title">
                            <i class="fas fa-award"></i>
                            What You Will Achieve
                        </h2>
                        <div class="text-dark">
                            {!! nl2br(e($event->workshop_details['completion_outcome'])) !!}
                        </div>
                    </div>
                    @endif

                    <!-- What You'll Learn -->
                    @if(isset($event->workshop_details['what_you_will_learn']) && !empty($event->workshop_details['what_you_will_learn']))
                    <div class="content-section">
                        <h2 class="section-title">
                            <i class="fas fa-lightbulb"></i>
                            What You'll Learn
                        </h2>
                        @if(is_array($event->workshop_details['what_you_will_learn']))
                        <div class="learning-grid">
                            @foreach($event->workshop_details['what_you_will_learn'] as $index => $item)
                            <div class="learning-card" style="--delay: {{ $index * 0.1 }}s">
                                <div class="learning-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="learning-content">
                                    <h6>{{ $item }}</h6>
                                </div>
                                <div class="learning-number">{{ sprintf('%02d', $index + 1) }}</div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <!-- Legacy text format -->
                        <div class="text-dark" style="line-height: 1.8;">
                            {!! nl2br(e($event->workshop_details['what_you_will_learn'])) !!}
                        </div>
                        @endif
                    </div>
                    @else
                    <!-- Default What You'll Learn -->
                    <div class="content-section">
                        <h2 class="section-title">
                            <i class="fas fa-lightbulb"></i>
                            What You'll Learn
                        </h2>
                        <div class="row g-3" id="eventLearning">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <i class="bi bi-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Latest Industry Trends</h6>
                                        <p class="text-muted small mb-0">Stay updated with current developments</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <i class="bi bi-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Practical Skills</h6>
                                        <p class="text-muted small mb-0">Hands-on learning experience</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <i class="bi bi-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Networking</h6>
                                        <p class="text-muted small mb-0">Connect with industry professionals</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <i class="bi bi-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Expert Insights</h6>
                                        <p class="text-muted small mb-0">Learn from experienced professionals</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Registration -->
                    <div class="registration-section mb-4">
                        <h3 class="fw-bold mb-3">Register Now</h3>
                        <p class="mb-4 text-muted">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Secure your spot today. Limited seats available!
                        </p>
                        @if($event->isOpenForRegistration())
                            <button type="button" id="registerBtn" class="btn register-btn w-100 mb-3" data-event-id="{{ $event->id }}">
                                <span class="btn-text"><i class="fas fa-ticket-alt me-2"></i>Register Now</span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Registering...
                                </span>
                            </button>
                        @else
                            <div class="alert alert-warning text-center mb-3" style="border-radius: 12px; background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 152, 0, 0.1) 100%); border: 1px solid rgba(255, 152, 0, 0.3);">
                                <i class="fas fa-clock me-2" style="color: #FF9800;"></i>
                                <span style="color: #E65100; font-weight: 600;">Registration is currently unavailable. This event has been postponed and will reopen soon.</span>
                            </div>
                        @endif
                        <div class="d-flex gap-2">
                            <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($event->title) }}&dates={{ $event->start_date?->format('Ymd\\THis') }}/{{ $event->end_date?->format('Ymd\\THis') ?? $event->start_date?->copy()->addDay()->format('Ymd\\THis') }}&location={{ urlencode($event->location ?? 'Online') }}&details={{ urlencode(Str::limit(strip_tags($event->description), 200)) }}"
                               target="_blank" class="btn btn-action-calendar flex-fill">
                                <i class="fas fa-calendar-plus me-2"></i>
                                <span>Add to Calendar</span>
                            </a>
                            <button class="btn btn-action-share flex-fill" onclick="shareEvent()">
                                <i class="fas fa-share-alt me-2"></i>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>

                    @if($event->google_maps_url)
                    <!-- Location Map -->
                    <div class="content-section mb-4">
                        <h4 class="section-title">
                            <i class="fas fa-map-marker-alt"></i>
                            Location
                        </h4>
                        <div class="map-container" style="border-radius: 12px; overflow: hidden;">
                            <iframe
                                src="{{ $event->google_maps_url }}"
                                width="100%"
                                height="250"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        @if($event->location)
                        <p class="mt-2 mb-0 text-muted">
                            <i class="fas fa-location-arrow me-2"></i>{{ $event->location }}
                        </p>
                        @endif
                    </div>
                    @endif

                    <!-- Event Features -->
                    @php
                        $featureIcons = [
                            'certificate' => 'fa-certificate',
                            'recording' => 'fa-video',
                            'materials' => 'fa-file-pdf',
                            'qa' => 'fa-comments',
                            'networking' => 'fa-users',
                            'mentorship' => 'fa-user-tie',
                            'internship' => 'fa-briefcase',
                            'job' => 'fa-handshake',
                            'live' => 'fa-broadcast-tower',
                        ];
                        $featureLabels = [
                            'certificate' => 'Certificate of Completion',
                            'recording' => 'Event Recording Available',
                            'materials' => 'Digital Resources Included',
                            'qa' => 'Q&A Session',
                            'networking' => 'Networking Opportunities',
                            'mentorship' => 'Mentorship Program',
                            'internship' => 'Internship Opportunities',
                            'job' => 'Job Placement Support',
                            'live' => 'Live Streaming',
                        ];
                    @endphp
                    <div class="content-section">
                        <h4 class="section-title">
                            <i class="fas fa-star"></i>
                            Event Features
                        </h4>
                        <div class="d-flex flex-column gap-3" id="eventFeatures">
                            @if(isset($event->workshop_details['event_features']) && count($event->workshop_details['event_features']) > 0)
                                @foreach($event->workshop_details['event_features'] as $feature)
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas {{ $featureIcons[$feature] ?? 'fa-check' }} text-primary"></i>
                                    <span>{{ $featureLabels[$feature] ?? ucfirst($feature) }}</span>
                                </div>
                                @endforeach
                            @else
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-video text-primary"></i>
                                    <span>Live Streaming Available</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-pdf text-primary"></i>
                                    <span>Digital Resources Included</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-certificate text-primary"></i>
                                    <span>Certificate of Attendance</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-users text-primary"></i>
                                    <span>Networking Opportunities</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Invitation Card Download -->
                    @if($event->invitation_card_file || $event->invitation_card_content)
                    <div class="content-section mt-4">
                        <h4 class="section-title">
                            <i class="fas fa-envelope-open-text"></i>
                            Invitation Card
                        </h4>
                        @if($event->invitation_card_file)
                        <div class="invitation-card-preview mb-3">
                            <img src="{{ asset('storage/' . $event->invitation_card_file) }}" alt="Invitation Card" class="img-fluid rounded" style="border: 1px solid rgba(0,0,0,0.1);">
                        </div>
                        <a href="{{ asset('storage/' . $event->invitation_card_file) }}" download class="btn btn-outline-primary w-100">
                            <i class="fas fa-download me-2"></i>Download Invitation
                        </a>
                        @elseif($event->invitation_card_content)
                        <div class="invitation-text-content p-3 rounded bg-light mb-3">
                            <p class="mb-0 text-muted small">{!! nl2br(e($event->invitation_card_content)) !!}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/events-detail.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
<script>
// Registration button with loading spinner
function handleRegistration(btn) {
    const eventId = btn.data('event-id');
    const btnText = btn.find('.btn-text');
    const btnLoading = btn.find('.btn-loading');
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    if (!csrfToken) {
        showToast('Security token missing. Please refresh the page.', 'error');
        return;
    }

    // Show loading state
    btn.prop('disabled', true);
    btnText.hide();
    btnLoading.show();
    btn.css('opacity', '0.8');

    // Send registration request
    $.ajax({
        url: '/events/' + eventId + '/register',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        success: function(response) {
            btnLoading.hide();
            btnText.html('<i class="fas fa-check me-2"></i>Registered!').show();
            btn.removeClass('register-btn btn-register-hero').addClass('btn-success');
            showToast(response.message || 'Registration successful! Check your email for confirmation.', 'success');
        },
        error: function(xhr) {
            btnLoading.hide();
            btnText.show();
            btn.prop('disabled', false).css('opacity', '1');

            if (xhr.status === 401) {
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
            } else {
                const message = xhr.responseJSON?.message || 'Registration failed. Please try again.';
                showToast(message, xhr.status === 400 ? 'warning' : 'error');
            }
        }
    });
}

$(document).ready(function() {
    // Desktop register button
    $('#registerBtn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        handleRegistration($(this));
    });

    // Mobile register button
    $('#registerBtnMobile').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        handleRegistration($(this));
    });
});
</script>
@endpush
