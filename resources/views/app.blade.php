<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Early Lang/RTL detection to eliminate layout flash -->
    <script>
        (function() {
            try {
                var savedLang = localStorage.getItem('edvora_lang') || 'fa';
                var isRtl = savedLang === 'fa';
                document.documentElement.lang = savedLang;
                document.documentElement.dir = isRtl ? 'rtl' : 'ltr';
                if (isRtl) {
                    document.documentElement.classList.add('rtl-layout');
                }
            } catch(e) {}
        })();
    </script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    <!-- Google Fonts: Poppins & Vazirmatn with non-blocking swap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Vazirmatn:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Vazirmatn CDN Fallback -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/fonts/webfonts/Vazirmatn-font-face.css" />

    <!-- Global Edvora Sidebar CSS (Unified with the whole application) -->
    <link href="{{ asset('assets/css/sidebar-v2.css') }}?v={{ file_exists(public_path('assets/css/sidebar-v2.css')) ? filemtime(public_path('assets/css/sidebar-v2.css')) : time() }}" rel="stylesheet" />

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-brand-500 selection:text-white min-h-screen">
    @inertia
</body>
</html>
