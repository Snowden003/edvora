@extends('layouts.auth')

@section('title', 'Verify Your Email - Edvora Tech')

@push('styles')
<link href="{{ asset('assets/css/verify-otp.css') }}" rel="stylesheet" />
@endpush

@section('content')
<section class="otp-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-11">
                <div class="card otp-card border-0">
                    <div class="card-body text-center">

                        <div class="otp-icon-wrap">
                            <i class="bi bi-envelope-check-fill"></i>
                        </div>

                        <h2 class="otp-title">Check Your Email</h2>
                        <p class="otp-subtitle">We sent a 6-digit code to</p>
                        <div class="otp-email-badge">
                            <i class="bi bi-envelope me-1"></i>{{ $email }}
                        </div>

                        @if ($errors->any())
                        <div class="alert alert-danger py-2 mt-3 text-start">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if (session('status') === 'otp-sent')
                        <div class="alert alert-success py-2 mt-3 small">
                            <i class="bi bi-check-circle me-1"></i>A new code has been sent to your email.
                        </div>
                        @endif

                        <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                            @csrf
                            <input type="hidden" name="code" id="otpHidden">

                            <div class="otp-inputs" id="otpInputs">
                                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="1" autocomplete="off">
                                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="2" autocomplete="off">
                                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="3" autocomplete="off">
                                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="4" autocomplete="off">
                                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="5" autocomplete="off">
                                <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" tabindex="6" autocomplete="off">
                            </div>

                            <button type="submit" class="btn otp-submit-btn" id="submitBtn" disabled>
                                <i class="bi bi-shield-check me-2"></i>Verify Email
                            </button>
                        </form>

                        <hr class="otp-divider">

                        <div class="otp-timer" id="timerSection">
                            Code expires in <span id="countdown">10:00</span>
                        </div>

                        <div class="mt-2">
                            <span class="text-muted small">Didn't receive the code? </span>
                            <form method="POST" action="{{ route('otp.resend') }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="otp-resend-btn" id="resendBtn" disabled>Resend</button>
                            </form>
                        </div>

                        <div class="mt-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link text-muted small p-0">
                                    <i class="bi bi-box-arrow-left me-1"></i>Use a different account
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/verify-otp.js') }}" defer></script>
@endpush
