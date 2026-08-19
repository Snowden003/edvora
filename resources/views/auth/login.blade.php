@extends('layouts.app')

@section('title', 'Login - Edvora Tech')

@push('styles')
    <link href="{{ asset('assets/css/auth-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-11">
                <div class="card auth-card border-0">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="auth-title">Welcome Back!</h2>
                            <p class="auth-subtitle">Sign in to continue your learning journey</p>
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

                        @if (session('status'))
                        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3 auth-input-group">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                                </div>
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3 auth-input-group">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required>
                                    <button class="btn togglePass" type="button" id="togglePassword">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                    <label class="form-check-label small" for="rememberMe">Remember me</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="auth-link small">Forgot Password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary auth-submit-btn w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>

                            <p class="text-center text-muted small mb-0">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="auth-link">Sign up here</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
