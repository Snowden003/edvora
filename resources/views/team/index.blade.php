@extends('layouts.app')

@section('title', 'Our Team - ' . ($siteSettings->get('company_name', 'Edvora Tech')))
@section('meta_description', 'Meet the passionate educators, software engineers, and visionaries behind Edvora Tech.')

@push('styles')
<link href="{{ asset('assets/css/company-pages.css') }}?v={{ file_exists(public_path('assets/css/company-pages.css')) ? filemtime(public_path('assets/css/company-pages.css')) : time() }}" rel="stylesheet" />
<style>
    .team-card {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1.75rem;
        transition: all 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .team-card:hover {
        transform: translateY(-6px);
        border-color: rgba(20, 184, 166, 0.4);
        box-shadow: 0 20px 40px -15px rgba(20, 184, 166, 0.2), 0 0 0 1px rgba(20, 184, 166, 0.2);
    }
    .team-card__top-banner {
        height: 80px;
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.4), rgba(14, 165, 233, 0.3), rgba(168, 85, 247, 0.3));
        position: relative;
    }
    .team-avatar-wrapper {
        position: relative;
        margin-top: -45px;
        display: inline-block;
    }
    .team-avatar-img {
        width: 90px;
        height: 90px;
        border-radius: 1.25rem;
        object-fit: cover;
        border: 3px solid #0f172a;
        background-color: #1e293b;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.6);
        transition: transform 0.3s ease;
    }
    .team-card:hover .team-avatar-img {
        transform: scale(1.05);
    }
    .team-social-btn {
        width: 34px;
        height: 34px;
        border-radius: 0.65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(30, 41, 59, 0.7);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .team-social-btn:hover {
        color: #ffffff;
        background: var(--cp-primary, #0d9488);
        border-color: transparent;
        transform: translateY(-2px);
    }
    .qr-badge-btn {
        padding: 0.4rem 0.85rem;
        border-radius: 0.75rem;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(20, 184, 166, 0.12);
        color: #2dd4bf;
        border: 1px solid rgba(20, 184, 166, 0.25);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .qr-badge-btn:hover {
        background: #0d9488;
        color: #ffffff;
        border-color: #0d9488;
        box-shadow: 0 0 15px rgba(13, 148, 136, 0.4);
    }
    .filter-btn-pill {
        padding: 0.45rem 1.15rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
        background: rgba(15, 23, 42, 0.6);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .filter-btn-pill:hover,
    .filter-btn-pill.active {
        background: linear-gradient(135deg, #0d9488, #0284c7);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.35);
    }
</style>
@endpush

@section('content')
<div class="cp-page-body">
    <!-- 3D Background Canvas -->
    <canvas id="cp3dCanvas" class="cp-3d-canvas"></canvas>

    <!-- Hero Header -->
    <x-page-hero
        layout="centered"
        badgeIcon="bi bi-people-fill"
        badgeText="Leadership & Talent"
        titlePrefix="Meet the Innovators Behind"
        highlight="{{ $siteSettings->get('company_name', 'Edvora Tech') }}"
        subtitle="A multidisciplinary collective of educators, software architects, and creators dedicated to transforming tech education."
        :floatingIcons="['bi bi-laptop', 'bi bi-code-slash', 'bi bi-award', 'bi bi-lightning-charge', 'bi bi-shield-check']"
    />

    <!-- Main Content Container -->
    <section class="cp-section pt-0">
        <div class="container position-relative" style="z-index: 2;">

            <!-- Quick Stats & Filter Controls Bar -->
            <div class="row align-items-center justify-content-between mb-5 g-3">
                <div class="col-lg-6">
                    <!-- Dynamic Filter Pills -->
                    <div class="d-flex flex-wrap gap-2" id="departmentFilters">
                        <button class="filter-btn-pill active" data-filter="all">
                            All Members <span class="badge bg-dark ms-1 text-light rounded-pill">{{ $members->count() }}</span>
                        </button>
                        @foreach($departments as $dept)
                            <button class="filter-btn-pill" data-filter="{{ Str::slug($dept) }}">
                                {{ $dept }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <!-- Realtime Search Box -->
                    <div class="input-group" style="border-radius: 9999px; overflow: hidden; background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input 
                            type="text" 
                            id="memberSearchInput" 
                            class="form-control bg-transparent border-0 text-light py-2" 
                            placeholder="Search by name, role or skill..." 
                            style="outline: none; box-shadow: none;"
                        />
                    </div>
                </div>
            </div>

            <!-- Team Members Grid -->
            @if($members->isEmpty())
                <div class="text-center py-5">
                    <div class="cp-icon-box cp-icon-box-blue mx-auto mb-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-2">Our Team is Growing</h3>
                    <p class="text-muted">No team members are published at the moment. Please check back soon!</p>
                </div>
            @else
                <div class="row g-4 justify-content-start" id="teamMembersGrid">
                    @foreach($members as $member)
                        @php
                            $deptSlug = Str::slug($member->department ?: 'general');
                            $searchKeywords = strtolower($member->name . ' ' . $member->role_title . ' ' . $member->department . ' ' . $member->bio);
                        @endphp
                        <div 
                            class="col-xl-4 col-md-6 team-member-col" 
                            data-department="{{ $deptSlug }}"
                            data-keywords="{{ $searchKeywords }}"
                        >
                            <div class="team-card h-100 d-flex flex-column">
                                <!-- Top decorative banner -->
                                <div class="team-card__top-banner px-4 pt-3 d-flex justify-content-between align-items-start">
                                    @if($member->employee_id)
                                        <span class="badge bg-dark bg-opacity-75 text-light font-monospace border border-secondary border-opacity-25 px-2 py-1" style="font-size: 0.7rem;">
                                            {{ $member->employee_id }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif

                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1 d-flex align-items-center gap-1" style="font-size: 0.7rem;">
                                        <span class="p-1 rounded-circle bg-success"></span> Verified
                                    </span>
                                </div>

                                <!-- Body -->
                                <div class="p-4 pt-0 d-flex flex-column flex-grow-1">
                                    <!-- Avatar & Actions -->
                                    <div class="d-flex justify-content-between align-items-end mb-3">
                                        <div class="team-avatar-wrapper">
                                            <img 
                                                src="{{ $member->avatar_url }}" 
                                                alt="{{ $member->name }}" 
                                                class="team-avatar-img"
                                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0d9488&color=ffffff&size=256&bold=true'"
                                            />
                                        </div>

                                        <!-- QR Code Link -->
                                        <a 
                                            href="{{ $member->public_url }}" 
                                            class="qr-badge-btn"
                                            title="View Smart Digital ID & QR Code"
                                        >
                                            <i class="bi bi-qr-code"></i>
                                            <span>Scan ID</span>
                                        </a>
                                    </div>

                                    <!-- Member Info -->
                                    <div class="mb-3">
                                        <h3 class="fw-bold mb-1 text-white" style="font-size: 1.25rem;">
                                            <a href="{{ $member->public_url }}" class="text-white text-decoration-none hover-teal">
                                                {{ $member->name }}
                                            </a>
                                        </h3>
                                        <p class="mb-1 fw-semibold text-teal" style="font-size: 0.92rem; color: #2dd4bf;">
                                            {{ $member->role_title }}
                                        </p>
                                        @if($member->department)
                                            <span class="badge bg-dark text-secondary border border-secondary border-opacity-25 px-2 py-1 mt-1" style="font-size: 0.75rem;">
                                                <i class="bi bi-buildings me-1"></i>{{ $member->department }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Bio / Description -->
                                    @if($member->bio)
                                        <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                                            {{ Str::limit($member->bio, 130) }}
                                        </p>
                                    @else
                                        <div class="flex-grow-1"></div>
                                    @endif

                                    <!-- Bottom Action & Socials -->
                                    <div class="pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                        <!-- Social icons -->
                                        <div class="d-flex gap-1.5">
                                            @if($member->linkedin)
                                                <a href="{{ str_starts_with($member->linkedin, 'http') ? $member->linkedin : 'https://linkedin.com/in/' . $member->linkedin }}" target="_blank" rel="noopener" class="team-social-btn" title="LinkedIn">
                                                    <i class="bi bi-linkedin"></i>
                                                </a>
                                            @endif
                                            @if($member->github)
                                                <a href="{{ str_starts_with($member->github, 'http') ? $member->github : 'https://github.com/' . $member->github }}" target="_blank" rel="noopener" class="team-social-btn" title="GitHub">
                                                    <i class="bi bi-github"></i>
                                                </a>
                                            @endif
                                            @if($member->telegram)
                                                <a href="https://t.me/{{ ltrim($member->telegram, '@') }}" target="_blank" rel="noopener" class="team-social-btn" title="Telegram">
                                                    <i class="bi bi-telegram"></i>
                                                </a>
                                            @endif
                                            @if($member->email)
                                                <a href="mailto:{{ $member->email }}" class="team-social-btn" title="Email">
                                                    <i class="bi bi-envelope"></i>
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Direct Profile Button -->
                                        <a href="{{ $member->public_url }}" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                            <span>Profile</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

    <!-- Join The Mission CTA -->
    <section class="cp-section-sm pb-5">
        <div class="container position-relative" style="z-index: 2;">
            <div class="cp-glass-card cp-glass-card-glow-blue p-4 p-lg-5 text-center">
                <span class="cp-badge cp-badge-cyan mb-2">Want to collaborate?</span>
                <h2 class="fw-bold mb-3 text-white">Join Our Growing Movement</h2>
                <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
                    We are always seeking passionate instructors, contributors, and technology mentors to empower students worldwide.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold">
                        <i class="bi bi-send me-1.5"></i> Get in Touch
                    </a>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-light rounded-pill px-4 py-2.5 fw-bold">
                        Explore Courses
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn-pill');
        const searchInput = document.getElementById('memberSearchInput');
        const memberCols = document.querySelectorAll('.team-member-col');

        let activeFilter = 'all';
        let searchQuery = '';

        function applyFilters() {
            memberCols.forEach(col => {
                const dept = col.getAttribute('data-department');
                const keywords = col.getAttribute('data-keywords') || '';

                const matchesDept = (activeFilter === 'all' || dept === activeFilter);
                const matchesSearch = (searchQuery === '' || keywords.includes(searchQuery));

                if (matchesDept && matchesSearch) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeFilter = this.getAttribute('data-filter');
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                searchQuery = e.target.value.toLowerCase().trim();
                applyFilters();
            });
        }
    });
</script>
@endpush
