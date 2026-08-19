@extends('layouts.app')

@php
    $courseUrl = route('courses.detail', $course->slug);
    $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('assets/images/logo1.jpg');
    $courseDescription = (string) \Illuminate\Support\Str::of(strip_tags($course->description ?? ''))
        ->squish()
        ->limit(155, '');
    $lessons = $course->lessons ?? collect();
    $courseSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Course',
                'name' => $course->title,
                'description' => $courseDescription,
                'url' => $courseUrl,
                'image' => $courseImage,
                'provider' => [
                    '@type' => 'Organization',
                    'name' => 'Edvora Tech',
                    'url' => url('/'),
                ],
                'educationalLevel' => ucfirst($course->level),
                'timeRequired' => 'PT' . max(1, (int) $course->duration_hours) . 'H',
                'courseMode' => 'online',
                'isAccessibleForFree' => true,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'USD',
                    'availability' => 'https://schema.org/InStock',
                ],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => [
                    [
                        '@type' => 'Question',
                        'name' => 'Is ' . $course->title . ' free?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'Yes. This Edvora course is free for learners.',
                        ],
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'What level is this course?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'This course is designed for ' . $course->level . ' learners.',
                        ],
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'How long does this course take?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'The course includes approximately ' . $course->duration_hours . ' hours of learning content.',
                        ],
                    ],
                ],
            ],
        ],
    ];

    if ($course->teacher) {
        $courseSchema['@graph'][0]['hasCourseInstance'] = [
            '@type' => 'CourseInstance',
            'courseMode' => 'online',
            'instructor' => [
                '@type' => 'Person',
                'name' => $course->teacher->name,
            ],
        ];
    }
@endphp

@section('title', 'Free ' . $course->title . ' Online Course for Afghan Women | Edvora')
@section('meta_description', 'Free online ' . $course->title . ' course for Afghan women and girls. ' . $courseDescription)
@section('canonical', $courseUrl)
@section('meta_image', $courseImage)
@section('meta_type', 'website')

