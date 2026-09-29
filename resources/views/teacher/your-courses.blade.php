@extends('layouts.app')

@section('title', 'دوره‌های من - پنل اساتید ادورا تک')

@push('styles')
<link href="{{ asset('assets/css/teacher-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/your-courses.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
@endpush

@section('content')

    {{-- 3D Animated Background --}}
    <div class="yc-bg-canvas">
        <div class="yc-grid-bg"></div>
        <div class="yc-orb yc-orb-1"></div>
        <div class="yc-orb yc-orb-2"></div>
        <div class="yc-orb yc-orb-3"></div>
        <div class="yc-orb yc-orb-4"></div>
        <div style="position:absolute;inset:0;">
            <div class="yc-particle"></div>
            <div class="yc-particle"></div>
            <div class="yc-particle"></div>
            <div class="yc-particle"></div>
            <div class="yc-particle"></div>
            <div class="yc-particle"></div>
            <div class="yc-particle"></div>
            <div class="yc-particle"></div>
        </div>
    </div>

    <div class="dashboard-wrapper yc-wrapper">
        {{-- Sidebar --}}
        <x-teacher-sidebar />

        {{-- Main Content --}}
        <main class="main-content">
            <div class="yc-content container-fluid">

                {{-- ── HERO BANNER ──────────────────────────────────────── --}}
                <div class="yc-hero yc-reveal" dir="rtl">
                    {{-- Floating Background Icons --}}
                    <div class="yc-hero-float yc-hero-float-1"><i class="bi bi-mortarboard"></i></div>
                    <div class="yc-hero-float yc-hero-float-2"><i class="bi bi-book-half"></i></div>
                    <div class="yc-hero-float yc-hero-float-3"><i class="bi bi-lightbulb"></i></div>

                    <div class="yc-hero-content">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <div class="yc-hero-badge">
                                    <i class="bi bi-shield-check"></i>
                                    <span>پنل اساتید و مدرسین · ادورا تک</span>
                                </div>
                                <h1>
                                    مرکز <span class="highlight">دوره‌های</span> من
                                </h1>
                                <p>
                                    مدیریت، سازماندهی و توسعه تأثیر آموزشی شما. در حال حاضر شما
                                    <strong>{{ $totalStudents }} دانشجو</strong>
                                    در تمامی دوره‌هایتان دارید.
                                </p>
                                <div class="yc-hero-stats">
                                    <div class="yc-hero-stat-pill">
                                        <i class="bi bi-journal-text" style="color:#7de8ff;"></i>
                                        <span>{{ $totalCourses }} دوره فعال</span>
                                    </div>
                                    <div class="yc-hero-stat-pill">
                                        <i class="bi bi-people-fill" style="color:#a5f3a0;"></i>
                                        <span>{{ $totalStudents }} دانشجو</span>
                                    </div>
                                    <div class="yc-hero-stat-pill">
                                        <i class="bi bi-star-fill" style="color:#fde68a;"></i>
                                        <span>{{ number_format($avgRating, 1) }} امتیاز میانگین</span>
                                    </div>
                                    <div class="yc-hero-stat-pill">
                                        <i class="bi bi-graph-up" style="color:#c4b5fd;"></i>
                                        <span>{{ $activeCourses }} دوره جاری</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── STAT CARDS ────────────────────────────────────────── --}}
                <div class="yc-stats-row yc-reveal" dir="rtl" style="--reveal-delay:0.1s">
                    <div class="yc-stat-card" style="--stat-color:#1F8FFF;">
                        <div class="yc-stat-icon"><i class="bi bi-journal-text"></i></div>
                        <div class="yc-stat-num" data-count="{{ $totalCourses }}">0</div>
                        <div class="yc-stat-label">کل دوره‌ها</div>
                    </div>
                    <div class="yc-stat-card" style="--stat-color:#10b981;">
                        <div class="yc-stat-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="yc-stat-num" data-count="{{ $totalStudents }}">0</div>
                        <div class="yc-stat-label">دانشجویان فعال</div>
                    </div>
                    <div class="yc-stat-card" style="--stat-color:#f59e0b;">
                        <div class="yc-stat-icon"><i class="bi bi-star-fill"></i></div>
                        <div class="yc-stat-num" data-count="{{ number_format($avgRating, 1) }}" data-decimal="1">0</div>
                        <div class="yc-stat-label">میانگین امتیاز</div>
                    </div>
                    <div class="yc-stat-card" style="--stat-color:#8b5cf6;">
                        <div class="yc-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <div class="yc-stat-num" data-count="{{ $activeCourses }}">0</div>
                        <div class="yc-stat-label">دوره‌های جاری</div>
                    </div>
                </div>

                {{-- ── FILTER BAR ────────────────────────────────────────── --}}
                <form method="GET" action="{{ route('teacher.your-courses') }}" id="filterForm">
                    <div class="yc-filter-bar yc-reveal" dir="rtl">
                        <div class="yc-search-wrap">
                            <i class="bi bi-search"></i>
                            <input
                                type="text"
                                id="searchCourses"
                                name="search"
                                class="yc-search-input"
                                placeholder="جستجو در دوره‌ها..."
                                value="{{ request('search') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="yc-divider-v"></div>
                        <select id="categoryFilter" name="category" class="yc-select" onchange="this.form.submit()">
                            <option value="">همه دسته‌بندی‌ها</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="yc-divider-v d-none d-md-block"></div>
                        <div class="yc-filter-count d-none d-md-flex align-items-center gap-2">
                            <i class="bi bi-collection-fill" style="color:var(--yc-primary);"></i>
                            {{ $courses->total() ?? $courses->count() }} دوره
                        </div>
                    </div>
                </form>

                {{-- ── SECTION HEADING ─────────────────────────────────── --}}
                <div class="yc-section-title yc-reveal" dir="rtl">
                    <div class="yc-section-title-line"></div>
                    <h2><i class="bi bi-collection me-2" style="color:var(--yc-primary);"></i>دوره‌های فعال</h2>
                    <span class="badge-count">{{ $courses->total() ?? $courses->count() }}</span>
                </div>

                {{-- ── COURSES GRID ─────────────────────────────────────── --}}
                @if($courses->isEmpty())
                <div class="yc-courses-grid">
                    <div class="yc-empty-state">
                        <div class="yc-empty-icon">
                            <i class="bi bi-journal-x"></i>
                        </div>
                        <h4>هنوز دوره‌ای ندارید</h4>
                        <p>در حال حاضر هیچ دوره‌ای به شما اختصاص داده نشده است.</p>
                    </div>
                </div>
                @else
                <div class="yc-courses-grid" id="coursesGrid" dir="rtl">
                    @foreach($courses as $course)ص
                    @php
                        $statusClass = match($course->status) {
                            'published' => 'published',
                            'active' => 'active',
                            'draft' => 'draft',
                            'archived' => 'archived',
                            default => 'active'
                        };
                        $statusLabel = match($course->status) {
                            'published' => 'منتشر شده',
                            'active' => 'فعال',
                            'draft' => 'پیش‌نویس',
                            'archived' => 'بایگانی',
                            default => 'فعال'
                        };
                    @endphp
                    <div class="yc-course-card">

                        {{-- Card Image --}}
                        <div class="yc-card-img">
                            <img
                                src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=600&h=300&fit=crop' }}"
                                alt="{{ $course->title }}"
                                loading="lazy"
                            >
                            <div class="yc-card-img-overlay"></div>

                            {{-- Category badge --}}
                            <div class="yc-card-category">
                                {{ $course->category->name ?? 'عمومی' }}
                            </div>

                            {{-- Status badge --}}
                            <div class="yc-card-status {{ $statusClass }}">
                                {{ $statusLabel }}
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="yc-card-body">
                            <h5 class="yc-card-title">{{ $course->title }}</h5>

                            {{-- Rating --}}
                            <div class="yc-card-rating">
                                <div class="yc-rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= round($course->rating ?? 0) ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                <span class="yc-rating-num">{{ number_format($course->rating ?? 0, 1) }}</span>
                            </div>

                            {{-- Meta chips --}}
                            <div class="yc-card-meta">
                                @if($course->duration_hours)
                                <span class="yc-meta-chip">
                                    <i class="bi bi-clock"></i> {{ $course->duration_hours }} ساعت
                                </span>
                                @endif
                                @if($course->level)
                                <span class="yc-meta-chip">
                                    <i class="bi bi-bar-chart-steps"></i>
                                    {{ match($course->level) { 'beginner' => 'مقدماتی', 'intermediate' => 'متوسط', 'advanced' => 'پیشرفته', default => ucfirst($course->level) } }}
                                </span>
                                @endif
                            </div>

                            {{-- Students --}}
                            <div class="yc-card-students">
                                <div class="yc-avatars-mini">
                                    @for($a = 0; $a < min(3, $course->enrolled_count ?? 0); $a++)
                                    <div class="av" style="background:linear-gradient(135deg,hsl({{ 200 + $a*40 }},80%,50%),hsl({{ 240 + $a*40 }},70%,45%));"></div>
                                    @endfor
                                </div>
                                <i class="bi bi-people-fill ms-1"></i>
                                <span>{{ $course->enrolled_count ?? 0 }} دانشجو ثبت‌نام شده</span>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="yc-card-footer">
                            <a href="{{ route('teacher.courses.detail', $course->id) }}" class="yc-btn-view">
                                <i class="bi bi-eye-fill"></i> مشاهده جزئیات
                            </a>
                            <div class="yc-card-footer-meta">
                                <span class="yc-footer-meta-val">{{ $course->created_at->format('Y/m/d') }}</span>
                                <span class="yc-footer-meta-lbl">تاریخ ایجاد</span>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="yc-pagination-wrap">
                    {{ $courses->links() }}
                </div>
                @endif

            </div>
        </main>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/teacher-dashboard.js') }}"></script>
