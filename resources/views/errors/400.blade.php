<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 - Bad Request | Edvora</title>
    <link href="{{ asset('assets/css/error-pages.css') }}" rel="stylesheet" />
</head>
<body class="error-page">
    <div class="glow-orb glow-1-yellow"></div>
    <div class="glow-orb glow-2-orange"></div>

    <div class="error-container">
        <div class="error-content border-400">
            <div class="error-code code-400">400</div>
            <div class="error-icon icon-400">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="12" y1="9" x2="12" y2="13" stroke-linecap="round"/>
                    <line x1="12" y1="17" x2="12.01" y2="17" stroke-linecap="round"/>
                </svg>
            </div>
            <h1 class="error-title">Bad Request</h1>
            <p class="error-message">
                The server couldn't understand your request.<br>
                Please check the URL or try again.
            </p>
            <div class="error-actions">
                <a href="{{ url()->previous() }}" class="error-btn error-btn-yellow">
                    <svg class="error-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Go Back
                </a>
                <a href="{{ route('home') }}" class="error-btn error-btn-outline-yellow">
                    Home Page
                </a>
            </div>
        </div>
    </div>
</body>
</html>
