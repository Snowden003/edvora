<!DOCTYPE html>
<html lang="en" dir="ltr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <title>{{ $member->name }} | Verified Faculty Pass - Edvora Tech</title>
    
    <meta name="description" content="Official verified digital identity and landscape faculty credentials for {{ $member->name }}, {{ $member->role_title }} at Edvora Tech.">
    <meta property="og:title" content="{{ $member->name }} - {{ $member->role_title }} | Edvora Tech">
    <meta property="og:description" content="Verified personnel digital identity and direct communication channels for {{ $member->name }} at Edvora Tech.">
    <meta property="og:image" content="{{ $member->avatar_url }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ $member->public_url }}">
    <meta name="theme-color" content="#030712">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        },
                        cyber: {
                            blue: '#0ea5e9',
                            purple: '#a855f7',
                            emerald: '#10b981',
                        }
                    },
                    animation: {
                        'float-slow': 'floatSlow 6s ease-in-out infinite',
                        'float-reverse': 'floatReverse 7s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
                        'shimmer': 'shimmer 2.5s infinite',
                    },
                    keyframes: {
                        floatSlow: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(-10px) rotate(1.5deg)' },
                        },
                        floatReverse: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(8px) rotate(-1.5deg)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.08)' },
                        },
                        shimmer: {
                            '100%': { transform: 'translateX(100%)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Three.js for 3D Background Atmosphere -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #030712;
            color: #f8fafc;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
            position: relative;
        }

        #bg-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            z-index: 0;
            pointer-events: none;
        }

        /* 3D Glass Surface */
        .glass-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(3, 7, 18, 0.92) 100%);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 
                0 25px 60px -15px rgba(0, 0, 0, 0.8),
                0 0 0 1px rgba(255, 255, 255, 0.06),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.15);
        }

        .glass-subtle {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* 3D Card Perspective */
        .tilt-container {
            perspective: 1400px;
            transform-style: preserve-3d;
        }

        .tilt-card {
            transform-style: preserve-3d;
            transition: transform 0.2s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.2s ease;
            will-change: transform;
        }

        /* Holographic Dynamic Glare */
        .hologram-glare {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(255, 255, 255, 0.14) 0%, transparent 60%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            mix-blend-mode: overlay;
            border-radius: inherit;
            z-index: 30;
        }

        .tilt-card:hover .hologram-glare {
            opacity: 1;
        }

        /* Animated Glowing Border */
        .glow-border {
            position: relative;
        }

        .glow-border::before {
            content: '';
            position: absolute;
            inset: -1.5px;
            border-radius: inherit;
            padding: 1.5px;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.6), rgba(14, 165, 233, 0.3), rgba(168, 85, 247, 0.6), rgba(20, 184, 166, 0.6));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            z-index: 25;
            animation: borderGradient 8s linear infinite;
        }

        @keyframes borderGradient {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        /* QR Wrapper inside SVG */
        .qr-render svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Shimmer button highlight */
        .shimmer-btn {
            position: relative;
            overflow: hidden;
        }

        .shimmer-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                60deg,
                transparent 30%,
                rgba(255, 255, 255, 0.2) 40%,
                rgba(255, 255, 255, 0.4) 50%,
                rgba(255, 255, 255, 0.2) 60%,
                transparent 70%
            );
            transform: rotate(30deg) translateX(-100%);
            animation: shimmerEffect 4s infinite ease-in-out;
        }

        @keyframes shimmerEffect {
            0% { transform: rotate(30deg) translateX(-150%); }
            40%, 100% { transform: rotate(30deg) translateX(150%); }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-3 sm:p-6 lg:p-10 select-none">

    <!-- 3D Three.js WebGL Particle Background -->
    <canvas id="bg-canvas"></canvas>

    <!-- Floating Ambient Orbs (Fallback + Layering) -->
    <div class="fixed top-12 left-1/4 w-80 h-80 bg-brand-500/15 rounded-full blur-[110px] pointer-events-none animate-float-slow"></div>
    <div class="fixed bottom-12 right-1/4 w-96 h-96 bg-cyber-purple/15 rounded-full blur-[120px] pointer-events-none animate-float-reverse"></div>
    <div class="fixed top-1/2 right-12 w-64 h-64 bg-cyber-blue/10 rounded-full blur-[90px] pointer-events-none"></div>

    <!-- Main Container: WIDE LANDSCAPE (max-w-5xl) -->
    <div class="w-full max-w-5xl mx-auto my-auto relative z-10 space-y-4">

        <!-- Top Verified Security Banner -->
        <header class="glass-subtle rounded-2xl px-5 py-3 flex items-center justify-between shadow-2xl border border-white/10">
            <div class="flex items-center gap-3">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-80"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-glow"></span>
                </span>
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-400 font-mono">
                    Official Cryptographic Faculty Pass // Landscape Clearance
                </span>
            </div>

            <div class="flex items-center gap-3">
                <a 
                    href="{{ route('team.index') }}" 
                    class="text-xs font-semibold text-brand-400 hover:text-brand-300 transition flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700"
                >
                    <i class="bi bi-people"></i>
                    <span>All Faculty</span>
                </a>
                <span class="text-slate-600 hidden sm:inline">•</span>
                <a 
                    href="{{ url('/') }}" 
                    class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition duration-200 group"
                >
                    <span class="font-bold text-slate-300 group-hover:text-brand-400 transition">Edvora Tech</span>
                    <i class="bi bi-arrow-up-right text-[10px] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                </a>
            </div>
        </header>

        <!-- Stable LANDSCAPE Identity Card -->
        <div>
            <main 
                id="mainCard" 
                class="glass-card rounded-[2.5rem] overflow-hidden relative glow-border transition-all duration-300 hover:border-cyan-400/30 hover:shadow-2xl"
            >

                <!-- LANDSCAPE GRID: 2 Columns on md+ -->
                <div class="grid grid-cols-1 md:grid-cols-12 items-stretch">

                    <!-- ─── LEFT COLUMN: Identity & QR Showcase (md:col-span-5) ─── -->
                    <div class="md:col-span-5 bg-gradient-to-b from-slate-900/90 via-slate-950/95 to-slate-950/95 p-6 sm:p-8 flex flex-col justify-between border-b md:border-b-0 md:border-r border-white/10 relative overflow-hidden">
                        
                        <!-- Background Cyber Watermark / Grid -->
                        <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
                        <div class="absolute -top-16 -left-16 w-48 h-48 bg-brand-500/20 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-cyber-blue/15 rounded-full blur-3xl pointer-events-none"></div>

                        <!-- Top ID Header -->
                        <div class="relative z-10 flex items-center justify-between gap-2 mb-6">
                            <!-- Employee ID Pill -->
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-950/80 backdrop-blur-md border border-white/15 text-xs font-mono font-bold text-slate-200 shadow-sm">
                                <span class="text-brand-400">ID:</span>
                                <span>{{ $member->employee_id ?: 'EDV-1001' }}</span>
                            </div>

                            <!-- Verified Badge -->
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-950/70 border border-emerald-500/30 text-[11px] font-bold text-emerald-400">
                                <i class="bi bi-patch-check-fill text-emerald-400"></i>
                                <span>Verified Member</span>
                            </div>
                        </div>

                        <!-- Avatar & Main Profile Header -->
                        <div class="relative z-10 flex flex-col items-center text-center space-y-4 mb-6">
                            <!-- Avatar with animated glowing rings -->
                            <div class="relative group/avatar">
                                <div class="absolute -inset-1.5 rounded-[2rem] bg-gradient-to-r from-brand-400 via-cyber-blue to-cyber-purple opacity-70 blur-md group-hover/avatar:opacity-100 transition duration-300 animate-pulse-glow"></div>
                                
                                <img 
                                    src="{{ $member->avatar_url }}" 
                                    alt="{{ $member->name }}" 
                                    class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-[1.8rem] object-cover border-4 border-slate-950 shadow-2xl bg-slate-900 transition-transform duration-300 group-hover/avatar:scale-105"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0d9488&color=ffffff&size=256&bold=true'"
                                />

                                @if($member->status === 'active')
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-emerald-500 border-3 border-slate-950 flex items-center justify-center text-white text-xs shadow-lg" title="Active Faculty">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                @endif
                            </div>

                            <!-- Name & Title -->
                            <div class="space-y-1">
                                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                                    {{ $member->name }}
                                </h1>
                                <p class="text-sm sm:text-base font-bold bg-gradient-to-r from-brand-300 via-teal-300 to-cyber-blue bg-clip-text text-transparent">
                                    {{ $member->role_title }}
                                </p>
                            </div>

                            <!-- Department Badge -->
                            @if($member->department)
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-900/90 border border-slate-800 text-xs font-semibold text-slate-300">
                                    <i class="bi bi-buildings text-brand-400"></i>
                                    <span>{{ $member->department }}</span>
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Embedded Clean QR Code Showcase -->
                        <div class="relative z-10 p-4 rounded-3xl bg-slate-950/70 border border-slate-800/80 backdrop-blur-md text-center space-y-3">
                            <div class="p-3 bg-white rounded-2xl shadow-xl border border-slate-200 inline-block mx-auto max-w-[170px]">
                                <div class="w-32 h-32 qr-render">
                                    {!! $qrSvg !!}
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 font-mono">
                                Scan with phone camera for verified cryptographic check
                            </p>
                        </div>

                        <!-- Save Contact to Phone Button -->
                        <div class="relative z-10 pt-4">
                            <a 
                                href="{{ $member->vcard_url }}" 
                                onclick="triggerHaptic()"
                                class="shimmer-btn w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-gradient-to-r from-brand-600 via-teal-500 to-cyber-blue hover:from-brand-500 hover:to-teal-400 text-white font-extrabold text-xs sm:text-sm shadow-xl shadow-brand-500/25 active:scale-[0.98] transition-all"
                            >
                                <i class="bi bi-person-plus-fill text-base"></i>
                                <span>Save Contact to Phone (vCard)</span>
                            </a>
                        </div>
                    </div>

                    <!-- ─── RIGHT COLUMN: Bio, Direct Channels & Verification (md:col-span-7) ─── -->
                    <div class="md:col-span-7 p-6 sm:p-8 flex flex-col justify-between bg-slate-950/80 relative">
                        
                        <div>
                            <!-- Header Action Bar -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pb-5 mb-5 border-b border-slate-800/80">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-300 font-mono">
                                        Faculty Credentials & Direct Channels
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button 
                                        type="button" 
                                        onclick="copyLink()" 
                                        class="flex items-center gap-1.5 py-1.5 px-3 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-slate-200 hover:text-white text-xs font-bold border border-slate-700 transition active:scale-95 shadow-sm"
                                    >
                                        <i class="bi bi-copy text-brand-400"></i>
                                        <span id="copyBtnText">Copy Link</span>
                                    </button>

                                    <button 
                                        type="button" 
                                        onclick="shareProfile()" 
                                        class="flex items-center gap-1.5 py-1.5 px-3 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-slate-200 hover:text-white text-xs font-bold border border-slate-700 transition active:scale-95 shadow-sm"
                                    >
                                        <i class="bi bi-share text-cyber-blue"></i>
                                        <span>Share</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Executive Bio / Overview -->
                            @if($member->bio)
                            <div class="mb-6">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2 font-mono">
                                    Executive Statement & Responsibilities
                                </span>
                                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 text-xs sm:text-sm text-slate-200 leading-relaxed relative overflow-hidden">
                                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-brand-400 via-cyan-400 to-indigo-500"></div>
                                    <p class="pl-3 italic">
                                        "{{ $member->bio }}"
                                    </p>
                                </div>
                            </div>
                            @endif

                            <!-- Direct Communication Channels (2-Column Grid) -->
                            <div class="space-y-2.5 mb-6">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block font-mono">
                                    Direct Verified Channels
                                </span>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @if($member->phone)
                                    <a 
                                        href="tel:{{ $member->phone }}" 
                                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800/90 border border-slate-800 hover:border-slate-700 transition group active:scale-[0.99]"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                                                <i class="bi bi-telephone-fill"></i>
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-slate-400 uppercase font-mono block">Direct Phone</span>
                                                <span class="text-xs font-bold text-white font-mono">{{ $member->phone }}</span>
                                            </div>
                                        </div>
                                        <i class="bi bi-chevron-right text-slate-600 group-hover:text-emerald-400 group-hover:translate-x-0.5 transition text-xs"></i>
                                    </a>
                                    @endif

                                    @if($member->email)
                                    <a 
                                        href="mailto:{{ $member->email }}" 
                                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800/90 border border-slate-800 hover:border-slate-700 transition group active:scale-[0.99]"
                                    >
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm group-hover:scale-110 transition-transform shrink-0">
                                                <i class="bi bi-envelope-fill"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-[10px] text-slate-400 uppercase font-mono block">Official Email</span>
                                                <span class="text-xs font-bold text-white font-mono truncate block">{{ $member->email }}</span>
                                            </div>
                                        </div>
                                        <i class="bi bi-chevron-right text-slate-600 group-hover:text-sky-400 group-hover:translate-x-0.5 transition text-xs shrink-0"></i>
                                    </a>
                                    @endif

                                    @if($member->telegram)
                                    <a 
                                        href="https://t.me/{{ ltrim($member->telegram, '@') }}" 
                                        target="_blank" 
                                        rel="noreferrer"
                                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800/90 border border-slate-800 hover:border-slate-700 transition group active:scale-[0.99]"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm group-hover:scale-110 transition-transform">
                                                <i class="bi bi-telegram"></i>
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-slate-400 uppercase font-mono block">Telegram Handle</span>
                                                <span class="text-xs font-bold text-white font-mono">@<span>{{ ltrim($member->telegram, '@') }}</span></span>
                                            </div>
                                        </div>
                                        <i class="bi bi-box-arrow-up-right text-slate-600 group-hover:text-blue-400 transition text-xs"></i>
                                    </a>
                                    @endif

                                    @if($member->linkedin)
                                    <a 
                                        href="{{ str_starts_with($member->linkedin, 'http') ? $member->linkedin : 'https://linkedin.com/in/' . $member->linkedin }}" 
                                        target="_blank" 
                                        rel="noreferrer"
                                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800/90 border border-slate-800 hover:border-slate-700 transition group active:scale-[0.99]"
                                    >
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-sm group-hover:scale-110 transition-transform shrink-0">
                                                <i class="bi bi-linkedin"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-[10px] text-slate-400 uppercase font-mono block">LinkedIn Profile</span>
                                                <span class="text-xs font-bold text-white font-mono truncate block">{{ $member->linkedin }}</span>
                                            </div>
                                        </div>
                                        <i class="bi bi-box-arrow-up-right text-slate-600 group-hover:text-indigo-400 transition text-xs shrink-0"></i>
                                    </a>
                                    @endif

                                    @if($member->github)
                                    <a 
                                        href="{{ str_starts_with($member->github, 'http') ? $member->github : 'https://github.com/' . $member->github }}" 
                                        target="_blank" 
                                        rel="noreferrer"
                                        class="flex items-center justify-between p-3 rounded-2xl bg-slate-900/60 hover:bg-slate-800/90 border border-slate-800 hover:border-slate-700 transition group active:scale-[0.99] sm:col-span-2"
                                    >
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-9 h-9 rounded-xl bg-slate-800 text-slate-200 flex items-center justify-center text-sm group-hover:scale-110 transition-transform shrink-0">
                                                <i class="bi bi-github"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-[10px] text-slate-400 uppercase font-mono block">GitHub Repository</span>
                                                <span class="text-xs font-bold text-white font-mono truncate block">{{ $member->github }}</span>
                                            </div>
                                        </div>
                                        <i class="bi bi-box-arrow-up-right text-slate-600 group-hover:text-white transition text-xs shrink-0"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Cryptographic Authentication Footer Seal -->
                        <footer class="pt-5 mt-4 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                            <div class="space-y-0.5">
                                <div class="flex items-center justify-center sm:justify-start gap-1.5 text-xs text-slate-300 font-semibold">
                                    <i class="bi bi-shield-check text-brand-400"></i>
                                    <span>Authenticated by Edvora Tech Learning Ecosystem</span>
                                </div>
                                <p class="text-[10px] text-slate-500 font-mono tracking-wider">
                                    CRYPTOGRAPHIC SEC-UUID: {{ $member->uuid }}
                                </p>
                            </div>

                            <a 
                                href="{{ route('team.index') }}" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 text-xs font-bold transition shrink-0 group"
                            >
                                <span>Browse All Faculty</span>
                                <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </footer>
                    </div>

                </div>
            </main>
        </div>

        <!-- Secondary Subtitle Note -->
        <p class="text-center text-[11px] text-slate-500 px-4 leading-normal font-mono">
            Official Executive Landscape ID Pass • Validated under Edvora Cryptographic Protocol EDV-2026
        </p>

    </div>

    <!-- Toast Notification for Copy/Share -->
    <div 
        id="toast" 
        class="fixed bottom-6 z-50 transition-all duration-300 transform translate-y-20 opacity-0 pointer-events-none flex items-center gap-2.5 bg-slate-900/95 text-white text-xs font-bold px-5 py-3 rounded-2xl border border-slate-700 shadow-2xl backdrop-blur-xl"
    >
        <i class="bi bi-check-circle-fill text-emerald-400 text-sm"></i>
        <span id="toastMessage">Link successfully copied to clipboard!</span>
    </div>

    <!-- Script: Three.js 3D Background & Dynamic Landscape 3D Tilt -->
    <script>
        // ─── 1. THREE.JS 3D BACKGROUND SIMULATION ───
        (function initThreeBackground() {
            const canvas = document.getElementById('bg-canvas');
            if (!canvas || typeof THREE === 'undefined') return;

            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.z = 80;

            const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Particles Constellation
            const particleCount = window.innerWidth < 768 ? 160 : 320;
            const geometry = new THREE.BufferGeometry();
            const positions = new Float32Array(particleCount * 3);
            const colors = new Float32Array(particleCount * 3);

            // Palette: Cyan (#2dd4bf), Sky Blue (#0ea5e9), Purple (#a855f7)
            const palette = [
                new THREE.Color(0x2dd4bf),
                new THREE.Color(0x0ea5e9),
                new THREE.Color(0xa855f7),
                new THREE.Color(0x10b981),
            ];

            for (let i = 0; i < particleCount; i++) {
                positions[i * 3]     = (Math.random() - 0.5) * 160;
                positions[i * 3 + 1] = (Math.random() - 0.5) * 160;
                positions[i * 3 + 2] = (Math.random() - 0.5) * 120;

                const c = palette[Math.floor(Math.random() * palette.length)];
                colors[i * 3]     = c.r;
                colors[i * 3 + 1] = c.g;
                colors[i * 3 + 2] = c.b;
            }

            geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

            const pMaterial = new THREE.PointsMaterial({
                size: 2.2,
                vertexColors: true,
                transparent: true,
                opacity: 0.65,
                blending: THREE.AdditiveBlending
            });

            const particleSystem = new THREE.Points(geometry, pMaterial);
            scene.add(particleSystem);

            // Floating 3D Geometric Polyhedra (Icosahedron & Dodecahedron)
            const wireMaterial1 = new THREE.MeshBasicMaterial({
                color: 0x14b8a6,
                wireframe: true,
                transparent: true,
                opacity: 0.22,
            });
            const poly1 = new THREE.Mesh(new THREE.IcosahedronGeometry(22, 1), wireMaterial1);
            poly1.position.set(-35, 20, -25);
            scene.add(poly1);

            const wireMaterial2 = new THREE.MeshBasicMaterial({
                color: 0xa855f7,
                wireframe: true,
                transparent: true,
                opacity: 0.18,
            });
            const poly2 = new THREE.Mesh(new THREE.DodecahedronGeometry(18, 0), wireMaterial2);
            poly2.position.set(40, -25, -20);
            scene.add(poly2);

            // Responsive resizing
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
                renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            });

            // Mouse parallax target
            let mouseX = 0, mouseY = 0;
            let targetX = 0, targetY = 0;
            window.addEventListener('mousemove', (e) => {
                mouseX = (e.clientX - window.innerWidth / 2) * 0.05;
                mouseY = (e.clientY - window.innerHeight / 2) * 0.05;
            });

            // Gyroscope tilt on mobile devices
            if (window.DeviceOrientationEvent) {
                window.addEventListener('deviceorientation', (e) => {
                    if (e.gamma !== null && e.beta !== null) {
                        mouseX = e.gamma * 0.8;
                        mouseY = (e.beta - 45) * 0.8;
                    }
                }, false);
            }

            // Animation Loop
            let clock = new THREE.Clock();
            function animate() {
                requestAnimationFrame(animate);
                const delta = clock.getDelta();

                // Gentle rotation
                particleSystem.rotation.y += delta * 0.04;
                particleSystem.rotation.x += delta * 0.015;

                poly1.rotation.x += delta * 0.12;
                poly1.rotation.y += delta * 0.15;

                poly2.rotation.x -= delta * 0.1;
                poly2.rotation.y += delta * 0.18;

                // Smooth camera follow mouse
                targetX += (mouseX - targetX) * 0.03;
                targetY += (mouseY - targetY) * 0.03;

                camera.position.x = targetX * 0.3;
                camera.position.y = -targetY * 0.3;
                camera.lookAt(scene.position);

                renderer.render(scene, camera);
            }
            animate();
        })();

        // ─── 2. UI FUNCTIONS: HAPTIC, TOAST, COPY, SHARE ───
        function triggerHaptic() {
            if (navigator.vibrate) {
                navigator.vibrate(40);
            }
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const msg = document.getElementById('toastMessage');
            msg.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }

        function copyLink() {
            triggerHaptic();
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('Verified Profile link copied to clipboard!');
                const copyBtn = document.getElementById('copyBtnText');
                if (copyBtn) {
                    const orig = copyBtn.textContent;
                    copyBtn.textContent = 'Copied!';
                    setTimeout(() => copyBtn.textContent = orig, 2000);
                }
            }).catch(() => {
                showToast('Could not copy link');
            });
        }

        function shareProfile() {
            triggerHaptic();
            if (navigator.share) {
                navigator.share({
                    title: '{{ $member->name }} | Edvora Tech Faculty Pass',
                    text: 'View verified executive credentials and channels of {{ $member->name }} ({{ $member->role_title }}) at Edvora Tech.',
                    url: window.location.href,
                }).catch(() => {});
            } else {
                copyLink();
            }
        }
    </script>
</body>
</html>
