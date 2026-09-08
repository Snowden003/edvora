@extends('layouts.app')

@section('title', 'Our Teachers - Edvora Tech')
@section('body-class', 'page-teachers-index')

@push('styles')
<link href="{{ asset('assets/css/teachers.css') }}" rel="stylesheet" />
@endpush

@section('content')
@php
    $totalTeachers = $teachers->count();
    $avgRating = $totalTeachers > 0 ? $teachers->avg('display_rating') : 0;
    $totalStudents = $teachers->sum(fn ($teacher) => (int) ($teacher->total_students_count ?? 0));
    $totalActiveCourses = $teachers->sum('active_courses_count');
@endphp

<x-page-hero
    layout="split"
    badgeIcon="bi bi-person-workspace"
    badgeText="Meet Our Teachers"
    titlePrefix="Qualified Mentors &"
    highlight="Instructors"
    subtitle="Explore instructors who actively teach on Edvora. Every profile includes verified teaching credentials, active course history, and student ratings."
    :stats="[
        ['icon' => 'bi bi-person-check-fill', 'value' => (string) $totalTeachers, 'label' => 'Instructors'],
        ['icon' => 'bi bi-people-fill', 'value' => number_format($totalStudents), 'label' => 'Students Reached'],
        ['icon' => 'bi bi-book-half', 'value' => (string) $totalActiveCourses, 'label' => 'Active Classes'],
        ['icon' => 'bi bi-star-fill text-warning', 'value' => number_format($avgRating, 1), 'label' => 'Avg Rating'],
    ]"
    :floatingIcons="['bi bi-mortarboard', 'bi bi-award', 'bi bi-lightbulb', 'bi bi-people', 'bi bi-book']"
>
    <x-slot:mediaSlot>
        <div class="hero-side-card text-start">
            <h3 class="fw-bold mb-3 text-white d-flex align-items-center gap-2" style="font-size: 1.25rem;">
                <i class="bi bi-shield-check text-info"></i> Instructor Verified
            </h3>
            <p class="text-white-50 small mb-3">All teacher profiles on Edvora undergo verification for expertise and curriculum quality.</p>
            <div class="d-flex flex-column gap-2 text-white-50" style="font-size: 0.88rem;">
                <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Direct Q&A & live feedback</div>
                <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Comprehensive course curriculum</div>
                <div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i> Continuous student assessments</div>
            </div>
        </div>
    </x-slot:mediaSlot>
</x-page-hero>

