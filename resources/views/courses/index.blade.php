@extends('layouts.app')

@section('title', 'Our Courses - Edvora Tech')

@push('styles')
    <link href="{{ asset('assets/css/glass-panel.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <!-- Header -->

        <!-- Hero Section with Special Animation -->
        <section class="py-5 courses-hero-section">
          <!-- Animated Background -->
          <div class="courses-bg-animation">
            <!-- Floating Learning Icons -->
            <div class="floating-learning-icon icon-1">
              <i class="bi bi-book"></i>
            </div>
            <div class="floating-learning-icon icon-2">
              <i class="bi bi-lightbulb"></i>
            </div>
            <div class="floating-learning-icon icon-3">
              <i class="bi bi-mortarboard"></i>
            </div>
            <div class="floating-learning-icon icon-4">
              <i class="bi bi-trophy"></i>
            </div>
            <div class="floating-learning-icon icon-5">
              <i class="bi bi-star"></i>
            </div>
            <div class="floating-learning-icon icon-6">
              <i class="bi bi-award"></i>
            </div>

            <!-- Animated Particles -->
            <div class="learning-particle particle-1"></div>
            <div class="learning-particle particle-2"></div>
            <div class="learning-particle particle-3"></div>
            <div class="learning-particle particle-4"></div>
            <div class="learning-particle particle-5"></div>
            <div class="learning-particle particle-6"></div>
            <div class="learning-particle particle-7"></div>
            <div class="learning-particle particle-8"></div>

            <!-- Animated Lines -->
            <div class="animated-line line-1"></div>
            <div class="animated-line line-2"></div>
            <div class="animated-line line-3"></div>

            <!-- Floating Geometric Shapes -->
            <div class="geometric-shape shape-1"></div>
            <div class="geometric-shape shape-2"></div>
            <div class="geometric-shape shape-3"></div>
            <div class="geometric-shape shape-4"></div>
          </div>

          <div class="container position-relative">
            <div class="row align-items-center text-white">
              <div class="col-lg-8">
                <div class="hero-content">
                  <h1 class="display-4 fw-bold mb-3 hero-title">
                    <span
                      class="typewriter-text"
                      data-text="Explore Our Courses"
                    ></span>
                  </h1>
                  <p class="lead mb-4 hero-subtitle">
                    <span
                      class="typewriter-text"
                      data-text="Discover world-class courses taught by industry experts. From programming to design, data science to marketing - find your perfect learning path."
                    ></span>
                  </p>
                  <div class="d-flex gap-3 flex-wrap hero-stats">
                    <div class="stat-card stat-1">
                      <i class="bi bi-people me-2"></i>{{ number_format($totalStudents) }}+ Students
                    </div>
                    <div class="stat-card stat-2">
                      <i class="bi bi-book me-2"></i>{{ $totalCourses }}+ Courses
                    </div>
                    <div class="stat-card stat-3">
                      <i class="bi bi-award me-2"></i>{{ $totalCertificates }} Certificates
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 text-center">
                <div class="hero-image-container">
                  <div class="image-glow"></div>
                  <img
                    src="{{ asset('assets/images/hero_logo_design.png') }}"
                    alt="Edvora Tech"
                    class="hero-image"
                  />
                  <div class="image-overlay">
                    <div class="pulse-ring ring-1"></div>
                    <div class="pulse-ring ring-2"></div>
                    <div class="pulse-ring ring-3"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Search and Filter Section -->
        <section class="py-4 gray">
          <div class="container">
            <form method="GET" action="{{ route('courses.index') }}" id="filterForm">
              <div class="glass-panel-">
                <div class="row g-3">
                  <div class="col-lg-6">
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-search"></i>
                      </span>
                      <input
                        type="text"
                        class="form-control"
                        name="search"
                        id="searchCourses"
                        placeholder="Search courses..."
                        value="{{ request('search') }}"
                      />
                    </div>
                  </div>
                  <div class="col-lg-2">
                    <select class="form-select" name="category" id="categoryFilter" onchange="document.getElementById('filterForm').submit()">
                      <option value="">All Categories</option>
                      @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                          {{ $cat->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-lg-2">
                    <select class="form-select" name="level" id="levelFilter" onchange="document.getElementById('filterForm').submit()">
                      <option value="">All Levels</option>
                      <option value="beginner"     {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                      <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                      <option value="advanced"     {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                  </div>
                  <div class="col-lg-2">
                    <select class="form-select" name="sort" id="sortBy" onchange="document.getElementById('filterForm').submit()">
                      <option value="popular" {{ request('sort', 'popular') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                      <option value="newest"  {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                      <option value="rating"  {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </section>

        <!-- Courses Grid -->
        <section class="py-5 light">
          <div class="container">

            @if($courses->isEmpty())
                  <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No courses found</h4>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">Clear Filters</a>
                  </div>
            @else
                  <div class="row g-4">
                    @foreach($courses as $course)
                          <div class="col-lg-4 col-md-6">
                            <div class="card course-card h-100">
                              <div class="position-relative">
                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=400&fit=crop' }}"
                                     class="card-img-top" alt="{{ $course->title }}" style="height:200px;object-fit:cover;">
                                <div class="position-absolute top-0 start-0 m-2">
                                  @if($course->enrolled_count > 800)
                                    <span class="badge bg-danger">Popular</span>
                                  @endif
                                  @if($course->created_at && $course->created_at->diffInDays() < 30)
                                    <span class="badge bg-success">New</span>
                                  @endif
                                </div>
                              </div>
                              <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                  <span class="badge bg-primary">{{ $course->category->name ?? '' }}</span>
                                  <span class="badge bg-secondary ms-1">{{ ucfirst($course->level) }}</span>
                                </div>
                                <h5 class="card-title">{{ $course->title }}</h5>
                                <p class="text-muted mb-2">by {{ $course->teacher->name ?? 'Edvora Instructor' }}</p>
                                <p class="card-text small text-muted">{{ Str::limit($course->description, 100) }}</p>

                                <div class="mt-auto">
                                  <div class="d-flex align-items-center mb-3">
                                    <div class="me-2">
                                      @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= round($course->rating) ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                                      @endfor
                                    </div>
                                    <span class="text-muted small">({{ $course->rating }}) &bull; {{ number_format($course->enrolled_count) }} students</span>
                                  </div>

                                  <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted">
                                      <i class="bi bi-clock me-1"></i>{{ $course->duration_hours }}h
                                    </small>
                                    <small class="text-success fw-semibold">
                                      <i class="bi bi-unlock me-1"></i>Free
                                    </small>
                                  </div>

                                  <div class="d-flex gap-2">
                                    <a href="{{ route('courses.detail', $course->slug) }}" class="btn btn-primary flex-fill">
                                      Enroll Now
                                    </a>
                                    <a href="{{ route('courses.detail', $course->slug) }}" class="btn btn-outline-primary">
                                      Details
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                    @endforeach
                  </div>

                  <!-- Pagination -->
                  <div class="d-flex justify-content-center mt-5">
                    {{ $courses->links('pagination::bootstrap-5') }}
                  </div>
            @endif

          </div>
        </section>

        <!-- Newsletter Section -->
        <section class="py-5 gray">
          <div class="container">
            <div class="glass-panel-">
              <div class="row justify-content-center text-center">
                <div class="col-lg-6">
                  <h3 class="fw-bold mb-3">Stay Updated</h3>
                  <p class="text-muted mb-4">
                    Get notified about new courses, special offers, and learning
                    tips delivered to your inbox.
                  </p>
                  <form class="d-flex gap-2">
                    <input
                      type="email"
                      class="form-control"
                      placeholder="Enter your email"
                    />
                    <button
                      class="btn btn-primary"
                      type="submit"
                      style="background-color: #1f8fff; border-color: #1f8fff"
                    >
                      Subscribe
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Footer -->
        <!-- Modern Footer -->


        <!-- Bootstrap 5 JS -->

        <!-- Typewriter Animation JS -->

        <!-- Custom JS -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/typewriter.js') }}"></script>
    <script src="{{ asset('assets/js/courses.js') }}"></script>
    <script src="{{ asset('assets/js/modern-footer.js') }}"></script>
@endpush
