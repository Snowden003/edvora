<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Page Expired | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-teal"></div>
    <div class="glow-orb glow-2-cyan"></div>

    <div class="error-container">
        <div class="error-content border-419">
            <div class="error-code code-419">419</div>
            <div class="error-icon icon-419">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10" stroke-linecap="round"/>
                    <polyline points="12 6 12 12 16 14" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="error-title">Page Expired</h1>
            <p class="error-message">
                Your session has expired due to inactivity.<br>
                Please refresh the page and try again.
            </p>
            <div class="error-actions">
                <a href="{{ url()->current() }}" class="error-btn error-btn-teal">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"/>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                    </svg>
                    Refresh Page
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline-teal">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
