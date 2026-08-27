@extends('layouts.app')

@section('title', 'Your Courses - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/your-courses.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/events-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/beta-notice.css') }}" rel="stylesheet" />
@endpush

@section('hide_header', true)
@section('hide_footer', true)

@section('content')
<!-- Navbar -->
    

    <!-- Dashboard Layout -->
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <x-student-sidebar />

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
                                <h1 class="display-5 fw-bold mb-3">Your Course Hub</h1>
                                <p class="lead text-white-50 mb-4">You are enrolled in
                                    <span class="text-white fw-bold">{{ $enrollments->total() }} course(s)</span>.
                                    Keep learning and track your progress below.</p>
                            </div>
                            <div class="col-lg-4 d-none d-lg-block text-center">
                                <i class="bi bi-mortarboard" style="font-size: 8rem; color: rgba(255,255,255,0.2);"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row g-4 mb-5">
                    @php
                        $totalCourses    = $enrollments->total();
                        $completedCount  = $user->enrollments()->where('status', 'completed')->count();
                    @endphp
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-primary text-white"><i class="bi bi-journal-text"></i></div>
                            <div>
                                <small class="text-muted d-block">Total Enrolled</small>
                                <h4 class="fw-bold mb-0">{{ $totalCourses }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-success text-white"><i class="bi bi-check-circle"></i></div>
                            <div>
                                <small class="text-muted d-block">Completed</small>
                                <h4 class="fw-bold mb-0">{{ $completedCount }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-warning text-dark"><i class="bi bi-graph-up"></i></div>
                            <div>
                                <small class="text-muted d-block">Avg. Progress</small>
                                <h4 class="fw-bold mb-0">{{ round($avgProgress) }}%</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="quick-stat-card">
                            <div class="stat-icon-box bg-info text-white"><i class="bi bi-mortarboard"></i></div>
                            <div>
                                <small class="text-muted d-block">In Progress</small>
                                <h4 class="fw-bold mb-0">{{ $user->enrollments()->where('status', 'active')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Control Bar -->
                <form method="GET" action="{{ route('student.courses') }}" id="filterForm">
                <div class="glass-control-bar mb-5">
                    <div class="row align-items-center g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-search text-muted fs-5"></i>
                                <input type="text" id="searchCourses" name="search" class="form-control search-input-premium"
                                    placeholder="Find your course by title..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="categoryFilter" name="category" class="form-select filter-select-premium" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 text-end d-none d-md-block">
                            <span class="text-muted small">Showing all your enrolled courses</span>
                        </div>
                    </div>
                </div>
                </form>

                <!-- Courses Grid -->
                @if($enrollments->isEmpty())
                <div class="empty-courses-state text-center py-5">
                    <div class="empty-icon-wrapper mx-auto mb-4">
                        <i class="bi bi-journal-x"></i>
                    </div>
                    <h4 class="fw-bold text-muted mb-2">No courses found</h4>
                    <p class="text-muted mb-4">Try adjusting your search or explore new courses to get started.</p>
                    <a href="{{ route('courses.index') }}" class="btn-premium-cta">
                        <i class="bi bi-compass me-2"></i>Explore Courses
                    </a>
                </div>
                @else
                <div class="row g-4" id="coursesGrid">
                    @foreach($enrollments as $enrollment)
                    @php $p = $enrollment->actual_progress ?? 0; @endphp
                    <div class="col-xl-4 col-md-6">
                        <div class="course-card-premium" style="animation-delay: {{ $loop->index * 0.08 }}s">

                            <!-- Card Image -->
                            <div class="card-image-top">
                                <img src="{{ $enrollment->course->thumbnail ? asset('storage/' . $enrollment->course->thumbnail) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=300&fit=crop' }}"
                                    alt="{{ $enrollment->course->title }}">
                                <div class="category-overlay">
                                    {{ $enrollment->course->category->name ?? 'General' }}
                                </div>
                                <div class="status-badge-overlay
                                    {{ $enrollment->status === 'completed' ? 'bg-success' : 'bg-primary' }}">
                                    {{ $enrollment->status === 'completed' ? 'Completed' : 'In Progress' }}
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body-premium">
                                <h5 class="course-title-premium">{{ $enrollment->course->title }}</h5>
                                <div class="student-count-mini">
                                    <i class="bi bi-person-fill text-primary"></i>
                                    <span>{{ $enrollment->course->teacher->name ?? 'Instructor' }}</span>
                                </div>

                                <!-- Progress Bar -->
                                <div class="progress-section mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted fw-semibold">Progress</small>
                                        <small class="fw-bold
                                            {{ $p >= 80 ? 'text-success' : ($p >= 40 ? 'text-primary' : 'text-warning') }}">
                                            {{ $p }}%
                                        </small>
                                    </div>
                                    <div class="progress-bar-track">
                                        <div class="progress-bar-fill
                                            {{ $p >= 80 ? 'fill-success' : ($p >= 40 ? 'fill-primary' : 'fill-warning') }}"
                                            style="width: {{ $p }}%">
                                        </div>
                                    </div>
                                    <div class="text-end mt-1">
                                        <small class="text-muted">
                                            {{ $enrollment->completed_lessons_count ?? 0 }} / {{ $enrollment->total_lessons_count ?? 0 }} lessons completed
                                        </small>
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer-premium">
                                    <a href="{{ route('student.courses.learn', $enrollment->course->slug) }}"
                                       class="btn-continue-course">
                                        <i class="bi bi-{{ $enrollment->status === 'completed' ? 'arrow-repeat' : 'play-fill' }} me-1"></i>
                                        {{ $enrollment->status === 'completed' ? 'Review' : 'Continue' }}
                                    </a>
                                    <div class="course-meta-mini">
                                        @if($enrollment->course->duration_hours)
                                        <span class="meta-chip">
                                            <i class="bi bi-clock"></i> {{ $enrollment->course->duration_hours }}h
                                        </span>
                                        @endif
                                        @if($enrollment->course->level)
                                        <span class="meta-chip">
                                            <i class="bi bi-bar-chart-steps"></i> {{ ucfirst($enrollment->course->level) }}
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
                    {{ $enrollments->links() }}
                </div>
                @endif
            </div>

        </main>
    </div>

    <!-- Modern Footer -->
    

    <!-- Modals (Reused from Dashboard) -->
    <div class="modal fade" id="createCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content glass-card-premium border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary">Create Your New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="createCourseForm">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Course Title</label>
                                <input type="text" class="form-control rounded-pill px-4"
                                    placeholder="e.g. Master React & Redux">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Category</label>
                                <select class="form-select rounded-pill px-4">
                                    <option>Programming</option>
                                    <option>Design</option>
                                    <option>Data Science</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Price ($)</label>
                                <input type="number" class="form-control rounded-pill px-4" placeholder="99.99">
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="button" class="btn-premium-primary rounded-pill px-5 py-2">Create
                                    Course</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/student-dashboard.js') }}"></script>
<script src="{{ asset('assets/js/your-courses.js') }}"></script>
<script src="{{ asset('assets/js/modern-footer.js') }}"></script>
<script src="{{ asset('assets/js/beta-notice.js') }}"></script>
@endpush
