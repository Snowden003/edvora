@extends('layouts.auth')

@section('title', 'Reset Password - Edvora Tech')

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
                        
                        {{-- Header Icon & Title --}}
                        <div class="text-center mb-4">
                            <div class="auth-icon-wrapper mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: linear-gradient(135deg, rgba(31,143,255,0.15) 0%, rgba(0,102,255,0.08) 100%); border: 2px solid rgba(31,143,255,0.25);">
                                    <i class="bi bi-shield-lock-fill" style="font-size: 2rem; color: #1F8FFF;"></i>
                                </div>
                            </div>
                            <h2 class="auth-title">Reset Password</h2>
                            <p class="auth-subtitle">Create a new, strong password for your account</p>
                        </div>

                        {{-- Errors Alert --}}
                        @if ($errors->any())
                        <div class="alert alert-danger py-2 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                <li class="small">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Session Status --}}
                        @if (session('status'))
                        <div class="alert alert-success py-2 small mb-3">
                            <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
                        </div>
                        @endif

                        {{-- Form --}}
                        <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                            @csrf

                            {{-- Hidden Token --}}
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            {{-- Email --}}
                            <div class="mb-3 auth-input-group">
                                <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $request->email) }}" 
                                           required 
                                           autofocus 
                                           autocomplete="username" 
                                           readonly 
                                           style="background-color: #f1f5f9; cursor: not-allowed;">
                                </div>
                                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- New Password --}}
                            <div class="mb-3 auth-input-group">
                                <label for="password" class="form-label fw-semibold small text-muted">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter new password (min. 8 characters)" 
                                           required 
                                           autocomplete="new-password">
                                    <button class="btn togglePass" type="button" onclick="toggleFieldVisibility('password', 'togglePasswordIcon')" aria-label="Toggle password visibility" tabindex="-1">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-4 auth-input-group">
                                <label for="password_confirmation" class="form-label fw-semibold small text-muted">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Re-enter new password" 
                                           required 
                                           autocomplete="new-password">
                                    <button class="btn togglePass" type="button" onclick="toggleFieldVisibility('password_confirmation', 'toggleConfirmPasswordIcon')" aria-label="Toggle confirm password visibility" tabindex="-1">
                                        <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="btn btn-primary auth-submit-btn w-100 mb-3 py-2 fw-semibold">
                                <i class="bi bi-arrow-repeat me-2"></i>Reset Password
                            </button>

                            {{-- Back to login --}}
                            <p class="text-center mb-0">
                                <a href="{{ route('login') }}" class="auth-link small text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Sign In
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

@push('scripts')
<script>
    function toggleFieldVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endpush
