@extends('layouts.app')

@section('title', 'Application Under Review - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/teacher-onboarding.css') }}" rel="stylesheet" />
@endpush

@section('content')
<section class="onboarding-section d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-11">

                {{-- Popup-style pending card --}}
                <div class="pending-card text-center">
                    <div class="pending-icon-wrap">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h2 class="pending-title">Application Under Review</h2>
                    <p class="pending-subtitle">
                        Thank you, <strong>{{ Auth::user()->name }}</strong>!<br>
                        Your teacher application has been received and is currently being reviewed by our admin team.
                    </p>

                    <div class="pending-steps">
                        <div class="pending-step done">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Profile submitted</span>
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
                            <span>Start teaching!</span>
                        </div>
                    </div>

                    <div class="pending-note">
                        <i class="bi bi-info-circle me-1"></i>
                        This usually takes <strong>1–2 business days</strong>. You'll receive an email once your account is approved.
                    </div>

                    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="bi bi-house me-2"></i>Go to Homepage
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
