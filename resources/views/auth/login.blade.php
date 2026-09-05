<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Edvora Tech</title>
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
                <h2 class="auth-title">Welcome Back</h2>
                <p class="auth-subtitle">Sign in to continue</p>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger py-2 border-0" style="background: rgba(220, 53, 69, 0.15); color: #ffb3b3; transform: translateZ(15px);">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('status'))
            <div class="alert alert-success py-2 small border-0" style="background: rgba(25, 135, 84, 0.15); color: #a3ffc2; transform: translateZ(15px);">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" style="transform-style: preserve-3d;">
                @csrf

                <div class="mb-3 auth-input-group z-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                    </div>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3 auth-input-group z-group" style="transform: translateZ(25px);">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        <button class="btn btn-toggle-pass" type="button" id="togglePassword" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 z-group flex-wrap gap-2" style="transform: translateZ(15px);">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                        <label class="form-check-label small" for="rememberMe">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="auth-link small">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3 z-group" style="transform: translateZ(35px);">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>

                <a href="{{ route('google.redirect') }}" class="btn btn-google w-100 mb-4 d-flex align-items-center justify-content-center z-group" style="transform: translateZ(25px);">
                    <i class="bi bi-google me-2" style="color: #ea4335;"></i>Sign In with Google
                </a>

                <p class="text-center small mb-0 z-group" style="color: rgba(255,255,255,0.6); transform: translateZ(10px);">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="auth-link fw-bold">Sign up here</a>
                </p>
            </form>
        </div>

    </div>
</div>

<script src="{{ asset('assets/js/auth-3d.js') }}"></script>
</body>
</html>
