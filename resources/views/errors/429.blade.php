<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Too Many Requests | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-orange"></div>
    <div class="glow-orb glow-2-yellow"></div>

    <div class="error-container">
        <div class="error-content border-429">
            <div class="error-code code-429">429</div>
            <div class="error-icon icon-429">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="error-title">Too Many Requests</h1>
            <p class="error-message">
                You've sent too many requests in a short time.<br>
                Please slow down and try again in a moment.
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
