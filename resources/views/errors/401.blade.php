<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Unauthorized | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-purple"></div>
    <div class="glow-orb glow-2-blue"></div>

    <div class="error-container">
        <div class="error-content border-401">
            <div class="error-code code-401">401</div>
            <div class="error-icon icon-401">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="8" r="4" stroke-linecap="round"/>
                    <path d="M20 21a8 8 0 1 0-16 0" stroke-linecap="round"/>
                    <line x1="18" y1="8" x2="22" y2="8" stroke-linecap="round"/>
                    <line x1="19" y1="5" x2="21" y2="3" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 class="error-title">Unauthorized</h1>
            <p class="error-message">
                You need to be logged in to access this page.<br>
                Please sign in and try again.
            </p>
            <div class="error-actions">
                <a href="{{ route('login') }}" class="error-btn error-btn-purple">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Sign In
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline-purple">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
