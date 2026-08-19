@extends('layouts.app')

@section('title', 'My Certificates - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/student-dashboard.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/certificates.css') }}" rel="stylesheet" />
@endpush

@section('content')

<div class="dashboard-wrapper">
  <x-student-sidebar />

  <main class="main-content">
    <div class="container-fluid py-5">
      <div class="col-lg-10 col-12 mx-auto">

        {{-- Hero --}}
        <div class="cert-page-hero">
          <h1><i class="bi bi-award me-2"></i>My Certificates</h1>
          <p class="mb-0">All the certificates you've earned through your learning journey.</p>
        </div>

        {{-- Stats --}}
        @php
          $totalCerts = $certificates->count() + $completedEnrollments->count();
        @endphp
        <div class="cert-stat-row">
          <div class="cert-stat-box">
            <div class="cert-stat-icon gold"><i class="bi bi-award-fill"></i></div>
            <div>
              <div class="cert-stat-value">{{ $totalCerts }}</div>
              <div class="cert-stat-label">Total Certificates</div>
            </div>
          </div>
          <div class="cert-stat-box">
            <div class="cert-stat-icon blue"><i class="bi bi-patch-check-fill"></i></div>
            <div>
              <div class="cert-stat-value">{{ $certificates->count() }}</div>
              <div class="cert-stat-label">Official Certificates</div>
            </div>
          </div>
          <div class="cert-stat-box">
            <div class="cert-stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
              <div class="cert-stat-value">{{ $completedEnrollments->count() }}</div>
              <div class="cert-stat-label">Completed Courses</div>
            </div>
          </div>
        </div>

        {{-- Certificates List --}}
        @if($totalCerts === 0)
          <div class="cert-empty">
            <i class="bi bi-award"></i>
            <h5>No Certificates Yet</h5>
            <p>Complete a course to earn your first certificate!</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary mt-2">
              <i class="bi bi-mortarboard me-2"></i>Browse Courses
            </a>
          </div>
        @else
          <div class="cert-list">

            {{-- Real certificates from DB --}}
            @foreach($certificates as $cert)
            <div class="cert-item">
              <div class="cert-item-icon blue-icon">
                <i class="bi bi-patch-check-fill"></i>
              </div>
              <div class="cert-item-body">
                <div class="cert-item-title">{{ $cert->title }}</div>
                @if($cert->course)
                  <div class="cert-item-course">
                    <i class="bi bi-book me-1"></i>{{ $cert->course->title }}
                  </div>
                @endif
                <div class="cert-meta-row">
                  <span class="cert-badge date">
                    <i class="bi bi-calendar-check"></i>
                    {{ $cert->issued_at ? $cert->issued_at->format('M d, Y') : $cert->created_at->format('M d, Y') }}
                  </span>
                  <span class="cert-badge num">
                    <i class="bi bi-hash"></i>{{ $cert->certificate_number }}
                  </span>
                </div>
              </div>
              <div class="cert-item-actions">
                @if($cert->image_path)
                  <a href="{{ asset('storage/' . $cert->image_path) }}" target="_blank" class="btn-cert-view" style="margin-right: 8px;">
                    <i class="bi bi-eye"></i>View
                  </a>
                @endif
                @if($cert->file_path)
                  <a href="{{ asset('storage/' . $cert->file_path) }}" target="_blank" class="btn-cert-download">
                    <i class="bi bi-download"></i>Download
                  </a>
                @endif
              </div>
            </div>
            @endforeach

            {{-- Completed enrollments fallback --}}
            @foreach($completedEnrollments as $enrollment)
            <div class="cert-item from-enrollment">
              <div class="cert-item-icon gold-icon">
                <i class="bi bi-trophy-fill"></i>
              </div>
              <div class="cert-item-body">
                <div class="cert-item-title">{{ $enrollment->course->title }}</div>
                @if($enrollment->course->category)
                  <div class="cert-item-course">
                    <i class="bi bi-tag me-1"></i>{{ $enrollment->course->category->name }}
                  </div>
                @endif
                <div class="cert-meta-row">
                  <span class="cert-badge done">
                    <i class="bi bi-check-circle"></i>Course Completed
                  </span>
                  <span class="cert-badge date">
                    <i class="bi bi-calendar-check"></i>
                    {{ $enrollment->completed_at
                        ? $enrollment->completed_at->format('M d, Y')
                        : $enrollment->updated_at->format('M d, Y') }}
                  </span>
                  @if($enrollment->course->category)
                  <span class="cert-badge cat">
                    <i class="bi bi-layers"></i>{{ $enrollment->course->category->name }}
                  </span>
                  @endif
                </div>
              </div>
              <div class="cert-item-actions">
              </div>
            </div>
            @endforeach

          </div>
        @endif

      </div>
    </div>
  </main>
</div>

@endsection