<script>
(function () {
    'use strict';

    /* ── Animated Counter ── */
    function animateCounter(el) {
        const target = parseFloat(el.dataset.count || 0);
        const isDecimal = el.dataset.decimal;
        const duration = 1400;
        const start = performance.now();
        const easeOut = t => 1 - Math.pow(1 - t, 3);

        function tick(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const value = target * easeOut(progress);
            el.textContent = isDecimal ? value.toFixed(1) : Math.floor(value).toLocaleString('fa-IR');
            if (progress < 1) requestAnimationFrame(tick);
            else el.textContent = isDecimal ? target.toFixed(1) : Math.floor(target).toLocaleString('fa-IR');
        }
        requestAnimationFrame(tick);
    }

    /* ── Scroll Reveal ── */
    const revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);

                    /* trigger counters if this is the stats row */
                    entry.target.querySelectorAll('.yc-stat-num[data-count]').forEach(animateCounter);
                }
            });
        },
        { threshold: 0.15 }
    );

    document.querySelectorAll('.yc-reveal').forEach(el => revealObserver.observe(el));

    /* ── Card 3D Tilt ── */
    document.querySelectorAll('.yc-course-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const cx = rect.left + rect.width / 2;
            const cy = rect.top + rect.height / 2;
            const dx = (e.clientX - cx) / (rect.width / 2);
            const dy = (e.clientY - cy) / (rect.height / 2);
            card.style.transform = `translateY(-10px) rotateX(${-dy * 5}deg) rotateY(${dx * 5}deg) scale(1.01)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

    /* ── Live Search Filter ── */
    const searchInput = document.getElementById('searchCourses');
    const coursesGrid = document.getElementById('coursesGrid');
    if (searchInput && coursesGrid) {
        let searchTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                const q = searchInput.value.trim().toLowerCase();
                coursesGrid.querySelectorAll('.yc-course-card').forEach(card => {
                    const title = card.querySelector('.yc-card-title')?.textContent?.toLowerCase() || '';
                    const cat = card.querySelector('.yc-card-category')?.textContent?.toLowerCase() || '';
                    const match = !q || title.includes(q) || cat.includes(q);
                    card.style.opacity = match ? '' : '0.3';
                    card.style.pointerEvents = match ? '' : 'none';
                    card.style.transform = match ? '' : 'scale(0.97)';
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                });
            }, 200);
        });
    }

})();
</script>
@endpush
