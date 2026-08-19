<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-orange"></div>
    <div class="glow-orb glow-2-pink"></div>

    <div class="error-container">
        <div class="error-content border-403">
            <div class="error-code code-403">403</div>
            <div class="error-icon icon-403">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 class="error-title">Access Denied</h1>
            <p class="error-message">
                You don't have permission to access this page.<br>
                Please contact an administrator if you believe this is an error.
            </p>
            <div class="error-actions">
                <a href="{{ url()->previous() }}" class="error-btn error-btn-orange">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Go Back
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline-orange">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
