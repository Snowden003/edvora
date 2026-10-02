<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA & Mobile Web App Meta & Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Edvora">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512x512.png">

    <!-- Early Lang/RTL detection to eliminate layout flash -->
    <script>
        (function () {
            try {
                var savedLang = localStorage.getItem('edvora_lang') || 'fa';
                var isRtl = savedLang === 'fa';
                document.documentElement.lang = savedLang;
                document.documentElement.dir = isRtl ? 'rtl' : 'ltr';
                if (isRtl) {
                    document.documentElement.classList.add('rtl-layout');
                }
            } catch (e) { }
        })();
    </script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- Google Fonts: Poppins & Vazirmatn with non-blocking swap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Vazirmatn:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Vazirmatn CDN Fallback -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/webfonts/Vazirmatn-font-face.css" />

    <!-- Global Edvora Sidebar CSS (Unified with the whole application) -->
    <link
        href="{{ asset('assets/css/sidebar-v2.css') }}?v={{ file_exists(public_path('assets/css/sidebar-v2.css')) ? filemtime(public_path('assets/css/sidebar-v2.css')) : time() }}"
        rel="stylesheet" />

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>

<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-brand-500 selection:text-white min-h-screen">
    @inertia

    <!-- PWA Service Worker Registration & Install Prompt Capture -->
    <script>
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            window.deferredPwaPrompt = e;
            window.dispatchEvent(new CustomEvent('pwa-prompt-ready'));
        });
        window.addEventListener('appinstalled', function() {
            window.deferredPwaPrompt = null;
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(reg) {
                        // console.log('PWA ServiceWorker registered with scope:', reg.scope);
                    })
                    .catch(function(err) {
                        console.warn('PWA ServiceWorker registration failed:', err);
                    });
            });
        }
    </script>
</body>

</html>