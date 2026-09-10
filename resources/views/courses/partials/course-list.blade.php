            @if(request('search') || request('category') || request('level') || (request('filter') && request('filter') !== 'all'))
              <div class="filter-active-summary mb-4">
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

            @if(($filter ?? request('filter')) === 'vip')
              <!-- VIP Spotlight Header Banner -->
              <div class="vip-spotlight-banner mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="vip-crown-badge-glow">
                      <i class="bi bi-star-fill text-primary fs-4"></i>
                    </div>
                    <div>
                      <h4 class="fw-bold mb-1" style="color: #0369a1;">Exclusive VIP Programs & Masterclasses</h4>
                      <p class="mb-0 text-muted small" style="font-size: 0.85rem;">
                        Advanced technology courses featuring 1-on-1 mentorship, live project reviews, priority support, and verified certification.
                      </p>
                    </div>
                  </div>
                  <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold shadow-sm" style="font-size: 0.82rem; background: linear-gradient(135deg, #1f8fff, #0070e0) !important;">
                    <i class="bi bi-gem me-1"></i> VIP Access
                  </span>
                </div>
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
                    $isVipFilter = ($filter ?? request('filter')) === 'vip';
                    $vipCourses = collect($courses->items())->filter(fn($c) => (bool)$c->is_featured);
                    $groupedCourses = collect($courses->items())->groupBy(function($c) { 
                        return $c->category ? $c->category->name : 'General'; 
                    });
                  @endphp

                  @if(!$isVipFilter && $vipCourses->isNotEmpty())
                    <!-- TOP VIP SPOTLIGHT SECTION (Always at the Very Top) -->
                    <div class="mb-5 vip-top-spotlight-section">
                      <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3 flex-wrap gap-2" style="border-color: rgba(245, 158, 11, 0.3) !important;">
                        <div class="d-flex align-items-center gap-3">
                          <div class="vip-crown-badge-glow" style="width: 42px; height: 42px;">
                            <i class="bi bi-star-fill text-warning fs-5"></i>
                          </div>
                          <div>
                            <h2 class="h4 fw-bold mb-0" style="color: #92400e;">Featured VIP Masterclasses</h2>
                            <p class="mb-0 text-muted small" style="font-size: 0.82rem;">
                              Top-tier programs with direct instructor guidance and verified credentials
                            </p>
                          </div>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem;">
                          <i class="bi bi-gem me-1"></i> VIP Spotlight
                        </span>
                      </div>

                      <div class="row g-4">
                        @foreach($vipCourses as $course)
                          @php
                            $today = \Carbon\Carbon::today();
                            $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date)->startOfDay() : null;
                            $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date)->startOfDay() : null;

                            $dateStatus = [
                                'type' => 'open',
                                'badge_text' => 'Open Course',
                                'countdown_text' => 'Flexible Schedule',
                                'icon' => 'bi bi-lightning-charge-fill',
                                'pulse' => false,
                            ];

                            if ($startDate && $startDate->greaterThan($today)) {
                                $daysUntilStart = (int) $today->diffInDays($startDate, false);
                                $dateStatus['type'] = 'upcoming';
                                $dateStatus['icon'] = 'bi bi-rocket-takeoff-fill';
                                $dateStatus['pulse'] = true;
                                $dateStatus['badge_text'] = $daysUntilStart === 0 ? 'Starts Today' : ($daysUntilStart === 1 ? 'Starts Tomorrow' : "Starts in {$daysUntilStart} days");
                                $dateStatus['countdown_text'] = $daysUntilStart === 0 ? 'Starts today' : ($daysUntilStart === 1 ? '1 day left until start' : "{$daysUntilStart} days left");
                            } elseif ((($startDate && $startDate->lessThanOrEqualTo($today)) || $course->started_at !== null || $course->status === 'started') && ($endDate && $endDate->greaterThanOrEqualTo($today))) {
                                $daysUntilEnd = (int) $today->diffInDays($endDate, false);
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-hourglass-split';
                                $dateStatus['pulse'] = true;
                                $dateStatus['badge_text'] = $daysUntilEnd === 0 ? 'Ends Today' : ($daysUntilEnd === 1 ? 'Ends Tomorrow' : "Ends in {$daysUntilEnd} days");
                                $dateStatus['countdown_text'] = $daysUntilEnd === 0 ? 'Ends today' : ($daysUntilEnd === 1 ? '1 day left' : "{$daysUntilEnd} days left");
                            } elseif (($endDate && $endDate->lessThan($today)) || in_array($course->status, ['archived', 'completed'])) {
                                $dateStatus['type'] = 'finished';
                                $dateStatus['icon'] = 'bi bi-check2-circle';
                                $dateStatus['badge_text'] = 'Completed';
                                $dateStatus['countdown_text'] = 'Course completed';
                                $dateStatus['pulse'] = false;
                            } elseif ($startDate && $startDate->lessThanOrEqualTo($today)) {
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-play-circle-fill';
                                $dateStatus['badge_text'] = 'In Progress';
                                $dateStatus['countdown_text'] = 'Class in progress';
                                $dateStatus['pulse'] = true;
                            }
                          @endphp
                          <div class="col-12 col-xl-6">
                            <article class="edvora-landscape-course-card is-featured-course">
                              <div class="glass-card-specular"></div>
                              <div class="featured-card-flare"></div>

                              <!-- Thumbnail Box -->
                              <div class="edvora-landscape-thumb-box">
                                <div class="edvora-landscape-status-pill status-{{ $dateStatus['type'] }}">
                                  @if($dateStatus['pulse'])
                                    <span class="status-live-dot"></span>
                                  @endif
                                  <i class="{{ $dateStatus['icon'] }}"></i>
                                  <span>{{ $dateStatus['badge_text'] }}</span>
                                </div>

                                <div class="edvora-landscape-vip-badge">
                                  <i class="bi bi-star-fill"></i> VIP
                                </div>

                                <a href="{{ route('courses.detail', $course->slug) }}" class="edvora-thumb-link" title="{{ $course->title }}">
                                  @php
                                    $cThumbUrl = $course->thumbnail ? (Str::startsWith($course->thumbnail, ['http://', 'https://']) ? $course->thumbnail : asset('storage/' . $course->thumbnail)) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop';
                                  @endphp
                                  <div class="edvora-thumb-ambient" style="background-image: url('{{ $cThumbUrl }}');"></div>
                                  <img src="{{ $cThumbUrl }}"
                                       alt="{{ $course->title }}"
                                       class="edvora-card-img"
                                       loading="lazy"
                                       onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop';">
                                </a>
                              </div>

                              <!-- Content Body -->
                              <div class="edvora-landscape-body">
                                <div class="edvora-landscape-meta-top">
                                  <span class="edvora-landscape-cat-badge">
                                    <i class="bi bi-folder2-open"></i>
                                    {{ $course->category ? $course->category->name : 'Technology' }}
                                  </span>

                                  @if($course->level)
                                    <span class="edvora-landscape-level-badge">
                                      <i class="bi bi-bar-chart-fill me-1"></i>
                                      {{ ucfirst($course->level) }}
                                    </span>
                                  @endif
                                </div>

                                <h3 class="edvora-landscape-title">
                                  <a href="{{ route('courses.detail', $course->slug) }}" title="{{ $course->title }}">
                                    {{ $course->title }}
                                  </a>
                                </h3>

                                <div class="edvora-landscape-teacher">
                                  <i class="bi bi-person-circle"></i>
                                  <span>{{ $course->teacher->name ?? 'Edvora Instructor' }}</span>
                                  @if($course->teacher && $course->teacher->is_verified)
                                    <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.75rem;" title="Verified Instructor"></i>
                                  @endif
                                </div>

                                <div class="vip-perks-strip">
                                  <i class="bi bi-patch-check-fill"></i>
                                  <span>VIP Access &bull; 1-on-1 Mentorship &bull; Certificate</span>
                                </div>

                                <p class="edvora-landscape-excerpt">
                                  {{ Str::limit(strip_tags($course->description ?? 'Gain practical skills and hands-on experience in this comprehensive program.'), 95) }}
                                </p>

                                <div class="edvora-landscape-schedule schedule-{{ $dateStatus['type'] }}">
                                  <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $dateStatus['icon'] }}"></i>
                                    <span class="fw-bold">{{ $dateStatus['countdown_text'] }}</span>
                                  </div>
                                  <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    @if($course->start_date && $course->end_date)
                                      {{ $course->start_date->format('M d') }} - {{ $course->end_date->format('M d, Y') }}
                                    @elseif($course->start_date)
                                      Starts {{ $course->start_date->format('M d, Y') }}
                                    @elseif($course->end_date)
                                      Ends {{ $course->end_date->format('M d, Y') }}
                                    @else
                                      Flexible Schedule
                                    @endif
                                  </div>
                                </div>

                                <div class="edvora-landscape-footer">
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
                                    <i class="bi bi-star-fill me-1"></i>
                                    <span>Explore VIP</span>
                                  </a>
                                </div>
                              </div>
                            </article>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  @endif

                  <!-- CATEGORY GROUPED SECTIONS (Side-by-Side Landscape Grid) -->
                  @foreach($groupedCourses as $categoryName => $categoryCourses)
                    <div class="mb-5 category-group-section">
                      <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3 border-opacity-10 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                          <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3" style="width: 42px; height: 42px;">
                            <i class="bi bi-folder2-open fs-5"></i>
                          </div>
                          <div>
                            <h2 class="h4 fw-bold mb-0 text-dark">{{ $categoryName }}</h2>
                            <p class="mb-0 text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">
                              {{ $categoryCourses->count() }} {{ Str::plural('Course', $categoryCourses->count()) }} Available
                            </p>
                          </div>
                        </div>
                      </div>

                      <div class="row g-4">
                        @foreach($categoryCourses->sortByDesc('is_featured') as $course)
                          @php
                            $today = \Carbon\Carbon::today();
                            $startDate = $course->start_date ? \Carbon\Carbon::parse($course->start_date)->startOfDay() : null;
                            $endDate = $course->end_date ? \Carbon\Carbon::parse($course->end_date)->startOfDay() : null;

                            $dateStatus = [
                                'type' => 'open',
                                'badge_text' => 'Open Course',
                                'countdown_text' => 'Flexible Schedule',
                                'icon' => 'bi bi-lightning-charge-fill',
                                'pulse' => false,
                            ];

                            if ($startDate && $startDate->greaterThan($today)) {
                                $daysUntilStart = (int) $today->diffInDays($startDate, false);
                                $dateStatus['type'] = 'upcoming';
                                $dateStatus['icon'] = 'bi bi-rocket-takeoff-fill';
                                $dateStatus['pulse'] = true;
                                $dateStatus['badge_text'] = $daysUntilStart === 0 ? 'Starts Today' : ($daysUntilStart === 1 ? 'Starts Tomorrow' : "Starts in {$daysUntilStart} days");
                                $dateStatus['countdown_text'] = $daysUntilStart === 0 ? 'Starts today' : ($daysUntilStart === 1 ? '1 day left until start' : "{$daysUntilStart} days left");
                            } elseif ((($startDate && $startDate->lessThanOrEqualTo($today)) || $course->started_at !== null || $course->status === 'started') && ($endDate && $endDate->greaterThanOrEqualTo($today))) {
                                $daysUntilEnd = (int) $today->diffInDays($endDate, false);
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-hourglass-split';
                                $dateStatus['pulse'] = true;
                                $dateStatus['badge_text'] = $daysUntilEnd === 0 ? 'Ends Today' : ($daysUntilEnd === 1 ? 'Ends Tomorrow' : "Ends in {$daysUntilEnd} days");
                                $dateStatus['countdown_text'] = $daysUntilEnd === 0 ? 'Ends today' : ($daysUntilEnd === 1 ? '1 day left' : "{$daysUntilEnd} days left");
                            } elseif (($endDate && $endDate->lessThan($today)) || in_array($course->status, ['archived', 'completed'])) {
                                $dateStatus['type'] = 'finished';
                                $dateStatus['icon'] = 'bi bi-check2-circle';
                                $dateStatus['badge_text'] = 'Completed';
                                $dateStatus['countdown_text'] = 'Course completed';
                                $dateStatus['pulse'] = false;
                            } elseif ($startDate && $startDate->lessThanOrEqualTo($today)) {
                                $dateStatus['type'] = 'ongoing';
                                $dateStatus['icon'] = 'bi bi-play-circle-fill';
                                $dateStatus['badge_text'] = 'In Progress';
                                $dateStatus['countdown_text'] = 'Class in progress';
                                $dateStatus['pulse'] = true;
                            }
                          @endphp
                          <div class="col-12 col-xl-6">
                            <article class="edvora-landscape-course-card {{ $course->is_featured ? 'is-featured-course' : '' }}">
                              <div class="glass-card-specular"></div>

                              @if($course->is_featured)
                                <div class="featured-card-flare"></div>
                              @endif

                              <!-- Thumbnail Box -->
                              <div class="edvora-landscape-thumb-box">
                                <div class="edvora-landscape-status-pill status-{{ $dateStatus['type'] }}">
                                  @if($dateStatus['pulse'])
                                    <span class="status-live-dot"></span>
                                  @endif
                                  <i class="{{ $dateStatus['icon'] }}"></i>
                                  <span>{{ $dateStatus['badge_text'] }}</span>
                                </div>

                                @if($course->is_featured)
                                  <div class="edvora-landscape-vip-badge">
                                    <i class="bi bi-star-fill"></i> VIP
                                  </div>
                                @endif

                                <a href="{{ route('courses.detail', $course->slug) }}" class="edvora-thumb-link" title="{{ $course->title }}">
                                  @php
                                    $cThumbUrl2 = $course->thumbnail ? (Str::startsWith($course->thumbnail, ['http://', 'https://']) ? $course->thumbnail : asset('storage/' . $course->thumbnail)) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop';
                                  @endphp
                                  <div class="edvora-thumb-ambient" style="background-image: url('{{ $cThumbUrl2 }}');"></div>
                                  <img src="{{ $cThumbUrl2 }}"
                                       alt="{{ $course->title }}"
                                       class="edvora-card-img"
                                       loading="lazy"
                                       onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=380&fit=crop';">
                                </a>
                              </div>

                              <!-- Content Body -->
                              <div class="edvora-landscape-body">
                                <div class="edvora-landscape-meta-top">
                                  <span class="edvora-landscape-cat-badge">
                                    <i class="bi bi-folder2-open"></i>
                                    {{ $course->category ? $course->category->name : 'Technology' }}
                                  </span>

                                  @if($course->level)
                                    <span class="edvora-landscape-level-badge">
                                      <i class="bi bi-bar-chart-fill me-1"></i>
                                      {{ ucfirst($course->level) }}
                                    </span>
                                  @endif
                                </div>

                                <h3 class="edvora-landscape-title">
                                  <a href="{{ route('courses.detail', $course->slug) }}" title="{{ $course->title }}">
                                    {{ $course->title }}
                                  </a>
                                </h3>

                                <div class="edvora-landscape-teacher">
                                  <i class="bi bi-person-circle"></i>
                                  <span>{{ $course->teacher->name ?? 'Edvora Instructor' }}</span>
                                  @if($course->teacher && $course->teacher->is_verified)
                                    <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.75rem;" title="Verified Instructor"></i>
                                  @endif
                                </div>

                                @if($course->is_featured)
                                  <div class="vip-perks-strip">
                                    <i class="bi bi-patch-check-fill"></i>
                                    <span>VIP Access &bull; Dedicated Mentorship &bull; Certificate</span>
                                  </div>
                                @endif

                                <p class="edvora-landscape-excerpt">
                                  {{ Str::limit(strip_tags($course->description ?? 'Gain practical skills and hands-on experience in this comprehensive program.'), 95) }}
                                </p>

                                <div class="edvora-landscape-schedule schedule-{{ $dateStatus['type'] }}">
                                  <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $dateStatus['icon'] }}"></i>
                                    <span class="fw-bold">{{ $dateStatus['countdown_text'] }}</span>
                                  </div>
                                  <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    @if($course->start_date && $course->end_date)
                                      {{ $course->start_date->format('M d') }} - {{ $course->end_date->format('M d, Y') }}
                                    @elseif($course->start_date)
                                      Starts {{ $course->start_date->format('M d, Y') }}
                                    @elseif($course->end_date)
                                      Ends {{ $course->end_date->format('M d, Y') }}
                                    @else
                                      Flexible Schedule
                                    @endif
                                  </div>
                                </div>

                                <div class="edvora-landscape-footer">
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
                                    @if($course->is_featured)
                                      <i class="bi bi-star-fill me-1"></i>
                                      <span>Explore VIP</span>
                                    @else
                                      <span>Explore Course</span>
                                      <i class="bi bi-arrow-right"></i>
                                    @endif
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
