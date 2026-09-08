@extends('layouts.app')

@section('title', 'My Courses - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/your-courses.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
@endpush

@section('content')

    <!-- Dashboard Layout -->
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <x-teacher-sidebar />

        <!-- Main Content -->
        <main class="main-content">
            <div class="container-fluid py-5 px-xl-5">

                <!-- Hub Banner -->
                <div class="courses-hub-banner">
                    <div class="hub-bg-animation">
                        <div class="floating-hub-icon hub-icon-1"><i class="bi bi-mortarboard"></i></div>
                        <div class="floating-hub-icon hub-icon-2"><i class="bi bi-book"></i></div>
                        <div class="floating-hub-icon hub-icon-3"><i class="bi bi-lightbulb"></i></div>
                    </div>
                    <div class="hub-content">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="display-5 fw-bold mb-3">My Course Hub</h1>
                                <p class="lead text-white-50 mb-0">
                                    Manage, organize, and expand your educational impact. You're currently mentoring
                                    <span class="text-white fw-bold">{{ $totalStudents }} students</span>
                                    across all your programs.
                                </p>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block text-center">
                                <i class="bi bi-mortarboard" style="font-size: 8rem; color: rgba(255,255,255,0.2);"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row g-4 mb-5">
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-primary text-white"><i class="bi bi-journal-text"></i></div>
                            <div>
                                <small class="text-muted d-block">Total Courses</small>
                                <h4 class="fw-bold mb-0">{{ $totalCourses }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-success text-white"><i class="bi bi-people"></i></div>
                            <div>
                                <small class="text-muted d-block">Active Students</small>
                                <h4 class="fw-bold mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-warning text-dark"><i class="bi bi-star"></i></div>
                            <div>
                                <small class="text-muted d-block">Avg. Rating</small>
                                <h4 class="fw-bold mb-0">{{ number_format($avgRating, 1) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-info text-white"><i class="bi bi-graph-up"></i></div>
                            <div>
                                <small class="text-muted d-block">Active Courses</small>
                                <h4 class="fw-bold mb-0">{{ $activeCourses }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Control Bar -->
                <form method="GET" action="{{ route('teacher.your-courses') }}" id="filterForm">
                <div class="glass-control-bar mb-5">
                    <div class="row align-items-center g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-search text-muted fs-5"></i>
                                <input type="text" id="searchCourses" name="search"
                                    class="form-control search-input-premium"
                                    placeholder="Find my course by title..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="categoryFilter" name="category"
                                class="form-select filter-select-premium"
                                onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 text-end d-none d-md-block">
                            <span class="text-muted small">Showing all my published courses</span>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Courses Grid -->
                @if($courses->isEmpty())
                <div class="empty-courses-state text-center py-5">
                    <div class="empty-icon-wrapper mx-auto mb-4">
                        <i class="bi bi-journal-x"></i>
                    </div>
                    <h4 class="fw-bold text-muted mb-2">No courses assigned</h4>
                    <p class="text-muted mb-0">You do not have any assigned courses at the moment.</p>
                </div>
                @else
                <div class="row g-4" id="coursesGrid">
                    @foreach($courses as $course)
                    <div class="col-xl-4 col-md-6">
                        <div class="course-card-premium" style="animation-delay: {{ $loop->index * 0.08 }}s">

                            <!-- Card Image -->
                            <div class="card-image-top">
                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=300&fit=crop' }}"
                                    alt="{{ $course->title }}">
                                <div class="category-overlay">
                                    {{ $course->category->name ?? 'General' }}
                                </div>
                                <div class="status-badge-overlay
                                    {{ $course->status === 'active' ? 'bg-success' : ($course->status === 'draft' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ ucfirst($course->status ?? 'Draft') }}
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body-premium">
                                <h5 class="course-title-premium">{{ $course->title }}</h5>
                                <div class="student-count-mini">
                                    <i class="bi bi-people-fill text-primary"></i>
                                    <span>{{ $course->enrolled_count ?? 0 }} Students Enrolled</span>
                                </div>

                                <!-- Rating -->
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= round($course->rating ?? 0) ? '-fill' : '' }}" style="font-size:0.8rem;"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted fw-semibold">{{ number_format($course->rating ?? 0, 1) }}</small>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer-premium">
                                    <a href="{{ route('teacher.courses.detail', $course->id) }}"
                                       class="btn-continue-course">
                                        <i class="bi bi-eye me-1"></i> View Details
                                    </a>
                                    <div class="course-meta-mini">
                                        @if($course->duration_hours)
                                        <span class="meta-chip">
                                            <i class="bi bi-clock"></i> {{ $course->duration_hours }}h
                                        </span>
                                        @endif
                                        @if($course->level)
                                        <span class="meta-chip">
                                            <i class="bi bi-bar-chart-steps"></i> {{ ucfirst($course->level) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $courses->links() }}
                </div>
                @endif

            </div>
        </main>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/your-courses.js') }}"></script>
@endpush
