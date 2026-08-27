@extends('layouts.app')

@section('title', 'Our Courses - Edvora Tech')

@push('styles')
    <link href="{{ asset('assets/css/glass-panel.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/courses-pages.css') }}" rel="stylesheet" />
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
        <section class="py-4 gray courses-filter-section">
          <div class="container">

            <!-- Filter Tabs Pills -->
            <div class="courses-filter-tabs-wrapper mb-3">
              <a href="{{ route('courses.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'all'])) }}"
                 class="filter-tab-pill {{ (!request('filter') || request('filter') === 'all') ? 'active' : '' }}">
                <span class="tab-icon"><i class="bi bi-grid-fill"></i></span>
                <span>All Courses</span>
                <span class="tab-count">{{ $counts['all'] ?? 0 }}</span>
              </a>

              <a href="{{ route('courses.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'vip'])) }}"
                 class="filter-tab-pill vip-tab {{ request('filter') === 'vip' ? 'active' : '' }}">
                <span class="tab-icon text-warning"><i class="bi bi-star-fill"></i></span>
                <span>VIP Courses</span>
                <span class="tab-count">{{ $counts['vip'] ?? 0 }}</span>
              </a>

              <a href="{{ route('courses.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'upcoming'])) }}"
                 class="filter-tab-pill {{ request('filter') === 'upcoming' ? 'active' : '' }}">
                <span class="tab-icon text-success"><i class="bi bi-rocket-takeoff-fill"></i></span>
                <span>Starting Soon</span>
                <span class="tab-count">{{ $counts['upcoming'] ?? 0 }}</span>
              </a>

              <a href="{{ route('courses.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'finished'])) }}"
                 class="filter-tab-pill {{ request('filter') === 'finished' ? 'active' : '' }}">
                <span class="tab-icon"><i class="bi bi-check2-circle"></i></span>
                <span>Completed</span>
                <span class="tab-count">{{ $counts['finished'] ?? 0 }}</span>
              </a>

              <a href="{{ route('courses.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'popular'])) }}"
                 class="filter-tab-pill {{ request('filter') === 'popular' ? 'active' : '' }}">
                <span class="tab-icon text-danger"><i class="bi bi-fire"></i></span>
                <span>Most Enrolled</span>
                <span class="tab-count">{{ $counts['popular'] ?? 0 }}</span>
              </a>
            </div>

            <!-- Filter Controls Bar -->
            <form method="GET" action="{{ route('courses.index') }}" id="filterForm">
              <input type="hidden" name="filter" id="activeFilterInput" value="{{ request('filter', 'all') }}" />

              <div class="courses-filter-bar">
                <div class="row g-3 align-items-center">
                  <div class="col-lg-5 col-md-12">
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-search"></i>
                      </span>
                      <input
                        type="text"
                        class="form-control"
                        name="search"
                        id="searchCourses"
                        placeholder="Search by title, topic or keywords..."
                        value="{{ request('search') }}"
                        autocomplete="off"
                      />
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-4 col-sm-6">
                    <select class="form-select" name="category" id="categoryFilter">
                      <option value="">All Categories</option>
                      @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>
                          {{ $cat->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-6">
                    <select class="form-select" name="level" id="levelFilter">
                      <option value="">All Levels</option>
                      <option value="beginner"     {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                      <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                      <option value="advanced"     {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-12">
                    <select class="form-select" name="sort" id="sortBy">
                      <option value="newest"  {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
                      <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                      <option value="rating"  {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    </select>
                  </div>
                </div>
              </div>
            </form>

            <div id="courses-grid-container">
                @include('courses.partials.course-list')
            </div>

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
