<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <title>{{ $member->name }} | شناسه رسمی عضو تیم ادورا تک</title>
    
    <meta name="description" content="کارت شناسایی دیجیتال و اطلاعات تماس رسمی {{ $member->name }}، {{ $member->role_title }} در ادورا تک.">
    <meta property="og:title" content="{{ $member->name }} - {{ $member->role_title }} | ادورا تک">
    <meta property="og:description" content="پروفایل هوشمند و تایید هویت رسمی {{ $member->name }} در سامانه ادورا تک">
    <meta property="og:image" content="{{ $member->avatar_url }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ $member->public_url }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Vazirmatn', 'Plus Jakarta Sans', 'sans-serif'],
                        display: ['Vazirmatn', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        },
                        accent: {
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: #0b0f19;
            color: #f1f5f9;
            background-image: 
                radial-gradient(at 100% 0%, rgba(14, 165, 233, 0.12) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(20, 184, 166, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.8) 0px, transparent 100%);
            min-height: 100vh;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-subtle {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .qr-wrapper svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        @keyframes pulseSlow {
            0%, 100% { opacity: 0.2; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(1.05); }
        }
        .glow-sphere {
            animation: pulseSlow 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center p-4 sm:p-6 lg:p-8 relative selection:bg-brand-500 selection:text-white">

    <!-- Ambient glowing spheres -->
    <div class="fixed top-1/4 -right-24 w-96 h-96 bg-brand-500/15 rounded-full blur-3xl pointer-events-none glow-sphere"></div>
    <div class="fixed bottom-1/4 -left-24 w-96 h-96 bg-accent-500/15 rounded-full blur-3xl pointer-events-none glow-sphere" style="animation-delay: -3s;"></div>

    <div class="w-full max-w-md my-auto relative z-10 space-y-4">

        <!-- Top Verified Banner -->
        <div class="glass-subtle rounded-2xl p-3 flex items-center justify-between shadow-xl">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-bold tracking-wide text-emerald-400">شناسه معتبر سازمانی ادورا تک</span>
            </div>
            <a href="{{ url('/') }}" class="text-xs font-semibold text-slate-400 hover:text-white transition flex items-center gap-1">
                <span>Edvora Tech</span>
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>

        <!-- Main Digital Identity Card -->
        <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl relative border border-slate-700/60">
            <!-- Header Pattern / Gradient -->
            <div class="h-28 bg-gradient-to-r from-brand-600 via-teal-600 to-accent-600 relative overflow-hidden flex items-start justify-between p-4">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                <div class="relative z-10 flex items-center gap-1.5 text-white/90 text-xs font-bold bg-black/30 backdrop-blur-md px-3 py-1 rounded-full border border-white/10">
                    <i class="bi bi-patch-check-fill text-emerald-400"></i>
                    <span>عضو رسمی تیم</span>
                </div>

                @if($member->employee_id)
                    <div class="relative z-10 text-[11px] font-mono font-bold text-white/90 bg-black/30 backdrop-blur-md px-2.5 py-1 rounded-full border border-white/10" dir="ltr">
                        {{ $member->employee_id }}
                    </div>
                @endif
            </div>

            <!-- Avatar & Profile Details -->
            <div class="px-6 pb-6 pt-0 relative">
                <!-- Avatar Floating -->
                <div class="relative -mt-14 mb-4 flex justify-between items-end">
                    <div class="relative inline-block">
                        <img 
                            src="{{ $member->avatar_url }}" 
                            alt="{{ $member->name }}" 
                            class="w-24 h-24 rounded-2xl object-cover border-4 border-slate-900 shadow-2xl bg-slate-800"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=0d9488&color=ffffff&size=256'"
                        />
                        @if($member->status === 'active')
                            <div class="absolute -bottom-1 -left-1 bg-emerald-500 w-5 h-5 rounded-full border-2 border-slate-900 flex items-center justify-center text-[10px] text-white" title="فعال در تیم">
                                <i class="bi bi-check"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Quick QR Trigger Toggle -->
                    <button 
                        type="button" 
                        onclick="toggleQrView()" 
                        id="toggleQrBtn"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold border border-slate-700 transition active:scale-95 shadow-md"
                    >
                        <i class="bi bi-qr-code text-brand-400"></i>
                        <span id="qrBtnText">نمایش QR کد</span>
                    </button>
                </div>

                <!-- Profile Info -->
                <div class="space-y-1 mb-5">
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-white tracking-tight">{{ $member->name }}</h1>
                        <span class="text-accent-400 text-lg" title="تایید هویت شده">
                            <i class="bi bi-patch-check-fill"></i>
                        </span>
                    </div>

                    <p class="text-sm font-bold text-brand-400">
                        {{ $member->role_title }}
                    </p>

                    @if($member->department)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-slate-800 text-slate-300 text-xs font-medium border border-slate-700/60 mt-1">
                            <i class="bi bi-buildings text-slate-400 text-[11px]"></i>
                            <span>{{ $member->department }}</span>
                        </div>
                    @endif
                </div>

                <!-- Bio / Responsibilities -->
                @if($member->bio)
                    <div class="p-3.5 rounded-2xl bg-slate-800/50 border border-slate-700/40 text-xs text-slate-300 leading-relaxed mb-5">
                        {{ $member->bio }}
                    </div>
                @endif

                <!-- Action: Save to Contacts (vCard) -->
                <div class="mb-5">
                    <a 
                        href="{{ $member->vcard_url }}" 
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-gradient-to-r from-brand-600 to-teal-500 hover:from-brand-500 hover:to-teal-400 text-white font-bold text-sm shadow-lg shadow-brand-500/20 active:scale-[0.98] transition-all"
                    >
                        <i class="bi bi-person-plus-fill text-base"></i>
                        <span>ذخیره مستقیم در مخاطبین گوشی</span>
                    </a>
                </div>

                <!-- Contact & Social Channels List -->
                <div class="space-y-2 mb-5">
                    @if($member->phone)
                        <a 
                            href="tel:{{ $member->phone }}" 
                            class="flex items-center justify-between p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/50 hover:border-slate-600 transition group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">شماره تماس</span>
                                    <span class="text-xs font-bold text-white tracking-wider font-mono" dir="ltr">{{ $member->phone }}</span>
                                </div>
                            </div>
                            <i class="bi bi-arrow-left text-slate-500 group-hover:text-emerald-400 transition text-sm"></i>
                        </a>
                    @endif

                    @if($member->email)
                        <a 
                            href="mailto:{{ $member->email }}" 
                            class="flex items-center justify-between p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/50 hover:border-slate-600 transition group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">پست الکترونیکی</span>
                                    <span class="text-xs font-bold text-white font-mono" dir="ltr">{{ $member->email }}</span>
                                </div>
                            </div>
                            <i class="bi bi-arrow-left text-slate-500 group-hover:text-sky-400 transition text-sm"></i>
                        </a>
                    @endif

                    @if($member->telegram)
                        <a 
                            href="https://t.me/{{ ltrim($member->telegram, '@') }}" 
                            target="_blank" 
                            rel="noreferrer"
                            class="flex items-center justify-between p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/50 hover:border-slate-600 transition group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm">
                                    <i class="bi bi-telegram"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">پیام‌رسان تلگرام</span>
                                    <span class="text-xs font-bold text-white font-mono" dir="ltr">@<span>{{ ltrim($member->telegram, '@') }}</span></span>
                                </div>
                            </div>
                            <i class="bi bi-box-arrow-up-right text-slate-500 group-hover:text-blue-400 transition text-xs"></i>
                        </a>
                    @endif

                    @if($member->linkedin)
                        <a 
                            href="{{ str_starts_with($member->linkedin, 'http') ? $member->linkedin : 'https://linkedin.com/in/' . $member->linkedin }}" 
                            target="_blank" 
                            rel="noreferrer"
                            class="flex items-center justify-between p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/50 hover:border-slate-600 transition group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-sm">
                                    <i class="bi bi-linkedin"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">لینکدین سازمانی</span>
                                    <span class="text-xs font-bold text-white font-mono truncate max-w-[200px] block" dir="ltr">{{ $member->linkedin }}</span>
                                </div>
                            </div>
                            <i class="bi bi-box-arrow-up-right text-slate-500 group-hover:text-indigo-400 transition text-xs"></i>
                        </a>
                    @endif

                    @if($member->github)
                        <a 
                            href="{{ str_starts_with($member->github, 'http') ? $member->github : 'https://github.com/' . $member->github }}" 
                            target="_blank" 
                            rel="noreferrer"
                            class="flex items-center justify-between p-3 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700/50 hover:border-slate-600 transition group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-700/60 text-slate-300 flex items-center justify-center text-sm">
                                    <i class="bi bi-github"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">گیت‌هاب</span>
                                    <span class="text-xs font-bold text-white font-mono" dir="ltr">{{ $member->github }}</span>
                                </div>
                            </div>
                            <i class="bi bi-box-arrow-up-right text-slate-500 group-hover:text-white transition text-xs"></i>
                        </a>
                    @endif
                </div>

                <!-- Collapsible QR Code Card View -->
                <div id="qrContainer" class="hidden pt-4 border-t border-slate-800 space-y-4">
                    <div class="p-4 rounded-2xl bg-white text-slate-900 flex flex-col items-center justify-center shadow-xl relative">
                        <div class="w-48 h-48 qr-wrapper">
                            {!! $qrSvg !!}
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 mt-2 text-center">
                            اسکن مستقیم جهت مشاهده مشخصات {{ $member->name }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            onclick="copyProfileUrl()" 
                            class="flex-1 py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition flex items-center justify-center gap-1.5 active:scale-95"
                        >
                            <i class="bi bi-copy text-accent-400"></i>
                            <span id="copyBtnText">کپی لینک اختصاصی</span>
                        </button>

                        <button 
                            type="button" 
                            onclick="shareProfile()" 
                            class="py-2 px-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition flex items-center justify-center gap-1.5 active:scale-95"
                            title="اشتراک‌گذاری"
                        >
                            <i class="bi bi-share text-brand-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Footer Verification Badge -->
                <div class="pt-4 mt-4 border-t border-slate-800/80 text-center space-y-1">
                    <div class="flex items-center justify-center gap-1.5 text-xs text-slate-400">
                        <i class="bi bi-shield-lock-fill text-brand-400 text-xs"></i>
                        <span>تایید هویت شده توسط ادورا تک • سامانه آموزش آنلاین</span>
                    </div>
                    <p class="text-[10px] text-slate-400/90 font-mono" dir="ltr">
                        UUID: {{ $member->uuid }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Secondary Note -->
        <p class="text-center text-xs text-slate-400/90 px-4">
            کارت شناسایی هوشمند ادورا تک • صادر شده جهت احراز هویت و ارتباط مستقیم با هم‌تیمی‌ها
        </p>

    </div>

    <!-- Toast Notification for Copy -->
    <div id="toast" class="fixed bottom-6 z-50 transition-all duration-300 transform translate-y-20 opacity-0 pointer-events-none flex items-center gap-2 bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-2xl border border-slate-700 shadow-2xl">
        <i class="bi bi-check-circle-fill text-emerald-400 text-sm"></i>
        <span id="toastMessage">لینک با موفقیت کپی شد!</span>
    </div>

    <script>
        function toggleQrView() {
            const container = document.getElementById('qrContainer');
            const btnText = document.getElementById('qrBtnText');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                btnText.textContent = 'بستن QR کد';
            } else {
                container.classList.add('hidden');
                btnText.textContent = 'نمایش QR کد';
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            toastMsg.textContent = msg;
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }

        function copyProfileUrl() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('لینک پروفایل اختصاصی کپی شد!');
                const copyBtnText = document.getElementById('copyBtnText');
                if (copyBtnText) {
                    const original = copyBtnText.textContent;
                    copyBtnText.textContent = 'کپی شد!';
                    setTimeout(() => copyBtnText.textContent = original, 2000);
                }
            }).catch(() => {
                showToast('خطا در کپی کردن لینک');
            });
        }

        function shareProfile() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $member->name }} | ادورا تک',
                    text: 'مشاهده مشخصات رسمی {{ $member->name }} در ادورا تک',
                    url: window.location.href,
                }).catch(() => {});
            } else {
                copyProfileUrl();
            }
        }
    </script>
</body>
</html>
