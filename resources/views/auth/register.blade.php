@extends('layouts.app')

@section('title', 'Sign Up - Edvora Tech')

@push('styles')
    <link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-11">
                <div class="card auth-card border-0">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="auth-title">Join Edvora!</h2>
                            <p class="auth-subtitle">Create your account and start your journey</p>
                        </div>

                        @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Role Selector --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">I want to join as</label>
                                <div class="role-selector">
                                    <label class="role-card">
                                        <input type="radio" name="role" value="student" {{ old('role', 'student') === 'student' ? 'checked' : '' }} required>
                                        <div class="role-card-inner">
                                            <i class="bi bi-mortarboard"></i>
                                            <span class="role-title">Student</span>
                                            <span class="role-desc">Learn & grow</span>
                                        </div>
                                    </label>
                                    <label class="role-card">
                                        <input type="radio" name="role" value="teacher" {{ old('role') === 'teacher' ? 'checked' : '' }}>
                                        <div class="role-card-inner">
                                            <i class="bi bi-person-video3"></i>
                                            <span class="role-title">Teacher</span>
                                            <span class="role-desc">Teach & inspire</span>
                                        </div>
                                    </label>
                                </div>
                                @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Full Name --}}
                            <div class="mb-2 auth-input-group">
                                <label for="name" class="form-label fw-semibold">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
                                </div>
                                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-2 auth-input-group">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                                </div>
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Password --}}
                            <div class="mb-2 auth-input-group">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Create a password (min 8 chars)" required>
                                    <button class="btn togglePass" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-3 auth-input-group">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>
                                </div>
                            </div>

                            {{-- Terms --}}
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                    <label class="form-check-label small" for="agreeTerms">
                                        I agree to the
                                        <a href="{{ route('terms') }}" class="auth-link">Terms of Service</a>
                                        and <a href="{{ route('privacy') }}" class="auth-link">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary auth-submit-btn w-100 mb-3">
                                <i class="bi bi-person-plus-fill me-2"></i>Create Account
                            </button>

                            <p class="text-center text-muted small mb-0">
                                Already have an account?
                                <a href="{{ route('login') }}" class="auth-link">Sign in here</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