<section class="teachers-directory-toolbar">
    <div class="container">
        <div class="teachers-directory-filters">
            <div class="teachers-filter-field teachers-filter-field--search">
                <i class="bi bi-search"></i>
                <input type="text" id="teacherSearch" placeholder="Search by teacher, specialization, or section">
            </div>
            <div class="teachers-filter-field">
                <select id="sectionFilter">
                    <option value="">All sections</option>
                    @foreach($teachingSections as $section)
                        <option value="{{ Str::slug($section) }}">{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            <div class="teachers-filter-field">
                <select id="experienceFilter">
                    <option value="">All experience levels</option>
                    <option value="1-3">1-3 years</option>
                    <option value="4-7">4-7 years</option>
                    <option value="8-100">8+ years</option>
                </select>
            </div>
        </div>
    </div>
</section>

<section class="teachers-directory-section">
    <div class="container">
        @if($teachers->isEmpty())
            <div class="teachers-empty-state">
                <i class="bi bi-people"></i>
                <h3>No complete teacher profiles yet</h3>
                <p>Only teachers with complete public profiles are shown here.</p>
            </div>
        @else
            <div class="row g-4" id="teachersGrid">
                @foreach($teachers as $teacher)
                    @php
                        $sections = $teacher->teaching_sections;
                        $expertiseList = $teacher->expertise_list;
                        $teacherRating = (float) $teacher->display_rating;
                        $showRating = $teacherRating > 0;
                        $experienceLabel = $teacher->experience_years ?: ($teacher->display_experience_years ? $teacher->display_experience_years . ' years' : 'Not specified');
                        $searchIndex = Str::lower(
                            collect([
                                $teacher->name,
                                $teacher->department,
                                $teacher->teacher?->specialization,
                                $teacher->teacher?->expertise,
                                $teacher->phone,
                                $sections->implode(' '),
                            ])->filter()->implode(' ')
                        );
                    @endphp
                    <div
                        class="col-xl-4 col-md-6 teacher-directory-item"
                        data-search="{{ $searchIndex }}"
                        data-sections="{{ $sections->map(fn ($section) => Str::slug($section))->implode('|') }}"
                        data-experience="{{ (int) ($teacher->display_experience_years ?? 0) }}"
                    >
                        <article class="teacher-directory-card h-100">
                            <div class="teacher-directory-card__header">
                                <img
                                    src="{{ $teacher->avatar_url }}"
                                    alt="{{ $teacher->name }}"
                                    class="teacher-directory-card__avatar"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <div class="teacher-directory-card__identity">
                                    <span class="teacher-directory-card__department">{{ $teacher->department }}</span>
                                    <h2>{{ $teacher->name }}</h2>
                                    <p>{{ $teacher->teacher?->specialization }}</p>
                                </div>
                                @if($showRating)
                                    <div class="teacher-directory-card__rating">
                                        <i class="bi bi-star-fill"></i>
                                        <span>{{ number_format($teacherRating, 1) }}</span>
                                    </div>
                                @else
                                    <div class="teacher-directory-card__verification">
                                        <i class="bi bi-patch-check-fill"></i>
                                        <span>Active teacher</span>
                                    </div>
                                @endif
                            </div>

                            <p class="teacher-directory-card__bio">
                                {{ Str::limit($teacher->bio, 145) }}
                            </p>

                            <div class="teacher-directory-card__meta">
                                <div class="teacher-directory-card__meta-item">
                                    <span class="label">Joined</span>
                                    <strong>{{ $teacher->joined_display }}</strong>
                                </div>
                                <div class="teacher-directory-card__meta-item">
                                    <span class="label">Experience</span>
                                    <strong>{{ $experienceLabel }}</strong>
                                </div>
                                <div class="teacher-directory-card__meta-item">
                                    <span class="label">Expertise</span>
                                    <strong>{{ $expertiseList->take(2)->implode(' • ') ?: 'Teaching expertise' }}</strong>
                                </div>
                            </div>

                            <div class="teacher-directory-card__stats">
                                <div class="teacher-stat-box">
                                    <span class="teacher-stat-box__icon"><i class="bi bi-people-fill"></i></span>
                                    <div>
                                        <strong>{{ number_format((int) ($teacher->total_students_count ?? 0)) }}</strong>
                                        <span>Students</span>
                                    </div>
                                </div>
                                <div class="teacher-stat-box">
                                    <span class="teacher-stat-box__icon"><i class="bi bi-play-circle-fill"></i></span>
                                    <div>
                                        <strong>{{ $teacher->active_courses_count }}</strong>
                                        <span>Active classes</span>
                                    </div>
                                </div>
                                <div class="teacher-stat-box">
                                    <span class="teacher-stat-box__icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                                    <div>
                                        <strong>{{ $teacher->total_courses_count }}</strong>
                                        <span>Total classes</span>
                                    </div>
                                </div>
                            </div>

                            @if($expertiseList->isNotEmpty())
                                <div class="teacher-directory-card__sections teacher-directory-card__expertise">
                                    <span class="teacher-directory-card__sections-title">Core expertise</span>
                                    <div class="teacher-directory-card__chips">
                                        @foreach($expertiseList->take(4) as $skill)
                                            <span class="teacher-chip teacher-chip--soft">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="teacher-directory-card__sections">
                                <span class="teacher-directory-card__sections-title">Teaching sections</span>
                                <div class="teacher-directory-card__chips">
                                    @foreach($sections as $section)
                                        <span class="teacher-chip">{{ $section }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="teacher-directory-card__footer">
                                <a href="{{ route('teachers.show', $teacher->id) }}" class="teacher-directory-card__cta">
                                    View full profile
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
            <div id="teachersNoResults" class="teachers-empty-state d-none mt-4">
                <i class="bi bi-search"></i>
                <h3>No teacher matched your filters</h3>
                <p>Try another name, section, or experience range.</p>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('teacherSearch');
    const sectionFilter = document.getElementById('sectionFilter');
    const experienceFilter = document.getElementById('experienceFilter');
    const cards = Array.from(document.querySelectorAll('.teacher-directory-item'));
    const noResults = document.getElementById('teachersNoResults');

    if (!cards.length) {
        return;
    }

    function matchExperience(value, years) {
        if (!value) {
            return true;
        }

        const [min, max] = value.split('-').map(Number);
        return years >= min && years <= max;
    }

    function applyFilters() {
        const searchValue = (searchInput?.value || '').trim().toLowerCase();
        const sectionValue = sectionFilter?.value || '';
        const experienceValue = experienceFilter?.value || '';

        let visibleCount = 0;

        cards.forEach((card) => {
            const haystack = card.dataset.search || '';
            const sections = card.dataset.sections || '';
            const years = Number(card.dataset.experience || 0);

            const matchesSearch = !searchValue || haystack.includes(searchValue);
            const matchesSection = !sectionValue || sections.split('|').includes(sectionValue);
            const matchesExperience = matchExperience(experienceValue, years);
            const visible = matchesSearch && matchesSection && matchesExperience;

            card.classList.toggle('d-none', !visible);
            if (visible) {
                visibleCount += 1;
            }
        });

        if (noResults) {
            noResults.classList.toggle('d-none', visibleCount !== 0);
        }
    }

    [searchInput, sectionFilter, experienceFilter].forEach((element) => {
        element?.addEventListener('input', applyFilters);
        element?.addEventListener('change', applyFilters);
    });
});
</script>
@endpush
