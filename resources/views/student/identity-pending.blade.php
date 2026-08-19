@extends('layouts.auth')

@section('title', 'Verification Pending - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-onboarding.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/student-identity.css') }}" rel="stylesheet" />
@endpush

@section('content')
<section class="onboarding-section d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-11">

                <div class="logo-wrap">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Edvora Tech">
                    </a>
                </div>

                <div class="pending-card text-center">

                    <div class="pending-icon-wrap">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <h2 class="pending-title">Identity Under Review</h2>
                    <p class="pending-subtitle">
                        Your Tazkira image has been received successfully.<br>
                        Our admin team will verify your identity shortly.<br>
                        You'll be notified by email once your account is approved.
                    </p>

                    <div class="pending-steps">
                        <div class="pending-step done">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Tazkira uploaded</span>
                        </div>
                        <div class="pending-step active">
                            <i class="bi bi-clock-fill"></i>
                            <span>Under review</span>
                        </div>
                        <div class="pending-step">
                            <i class="bi bi-envelope-fill"></i>
                            <span>Email notification</span>
                        </div>
                        <div class="pending-step">
                            <i class="bi bi-mortarboard-fill"></i>
                            <span>Access granted!</span>
                        </div>
                    </div>

                    @if($user->tazkira_image)
                    <div class="tazkira-preview">
                        <p class="preview-label">
                            <i class="bi bi-image me-1"></i>Submitted Image
                        </p>
                        <img src="{{ asset('storage/' . $user->tazkira_image) }}" alt="Submitted Tazkira" />
                    </div>
                    @endif

                    <div class="pending-note">
                        <i class="bi bi-info-circle me-1"></i>
                        This usually takes <strong>a few hours</strong>. You'll receive an email once your identity is verified.
                    </div>

                    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="bi bi-house me-2"></i>Go to Homepage
                        </a>
                        <a href="{{ route('student.identity.show') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-repeat me-2"></i>Re-upload Image
                        </a>
                    </div>

                    <div class="mt-3">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted small p-0">
                                <i class="bi bi-box-arrow-right me-1"></i>Sign out
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
