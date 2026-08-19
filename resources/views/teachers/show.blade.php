@extends('layouts.app')

@section('title', $teacher->name . ' - Teacher Profile - Edvora Tech')
@section('body-class', 'page-teacher-profile')

@push('styles')
<link href="{{ asset('assets/css/teachers.css') }}" rel="stylesheet" />
@endpush

@section('content')
@php
  $teacherRating = (float) ($teacher->display_rating ?? 0);
  $showRating = $teacherRating > 0;
  $experienceLabel = $teacher->experience_years ?: ($teacher->display_experience_years ? $teacher->display_experience_years . ' years' : 'Not specified');
  $expertiseList = $teacher->expertise_list ?? collect();
  $contactLinks = collect([
      [
          'label' => 'LinkedIn',
          'url' => $teacher->teacher?->linkedin,
          'icon' => 'bi-linkedin',
      ],
      [
          'label' => 'GitHub',
          'url' => $teacher->teacher?->github,
          'icon' => 'bi-github',
      ],
      [
          'label' => 'Website',
          'url' => $teacher->teacher?->website,
          'icon' => 'bi-globe2',
      ],
  ])->filter(fn ($item) => filled($item['url']))->values();
@endphp
<section class="teachers-hero-section py-5">
  <div class="container">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $teacher->name }}</li>
      </ol>
    </nav>

    {{-- Teacher Profile Card --}}
    <div class="teacher-profile-card">
      <div class="row g-4 align-items-center">
        <div class="col-lg-3 text-center">
          <div class="teacher-avatar-large">
            <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}" class="rounded-circle" width="150" height="150">
          </div>
        </div>
        <div class="col-lg-9">
          <div class="teacher-profile-heading">
            <div>
              <span class="teacher-directory-card__department">{{ $teacher->department }}</span>
              <h1 class="teacher-name">{{ $teacher->name }}</h1>
              <p class="teacher-title text-muted">{{ $teacher->teacher?->specialization }}</p>
            </div>
            @if($showRating)
              <div class="teacher-directory-card__rating teacher-directory-card__rating--profile">
                <i class="bi bi-star-fill"></i>
                <span>{{ number_format($teacherRating, 1) }}</span>
              </div>
            @endif
          </div>

          <div class="teacher-stats d-flex gap-3 mt-4">
            <div class="stat-item">
              <i class="bi bi-book"></i>
              <span>{{ $teacher->active_courses_count }} Active Classes</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-people"></i>
              <span>{{ number_format((int) ($teacher->total_students_count ?? 0)) }} Students</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-briefcase"></i>
              <span>{{ $teacher->display_experience_years }} Years Experience</span>
            </div>
            <div class="stat-item">
              <i class="bi bi-calendar-event"></i>
              <span>Joined {{ $teacher->joined_display }}</span>
            </div>
          </div>

          <div class="teacher-bio mt-3">
            <p>{{ $teacher->bio }}</p>
          </div>

          <div class="teacher-profile-grid mt-4">
            <div class="teacher-profile-detail">
              <span class="teacher-profile-detail__label">Experience level</span>
              <strong>{{ $experienceLabel }}</strong>
            </div>
            <div class="teacher-profile-detail">
              <span class="teacher-profile-detail__label">Students reached</span>
              <strong>{{ number_format((int) ($teacher->total_students_count ?? 0)) }}</strong>
            </div>
            <div class="teacher-profile-detail">
              <span class="teacher-profile-detail__label">Published classes</span>
              <strong>{{ $teacher->total_courses_count }}</strong>
            </div>
            @if(filled($teacher->phone))
              <div class="teacher-profile-detail">
                <span class="teacher-profile-detail__label">Phone</span>
                <strong dir="ltr">{{ $teacher->phone }}</strong>
              </div>
            @endif
          </div>

          @if($expertiseList->isNotEmpty())
            <div class="teacher-directory-card__sections mt-4">
              <span class="teacher-directory-card__sections-title">Expertise & tools</span>
              <div class="teacher-directory-card__chips">
                @foreach($expertiseList as $skill)
                  <span class="teacher-chip teacher-chip--soft">{{ $skill }}</span>
                @endforeach
              </div>
            </div>
          @endif

          <div class="teacher-directory-card__sections mt-4">
            <span class="teacher-directory-card__sections-title">Teaching sections</span>
            <div class="teacher-directory-card__chips">
              @foreach($teacher->teaching_sections as $section)
                <span class="teacher-chip">{{ $section }}</span>
              @endforeach
            </div>
          </div>

          @if($contactLinks->isNotEmpty())
            <div class="teacher-profile-links mt-4">
              @foreach($contactLinks as $link)
                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="teacher-profile-link">
                  <i class="bi {{ $link['icon'] }}"></i>
                  <span>{{ $link['label'] }}</span>
                </a>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Teacher's Courses --}}
    <div class="teacher-courses mt-5">
      <h2 class="section-title mb-4">Courses by {{ $teacher->name }}</h2>

      @if($teacher->public_courses->isEmpty())
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          No published courses available from this teacher yet.
        </div>
      @else
        <div class="row g-4">
          @foreach($teacher->public_courses as $course)
          <div class="col-lg-4 col-md-6">
            <article class="teacher-directory-card teacher-directory-card--course h-100">
              <div class="teacher-directory-card__header teacher-directory-card__header--course">
                <div class="teacher-directory-card__identity">
                  <span class="teacher-directory-card__department">{{ $course->category?->name ?? 'General' }}</span>
                  <h2>{{ $course->title }}</h2>
                  <p>{{ ucfirst($course->level ?? 'self-paced') }}</p>
                </div>
                <div class="teacher-directory-card__rating">
                  <i class="bi bi-star-fill"></i>
                  <span>{{ number_format($course->rating ?? 0, 1) }}</span>
                </div>
              </div>
              <p class="teacher-directory-card__bio">{{ Str::limit(strip_tags($course->description), 120) }}</p>
              <div class="teacher-directory-card__stats">
                <div class="teacher-stat-box">
                  <span class="teacher-stat-box__icon"><i class="bi bi-clock-fill"></i></span>
                  <div>
                    <strong>{{ $course->duration_hours ?: 'Self-paced' }}</strong>
                    <span>{{ $course->duration_hours ? 'Hours' : 'Duration' }}</span>
                  </div>
                </div>
                <div class="teacher-stat-box">
                  <span class="teacher-stat-box__icon"><i class="bi bi-people-fill"></i></span>
                  <div>
                    <strong>{{ number_format($course->enrolled_count) }}</strong>
                    <span>Students</span>
                  </div>
                </div>
              </div>
              <div class="teacher-directory-card__footer">
                <a href="{{ route('courses.detail', $course->slug) }}" class="teacher-directory-card__cta">
                  View course
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </article>
          </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</section>
@endsection
