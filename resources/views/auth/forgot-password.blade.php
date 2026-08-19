@extends('layouts.auth')

@section('title', 'Forgot Password - Edvora Tech')

@section('auth-nav-button')
<a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">Login</a>
@endsection

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-11">
                <div class="card auth-card border-0">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="bi bi-shield-lock" style="font-size: 2.5rem; color: #1F8FFF;"></i>
                            </div>
                            <h2 class="auth-title">Forgot Password?</h2>
                            <p class="auth-subtitle">Enter your email and we'll send you a reset link.</p>
                        </div>

                        @if (session('status'))
                        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4 auth-input-group">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                                </div>
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn btn-primary auth-submit-btn w-100 mb-3">
                                <i class="bi bi-send me-2"></i>Send Reset Link
                            </button>

                            <p class="text-center mb-0">
                                <a href="{{ route('login') }}" class="auth-link small">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Login
                                </a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
