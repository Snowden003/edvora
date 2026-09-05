<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Edvora Tech</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ asset('assets/css/auth-3d.css') }}" rel="stylesheet" />
</head>
<body>

<div class="bg-shapes">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
</div>

<div class="login-wrapper">
    <div class="glass-card" id="glassCard">
        
        <!-- Back to Home Link -->
        <a href="{{ route('home') }}" class="back-home-link" title="Back to Home">
            <i class="bi bi-arrow-left"></i>
            <span>Home</span>
        </a>

        <!-- Branding Section (Left Column - Desktop) -->
        <div class="brand-section d-none d-md-flex flex-column justify-content-center align-items-center">
            <div class="text-center w-100" style="transform-style: preserve-3d;">
                <div class="mb-4 z-group" style="transform: translateZ(60px);">
                    <i class="bi bi-mortarboard-fill" style="font-size: 5rem; color: #00F0FF; filter: drop-shadow(0 0 15px rgba(0, 240, 255, 0.5));"></i>
                </div>
                <h1 class="brand-title">Edvora Tech</h1>
                <p class="brand-tagline">Empowering Afghan women through free, practical digital skills education.</p>
                
                <div class="mt-5 z-group" style="transform: translateZ(20px);">
                    <div style="width: 60px; height: 3px; background: #1F8FFF; margin: 0 auto; box-shadow: 0 0 10px #1F8FFF;"></div>
                </div>
            </div>
        </div>

        <!-- Form Section (Right Column) -->
        <div class="form-section">
            
            <!-- Mobile Brand Header -->
            <div class="mobile-brand-header">
                <a href="{{ route('home') }}">
                    <div class="mb-1">
                        <i class="bi bi-mortarboard-fill brand-icon-mobile"></i>
                    </div>
                    <div class="brand-title-mobile">Edvora Tech</div>
                </a>
                <p class="brand-tagline-mobile">Empowering Afghan women through tech education</p>
            </div>

            <div class="text-center" style="transform-style: preserve-3d;">
                <h2 class="auth-title">Join Edvora!</h2>
                <p class="auth-subtitle">Create your account and start your journey</p>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger py-2 border-0 mb-3" style="background: rgba(220, 53, 69, 0.15); color: #ffb3b3; transform: translateZ(15px);">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" style="transform-style: preserve-3d;">
                @csrf

                {{-- Role Selector --}}
                <div class="mb-3 z-group">
                    <label class="form-label mb-2">I want to join as</label>
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
                <div class="mb-3 auth-input-group z-group">
                    <label for="name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
                    </div>
                    @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Email --}}
                <div class="mb-3 auth-input-group z-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                    </div>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Password --}}
                <div class="mb-3 auth-input-group z-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Create a password (min 8 chars)" required autocomplete="new-password">
                        <button class="btn btn-toggle-pass" type="button" id="togglePassword" aria-label="Toggle password visibility" tabindex="-1">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3 auth-input-group z-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required autocomplete="new-password">
                        <button class="btn btn-toggle-pass" type="button" id="toggleConfirmPassword" aria-label="Toggle password visibility" tabindex="-1">
                            <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Terms --}}
                <div class="mb-3 z-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label small" for="agreeTerms">
                            I agree to the
                            <a href="{{ route('terms') }}" class="auth-link">Terms of Service</a>
                            and <a href="{{ route('privacy') }}" class="auth-link">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3 z-group" style="transform: translateZ(35px);">
                    <i class="bi bi-person-plus-fill me-2"></i>Create Account
                </button>

                <a href="{{ route('google.redirect') }}?role=student" id="google-auth-link" class="btn btn-google w-100 mb-4 d-flex align-items-center justify-content-center z-group" style="transform: translateZ(25px);">
                    <i class="bi bi-google me-2" style="color: #ea4335;"></i>Continue with Google
                </a>

                <p class="text-center small mb-0 z-group" style="color: rgba(255,255,255,0.6); transform: translateZ(10px);">
                    Already have an account?
                    <a href="{{ route('login') }}" class="auth-link fw-bold">Sign in here</a>
                </p>
            </form>
        </div>

    </div>
</div>

<script src="{{ asset('assets/js/auth-3d.js') }}"></script>
</body>
</html>