@push('structured_data')
<script type="application/ld+json">{!! json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@push('styles')
<link href="{{ asset('assets/css/courses.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!-- Header -->
    

    <!-- Course Hero Section -->
    <section class="course-hero py-5 position-relative">
        <div class="container position-relative">
            <div class="row align-items-center text-white">
                <div class="col-lg-8">
                    

                    <div class="mb-3">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                            {{ $course->category->name ?? 'General' }}</span>
                        <span class="badge bg-success text-white px-3 py-2 rounded-pill ms-2">
                            {{ ucfirst($course->level) }}</span>
                    </div>

                    <h1 class="display-4 fw-bold mb-3">{{ $course->title }}</h1>
                    <div class="lead mb-4 course-hero-desc">{!! $course->description !!}</div>

                    <div class="course-stats p-4 mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-people fs-3 mb-2 d-block"></i>
                                    <h5 class="mb-1">{{ number_format($course->enrolled_count) }}</h5>
                                    <small>Students</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-star-fill fs-3 mb-2 d-block text-warning"></i>
                                    <h5 class="mb-1">{{ $course->rating }}</h5>
                                    <small>Rating</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-clock fs-3 mb-2 d-block"></i>
                                    <h5 class="mb-1">{{ $course->duration_hours }}h</h5>
                                    <small>Duration</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-award fs-3 mb-2 d-block"></i>
                                    <h5 class="mb-1">Certificate</h5>
                                    <small>Included</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @auth
                        @php
                            $studentProfileComplete = auth()->user()->studentProfile?->is_complete ?? false;
                        @endphp
                        @if(!$isEnrolled && !$studentProfileComplete)
                            <div class="alert alert-warning d-flex align-items-center gap-2 mb-3 rounded-3" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <div>
                                    <strong>Profile Incomplete.</strong>
                                    You must <a href="{{ route('student.profile-details') }}" class="alert-link">complete your profile details</a> before you can enroll.
                                </div>
                            </div>
                        @endif
                    @endauth

                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        @auth
                            @if($isEnrolled)
                                <button class="btn btn-success btn-lg" disabled>
                                    <i class="bi bi-check-circle me-2"></i>Already Enrolled
                                </button>
                            @else
                                <button class="btn enroll-btn text-white btn-lg"
                                        id="enrollBtn"
                                        data-url="{{ parse_url(route('courses.enroll', $course->id), PHP_URL_PATH) }}">
                                    <i class="bi bi-mortarboard me-2"></i>Enroll Now
                                </button>
                            @endif

                            <button class="btn {{ $isWishlisted ? 'btn-success' : 'btn-outline-light' }} btn-lg"
                                    id="wishlistBtn"
                                    data-url="{{ parse_url(route('courses.wishlist.toggle', $course->id), PHP_URL_PATH) }}"
                                    data-wishlisted="{{ $isWishlisted ? '1' : '0' }}">
                                <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }} me-2"></i>
                                {{ $isWishlisted ? 'In Wishlist' : 'Add to Wishlist' }}
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn enroll-btn text-white btn-lg">
                                <i class="bi bi-mortarboard me-2"></i>Login to Enroll
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-heart me-2"></i>Login to Wishlist
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-4 text-center">
                    <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&h=300&fit=crop' }}"
                        alt="{{ $course->title }}" class="course-thumbnail img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Course Content -->
    <div class="container py-5">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Course Overview -->
                <section class="mb-5">
                    <h2 class="fw-bold mb-4" style="color: #1F8FFF;">
                        <i class="bi bi-info-circle me-2"></i>Course Overview
                    </h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="mb-4 course-overview-desc">{!! $course->description !!}</div>

                            <h5 class="fw-bold mb-3">Course Details:</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Level: {{ ucfirst($course->level) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Duration: {{ $course->duration_hours }} hours</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Category: {{ $course->category->name ?? 'General' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>{{ number_format($course->enrolled_count) }} Students Enrolled</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>Rating: {{ $course->rating }} / 5</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <span>{{ $course->total_reviews }} Reviews</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mb-5">
                    <h2 class="fw-bold mb-4" style="color: #1F8FFF;">
                        <i class="bi bi-bullseye me-2"></i>What You Will Learn
                    </h2>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @forelse($lessons->take(6) as $lesson)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                            <span>Build confidence with {{ $lesson->title }}.</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                            <span>Build practical {{ ucfirst($course->level) }} skills through guided online learning.</span>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Course Curriculum -->
                <section class="mb-5">
                    <h2 class="fw-bold mb-4" style="color: #1F8FFF;">
                        <i class="bi bi-list-ul me-2"></i>Course Curriculum
                    </h2>

                    @php
                        $totalDuration = $lessons->sum('duration_minutes');
                    @endphp

                    @if($lessons->isEmpty())
                        <div class="alert alert-light text-center">
                            <i class="bi bi-calendar display-4 text-muted"></i>
                            <p class="mt-3 text-muted">Curriculum coming soon. Check back later!</p>
                        </div>
                    @else
                        <div class="d-flex align-items-center mb-3 text-muted">
                            <i class="bi bi-collection me-2"></i>
                            <span>{{ $lessons->count() }} lessons</span>
                            <span class="mx-2">•</span>
                            <i class="bi bi-clock me-2"></i>
                            <span>{{ floor($totalDuration / 60) }}h {{ $totalDuration % 60 }}m</span>
                        </div>

                        <div class="accordion" id="curriculumAccordion">
                            @foreach($lessons as $index => $lesson)
                            <div class="curriculum-item mb-3 p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-3">{{ $lesson->order }}</span>
                                        <div>
                                            <h6 class="mb-1">{{ $lesson->title }}</h6>
                                            <p class="text-muted mb-0 small">
                                                <i class="bi bi-clock me-1"></i>{{ $lesson->duration_minutes }} min
                                                @if($lesson->description)
                                                    <span class="ms-2">• {{ Str::limit($lesson->description, 50) }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($lesson->video_url)
                                        <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-play-circle"></i> Watch
                                        </a>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-lock me-1"></i>Video Unavailable</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="mb-5">
                    <h2 class="fw-bold mb-4" style="color: #1F8FFF;">
                        <i class="bi bi-question-circle me-2"></i>Course FAQs
                    </h2>
                    <div class="accordion" id="courseFaqAccordion">
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="courseFaqFreeHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#courseFaqFree">
                                    Is {{ $course->title }} free?
                                </button>
                            </h3>
                            <div id="courseFaqFree" class="accordion-collapse collapse show" data-bs-parent="#courseFaqAccordion">
                                <div class="accordion-body">Yes. This Edvora course is free for learners.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="courseFaqLevelHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#courseFaqLevel">
                                    What level is this course?
                                </button>
                            </h3>
                            <div id="courseFaqLevel" class="accordion-collapse collapse" data-bs-parent="#courseFaqAccordion">
                                <div class="accordion-body">This course is designed for {{ $course->level }} learners.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="courseFaqDurationHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#courseFaqDuration">
                                    How long does this course take?
                                </button>
                            </h3>
                            <div id="courseFaqDuration" class="accordion-collapse collapse" data-bs-parent="#courseFaqAccordion">
                                <div class="accordion-body">The course includes approximately {{ $course->duration_hours }} hours of learning content.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="courseFaqEnrollmentHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#courseFaqEnrollment">
                                    How do I register?
                                </button>
                            </h3>
                            <div id="courseFaqEnrollment" class="accordion-collapse collapse" data-bs-parent="#courseFaqAccordion">
                                <div class="accordion-body">Create an Edvora account, complete your profile, and select Enroll Now to join this course.</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Student Reviews -->
                <section class="mb-5">
                    <h2 class="fw-bold mb-4" style="color: #1F8FFF;">
                        <i class="bi bi-star me-2"></i>Student Reviews
                    </h2>
                    @if($course->total_reviews > 0)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            This course has {{ $course->total_reviews }} reviews with an average rating of {{ $course->rating }}.
                        </div>
                    @else
                        <div class="alert alert-light text-center">
                            <i class="bi bi-chat-square-text display-4 text-muted"></i>
                            <p class="mt-3 text-muted">No reviews yet. Be the first to review this course!</p>
                        </div>
                    @endif
                </section>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Instructor Info -->
                <div class="instructor-card p-4 mb-4">
                    <h4 class="fw-bold mb-3" style="color: #1F8FFF;">Your Instructor</h4>
                    <div class="text-center mb-3">
                        <img src="{{ $course->teacher && $course->teacher->avatar ? (str_starts_with($course->teacher->avatar, 'http') ? $course->teacher->avatar : asset('storage/' . $course->teacher->avatar)) : 'https://ui-avatars.com/api/?name=' . urlencode(($course->teacher && $course->teacher->name) ? $course->teacher->name : 'Instructor') . '&size=150' }}"
                            alt="{{ ($course->teacher && $course->teacher->name) ? $course->teacher->name : 'Instructor' }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit:cover;">
                        <h5 class="mb-1">{{ ($course->teacher && $course->teacher->name) ? $course->teacher->name : 'Edvora Instructor' }}</h5>
                        <p class="text-muted mb-0">{{ ($course->teacher && $course->teacher->department) ? $course->teacher->department : ($course->category->name ?? '') }}</p>
                    </div>
                    @php
                        $bio = ($course->teacher && $course->teacher->bio) ? $course->teacher->bio : '';
                        $bio = preg_replace('/^(\S{3,20})\1{3,}$/', '', $bio);
                        $bio = \Illuminate\Support\Str::limit($bio, 150);
                    @endphp
                    <p class="text-center mb-3">
                        {{ $bio ?: 'Expert instructor with years of industry experience.' }}
                    </p>
                    @php $teacherProfile = ($course->teacher && $course->teacher->teacher) ? $course->teacher->teacher : null; @endphp
                    <div class="text-center">
                        <div class="row g-2">
                            <div class="col-4">
                                <small class="text-muted d-block">Students</small>
                                <strong>{{ $teacherProfile ? number_format($teacherProfile->total_students) : 'N/A' }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Courses</small>
                                <strong>{{ $teacherProfile ? $teacherProfile->total_courses : 'N/A' }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Rating</small>
                                <strong>{{ $teacherProfile ? $teacherProfile->rating : 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Features -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3" style="color: #1F8FFF;">Free Course Benefits</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Free Access</li>
                            @if($course->has_lifetime_access)
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Lifetime Access</li>
                            @endif
                            @if($course->has_mobile_access)
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile & Desktop</li>
                            @endif
                            @if($course->has_certificate)
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Certificate of Completion</li>
                            @endif
                            @if($course->has_downloadable_resources)
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Downloadable Resources</li>
                            @endif
                            @if($course->has_community_access)
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Community Access</li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3" style="color: #1F8FFF;">Course Info</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @if($course->show_category_badge !== false)
                            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $course->category->name ?? 'General' }}</span>
                            @endif
                            @if($course->show_level_badge !== false)
                            <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($course->level) }}</span>
                            @endif
                            @if($course->show_duration_badge !== false)
                            <span class="badge bg-info rounded-pill px-3 py-2">{{ $course->duration_hours }}h Content</span>
                            @endif
                            @if($course->show_certificate_badge !== false)
                            <span class="badge bg-success rounded-pill px-3 py-2">Certificate</span>
                            @endif
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Free Access</span>
                            @if($course->show_students_badge !== false)
                            <span class="badge bg-dark rounded-pill px-3 py-2">{{ number_format($course->enrolled_count) }} Students</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Related Courses -->
                @if($related->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3" style="color: #1F8FFF;">Related Courses</h5>
                        <div class="d-grid gap-3">
                            @foreach($related as $rel)
                            <a href="{{ route('courses.detail', $rel->slug) }}" class="text-decoration-none">
                                <div class="d-flex">
                                    <img src="{{ $rel->thumbnail ? asset('storage/' . $rel->thumbnail) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=80&h=60&fit=crop' }}"
                                        alt="{{ $rel->title }}" class="rounded me-3" style="width: 80px; height: 60px; object-fit:cover;">
                                    <div>
                                        <h6 class="mb-1 text-dark">{{ $rel->title }}</h6>
                                        <small class="text-muted">{{ number_format($rel->enrolled_count) }} students</small>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modern Footer -->
    

    <!-- Bootstrap 5 JS -->
    
    <!-- Custom JS -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/course-detail.js') }}"></script>
@endpush
