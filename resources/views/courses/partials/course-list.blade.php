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
                              $isUpcoming = ($course->start_date && $course->start_date->isFuture()) || (!$course->started_at && $course->status === 'published' && (!$course->end_date || $course->end_date->isFuture()));
                              $isFinished = ($course->end_date && $course->end_date->isPast()) || in_array($course->status, ['archived', 'completed']);
                              $isHot = $course->enrolled_count > 600;
                              $isNew = $course->created_at && $course->created_at->diffInDays() < 30;
                            @endphp
                            <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                              <article class="safi-course-card">
                                <div class="safi-thumb-wrap">
                                  <div class="safi-pulse-bg"><div class="safi-pulse-inner"></div></div>
                                  <div class="safi-grad-overlay"></div>
                                  <div class="safi-hover-glow"></div>
                                  <img src="{{ $course->thumbnail ? (Str::startsWith($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail)) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=400&fit=crop' }}"
                                       alt="{{ $course->title }}"
                                       loading="lazy">
                                </div>

                                <div class="safi-card-body">
                                  <div class="safi-meta-top">
                                    <span class="safi-category">{{ $course->category ? $course->category->name : 'General' }}</span>
                                    <span class="safi-price">Free</span>
                                  </div>

                                  <h2 class="safi-title" title="{{ $course->title }}">
                                      {{ $course->title }}
                                  </h2>

                                  <p class="safi-description">
                                      {{ Str::limit(strip_tags($course->description ?? 'Learn the essential skills for success in this comprehensive course.'), 120) }}
                                  </p>

                                  <div class="safi-footer">
                                    <span class="safi-lang">en</span>
                                    <a href="{{ route('courses.detail', $course->slug) }}" class="safi-explore">
                                      Explore
                                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
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
