            @if(request('search') || request('category') || request('level') || (request('filter') && request('filter') !== 'all'))
              <div class="filter-active-summary">
                <span class="summary-text">
                  <i class="bi bi-funnel me-1 text-primary"></i>
                  Showing filtered results
                  @if(request('filter') && request('filter') !== 'all')
                    &bull; <strong>{{ ucfirst(request('filter')) }}</strong>
                  @endif
                  @if(request('category'))
                    &bull; Category: <strong>{{ ucfirst(request('category')) }}</strong>
                  @endif
                  @if(request('level'))
                    &bull; Level: <strong>{{ ucfirst(request('level')) }}</strong>
                  @endif
                  @if(request('search'))
                    &bull; Search: <em>"{{ request('search') }}"</em>
                  @endif
                </span>
                <a href="{{ route('courses.index') }}" class="clear-filters-btn" id="clearFiltersBtn">
                  <i class="bi bi-x-circle"></i> Reset All Filters
                </a>
              </div>
            @endif

            @if($courses->isEmpty())
                  <div class="text-center py-5">
                    <div class="mb-3 text-muted" style="font-size: 3rem;">
                      <i class="bi bi-journal-x"></i>
                    </div>
                    <h4 class="fw-bold text-muted mb-2">No courses found</h4>
                    <p class="text-muted mb-3">No courses match your selected filter criteria.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-primary px-4 clear-filters-btn">Clear All Filters</a>
                  </div>
            @else
                  @php
                      $groupedCourses = collect($courses->items())->groupBy(function($c) { return $c->category ? $c->category->name : 'General'; });
                  @endphp

                  @foreach($groupedCourses as $categoryName => $categoryCourses)
                      <div class="mb-5 category-group-section">
                        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3 border-opacity-10">
                            <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded" style="width: 40px; height: 40px;">
                                <i class="bi bi-book-half fs-5"></i>
                            </div>
                            <div>
                                <h2 class="h3 fw-bold mb-1" style="color: var(--bs-heading-color);">{{ $categoryName }}</h2>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                    {{ $categoryCourses->count() }} {{ Str::plural('Program', $categoryCourses->count()) }}
                                </p>
                            </div>
                        </div>

                        <div class="row g-3 g-xl-4">
                          @foreach($categoryCourses as $course)
                            @php
                              $today = \Carbon\Carbon::today();
                              $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date)->startOfDay() : null;
                              $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date)->startOfDay() : null;

                              // Date & Countdown Status Calculation
                              $dateStatus = [
                                  'type' => 'open', // upcoming, ongoing, finished, open
                                  'badge_text' => 'Open Course',
                                  'countdown_text' => 'Flexible Schedule',
                                  'icon' => 'bi bi-lightning-charge-fill',
                                  'pulse' => false,
                              ];

                              if ($startDate && $startDate->greaterThan($today)) {
                                  // Course starts in future
                                  $daysUntilStart = (int) $today->diffInDays($startDate, false);
                                  $dateStatus['type'] = 'upcoming';
                                  $dateStatus['icon'] = 'bi bi-rocket-takeoff-fill';
                                  $dateStatus['pulse'] = true;

                                  if ($daysUntilStart === 0) {
                                      $dateStatus['badge_text'] = 'Starts Today';
                                      $dateStatus['countdown_text'] = 'Starts today';
                                  } elseif ($daysUntilStart === 1) {
                                      $dateStatus['badge_text'] = 'Starts Tomorrow';
                                      $dateStatus['countdown_text'] = '1 day left until start';
                                  } else {
                                      $dateStatus['badge_text'] = "Starts in {$daysUntilStart} days";
                                      $dateStatus['countdown_text'] = "{$daysUntilStart} days left until start";
                                  }
                              } elseif (
                                  (($startDate && $startDate->lessThanOrEqualTo($today)) || $course->started_at !== null || $course->status === 'started')
                                  && ($endDate && $endDate->greaterThanOrEqualTo($today))
                              ) {
                                  // Course is running & has upcoming end date
                                  $daysUntilEnd = (int) $today->diffInDays($endDate, false);
                                  $dateStatus['type'] = 'ongoing';
                                  $dateStatus['icon'] = 'bi bi-hourglass-split';
                                  $dateStatus['pulse'] = true;

                                  if ($daysUntilEnd === 0) {
                                      $dateStatus['badge_text'] = 'Ends Today';
                                      $dateStatus['countdown_text'] = 'Ends today';
                                  } elseif ($daysUntilEnd === 1) {
                                      $dateStatus['badge_text'] = 'Ends Tomorrow';
                                      $dateStatus['countdown_text'] = '1 day left until class ends';
                                  } else {
                                      $dateStatus['badge_text'] = "Ends in {$daysUntilEnd} days";
                                      $dateStatus['countdown_text'] = "{$daysUntilEnd} days left until class ends";
                                  }
                              } elseif (($endDate && $endDate->lessThan($today)) || in_array($course->status, ['archived', 'completed'])) {
                                  // Course finished
                                  $dateStatus['type'] = 'finished';
                                  $dateStatus['icon'] = 'bi bi-check2-circle';
                                  $dateStatus['badge_text'] = 'Completed';
                                  $dateStatus['countdown_text'] = 'Course completed';
                                  $dateStatus['pulse'] = false;
                              } elseif ($startDate && $startDate->lessThanOrEqualTo($today)) {
                                  // Course started without fixed end date
                                  $dateStatus['type'] = 'ongoing';
                                  $dateStatus['icon'] = 'bi bi-play-circle-fill';
                                  $dateStatus['badge_text'] = 'In Progress';
                                  $dateStatus['countdown_text'] = 'Class in progress';
                                  $dateStatus['pulse'] = true;
                              }
                            @endphp
                            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-6">
                              <article class="edvora-glass-course-card">
                                <!-- Card Top Glass Specular Glow -->
                                <div class="glass-card-specular"></div>

                                <!-- Thumbnail Box -->
                                <div class="edvora-card-thumb-box" style="position: relative; aspect-ratio: 16/9.5; max-height: 210px; overflow: hidden; border-radius: 16px;">
                                  <!-- Live Status Badge (Top-Start) -->
                                  <div class="edvora-floating-status-pill status-{{ $dateStatus['type'] }}">
                                    @if($dateStatus['pulse'])
                                      <span class="status-live-dot"></span>
                                    @endif
                                    <i class="{{ $dateStatus['icon'] }}"></i>
                                    <span>{{ $dateStatus['badge_text'] }}</span>
                                  </div>

                                  <!-- VIP Badge (Top-End) -->
                                  @if($course->is_featured)
                                    <div class="edvora-floating-vip-badge">
                                      <i class="bi bi-star-fill"></i> VIP
                                    </div>
                                  @endif

                                  <!-- Floating Category Tag (Bottom-Start) -->
                                  <div class="edvora-floating-category">
                                    <span>{{ $course->category ? $course->category->name : 'Technology' }}</span>
                                  </div>

                                  <!-- Floating Level Pill (Bottom-End) -->
                                  @if($course->level)
                                    <div class="edvora-floating-level">
                                      <span>{{ ucfirst($course->level) }}</span>
                                    </div>
                                  @endif

                                  <!-- Image with Smooth Zoom Effect -->
                                  <a href="{{ route('courses.detail', $course->slug) }}" class="edvora-thumb-link" tabindex="-1" aria-hidden="true" style="display: block; width: 100%; height: 100%;">
                                    <img src="{{ $course->thumbnail ? (Str::startsWith($course->thumbnail, ['http://', 'https://']) ? $course->thumbnail : asset('storage/' . $course->thumbnail)) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop' }}"
                                         alt="{{ $course->title }}"
                                         class="edvora-card-img"
                                         style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                         loading="lazy"
                                         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop';">
                                  </a>
                                </div>

                                <!-- Card Content Body -->
                                <div class="edvora-card-content">
                                  <!-- Title -->
                                  <h3 class="edvora-card-title">
                                    <a href="{{ route('courses.detail', $course->slug) }}" title="{{ $course->title }}">
                                      {{ $course->title }}
                                    </a>
                                  </h3>

                                  <!-- Short Excerpt -->
                                  <p class="edvora-card-excerpt">
                                    {{ Str::limit(strip_tags($course->description ?? 'Gain practical skills and hands-on experience in this comprehensive program.'), 80) }}
                                  </p>

                                  <!-- Glass Timeline & Schedule Bar -->
                                  <div class="edvora-schedule-capsule schedule-{{ $dateStatus['type'] }}">
                                    <div class="capsule-countdown-row">
                                      <i class="{{ $dateStatus['icon'] }}"></i>
                                      <span class="countdown-text">{{ $dateStatus['countdown_text'] }}</span>
                                    </div>
                                    <div class="capsule-date-row">
                                      <i class="bi bi-calendar-event"></i>
                                      <span>
                                        @if($course->start_date && $course->end_date)
                                          {{ $course->start_date->format('M d') }} - {{ $course->end_date->format('M d, Y') }}
                                        @elseif($course->start_date)
                                          Starts {{ $course->start_date->format('M d, Y') }}
                                        @elseif($course->end_date)
                                          Ends {{ $course->end_date->format('M d, Y') }}
                                        @else
                                          Flexible Schedule
                                        @endif
                                      </span>
                                    </div>
                                  </div>

                                  <!-- Meta Info & CTA Footer -->
                                  <div class="edvora-card-footer">
                                    <div class="edvora-card-meta-chips">
                                      <span class="meta-chip meta-chip--free">
                                        <i class="bi bi-gift-fill me-1"></i> Free
                                      </span>
                                      @if($course->duration_hours)
                                        <span class="meta-chip">
                                          <i class="bi bi-stopwatch me-1"></i> {{ $course->duration_hours }}h
                                        </span>
                                      @endif
                                    </div>

                                    <a href="{{ route('courses.detail', $course->slug) }}" class="edvora-card-cta-btn">
                                      <span>Explore</span>
                                      <i class="bi bi-arrow-right"></i>
                                    </a>
                                  </div>
                                </div>
                              </article>
                            </div>
                          @endforeach
                        </div>
                      </div>
                  @endforeach

                  <!-- Pagination -->
                  <div class="d-flex justify-content-center mt-4 pt-2 pagination-container">
                    {{ $courses->links('pagination::bootstrap-5') }}
                  </div>
            @endif
