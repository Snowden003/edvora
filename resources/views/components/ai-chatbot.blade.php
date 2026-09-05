@php
    $isGuest = !auth()->check();
    $guestRemaining = 5;
    $guestUsed = 0;
    if ($isGuest) {
        $ip = request()->ip() ?: '127.0.0.1';
        $ipKey = 'ai_chat_guest_ip_' . md5($ip);
        $guestToken = request()->cookie('edvora_ai_token') ?? session()->get('edvora_ai_token');
        $tokenKey = $guestToken ? ('ai_chat_guest_token_' . $guestToken) : null;
        $ipCount = (int) \Illuminate\Support\Facades\Cache::get($ipKey, 0);
        $tokenCount = $tokenKey ? (int) \Illuminate\Support\Facades\Cache::get($tokenKey, 0) : 0;
        $sessionCount = (int) session()->get('ai_guest_question_count', 0);
        $guestUsed = max($ipCount, $tokenCount, $sessionCount);
        $guestRemaining = max(0, 5 - $guestUsed);
    }
@endphp

{{-- Floating Trigger Button --}}
<button type="button" class="edvora-ai-trigger" id="edvoraAiTrigger" aria-label="Open AI Assistant">
    <div class="edvora-ai-trigger__icon">
        <i class="bi bi-robot"></i>
        <span class="edvora-ai-trigger__pulse"></span>
    </div>
    <span id="edvoraAiTriggerText">Edvora AI Assistant</span>
</button>

{{-- Chat Drawer Window --}}
<div class="edvora-ai-drawer" id="edvoraAiDrawer" dir="ltr" aria-hidden="true"
     data-is-guest="{{ $isGuest ? '1' : '0' }}"
     data-guest-used="{{ $guestUsed }}"
     data-guest-remaining="{{ $guestRemaining }}"
     data-login-url="{{ route('login') }}"
     data-register-url="{{ route('register') }}">

    {{-- ========================================================================= --}}
    {{-- STEP 1: Topic Selection / Onboarding Screen                             --}}
    {{-- ========================================================================= --}}
    <div class="edvora-ai-view is-active" id="edvoraAiTopicView">
        {{-- Topic Screen Header --}}
        <div class="edvora-ai-header">
            <div class="edvora-ai-header__profile">
                <div class="edvora-ai-header__avatar">
                    <i class="bi bi-cpu-fill"></i>
                </div>
                <div class="edvora-ai-header__info">
                    <h4 class="edvora-ai-app-title">Edvora AI Assistant <i class="bi bi-patch-check-fill edvora-ai-header__verified-icon"></i></h4>
                    <div class="edvora-ai-header__status">
                        <span class="edvora-ai-header__status-dot"></span>
                        <span class="edvora-ai-status-text">Live Database Access</span>
                    </div>
                </div>
            </div>
            <div class="edvora-ai-header__actions">
                <div class="edvora-ai-lang-switch" role="group" aria-label="Language selection">
                    <button type="button" class="edvora-ai-lang-btn is-active" data-lang-switch="en">EN</button>
                    <button type="button" class="edvora-ai-lang-btn" data-lang-switch="fa">فارسی</button>
                </div>
                <button type="button" class="edvora-ai-header__btn edvora-ai-close-btn" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- Topic Screen Scrollable Content --}}
        <div class="edvora-ai-topic-screen">


            <div class="edvora-ai-grid-heading" id="edvoraAiGridHeading">
                SELECT A CATEGORY TO START
            </div>

            {{-- Topic Cards Grid (Namecheap-inspired, spacious and sleek) --}}
            <div class="edvora-ai-cards-grid">
                {{-- 1. Courses --}}
                <button type="button" class="edvora-ai-topic-card" data-select-topic="courses">
                    <div class="edvora-ai-topic-card__icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="edvora-ai-topic-card__title" data-i18n="topic_courses_title">Courses & Curriculum</div>
                    <div class="edvora-ai-topic-card__desc" data-i18n="topic_courses_desc">Syllabus, skill levels, duration & certificates</div>
                    <i class="bi bi-chevron-right edvora-ai-topic-card__arrow"></i>
                </button>

                {{-- 2. Instructors --}}
                <button type="button" class="edvora-ai-topic-card" data-select-topic="teachers">
                    <div class="edvora-ai-topic-card__icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="edvora-ai-topic-card__title" data-i18n="topic_teachers_title">Instructors & Mentors</div>
                    <div class="edvora-ai-topic-card__desc" data-i18n="topic_teachers_desc">Expert teacher bios, specialties & courses</div>
                    <i class="bi bi-chevron-right edvora-ai-topic-card__arrow"></i>
                </button>

                {{-- 3. Live Classes --}}
                <button type="button" class="edvora-ai-topic-card" data-select-topic="classes">
                    <div class="edvora-ai-topic-card__icon">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <div class="edvora-ai-topic-card__title" data-i18n="topic_classes_title">Live Classes</div>
                    <div class="edvora-ai-topic-card__desc" data-i18n="topic_classes_desc">Active interactive classes & schedule</div>
                    <i class="bi bi-chevron-right edvora-ai-topic-card__arrow"></i>
                </button>

                {{-- 4. Events --}}
                <button type="button" class="edvora-ai-topic-card" data-select-topic="events">
                    <div class="edvora-ai-topic-card__icon">
                        <i class="bi bi-calendar-event-fill"></i>
                    </div>
                    <div class="edvora-ai-topic-card__title" data-i18n="topic_events_title">Events & Webinars</div>
                    <div class="edvora-ai-topic-card__desc" data-i18n="topic_events_desc">Upcoming workshops, dates & registration</div>
                    <i class="bi bi-chevron-right edvora-ai-topic-card__arrow"></i>
                </button>

                {{-- 5. Free Books --}}
                <button type="button" class="edvora-ai-topic-card" data-select-topic="books">
                    <div class="edvora-ai-topic-card__icon">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <div class="edvora-ai-topic-card__title" data-i18n="topic_books_title">Free Library Books</div>
                    <div class="edvora-ai-topic-card__desc" data-i18n="topic_books_desc">Downloadable programming & tech guides</div>
                    <i class="bi bi-chevron-right edvora-ai-topic-card__arrow"></i>
                </button>

                {{-- 6. General / All --}}
                <button type="button" class="edvora-ai-topic-card" data-select-topic="all">
                    <div class="edvora-ai-topic-card__icon">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                    <div class="edvora-ai-topic-card__title" data-i18n="topic_all_title">General Inquiry</div>
                    <div class="edvora-ai-topic-card__desc" data-i18n="topic_all_desc">Ask anything about the Edvora Tech platform</div>
                    <i class="bi bi-chevron-right edvora-ai-topic-card__arrow"></i>
                </button>
            </div>
        </div>

        {{-- Direct Quick Ask Footer on Screen 1 --}}
        <form class="edvora-ai-topic-screen-footer" id="edvoraAiQuickStartForm">
            <input type="text"
                   class="edvora-ai-quick-input"
                   id="edvoraAiQuickInput"
                   placeholder="Or type a question directly..."
                   autocomplete="off" />
            <button type="submit" class="edvora-ai-quick-btn" id="edvoraAiQuickStartBtn">
                <span data-i18n="start_chat_btn">Ask AI</span> <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    </div>

    {{-- ========================================================================= --}}
    {{-- STEP 2: Dedicated Chat Screen (100% Uncluttered & Spacious)              --}}
    {{-- ========================================================================= --}}
    <div class="edvora-ai-view" id="edvoraAiChatView">
        {{-- Chat Screen Minimal Header --}}
        <div class="edvora-ai-header">
            <div class="edvora-ai-header__actions">
                <button type="button" class="edvora-ai-back-btn" id="edvoraAiBackBtn" title="Back to categories">
                    <i class="bi bi-chevron-left"></i>
                    <span id="edvoraAiBackLabel">Topics</span>
                </button>
                <div class="edvora-ai-header-topic-pill" id="edvoraAiActivePill">
                    <i class="bi bi-grid"></i>
                    <span id="edvoraAiActivePillName">All</span>
                </div>
                @if($isGuest)
                <div class="edvora-ai-header-limit-pill {{ $guestRemaining === 0 ? 'is-limit' : '' }}" id="edvoraAiHeaderLimit" title="Guest free limit">
                    <i class="bi bi-patch-question-fill"></i>
                    <span id="edvoraAiHeaderLimitText">{{ $guestRemaining }}/5</span>
                </div>
                @endif
            </div>
            <div class="edvora-ai-header__actions">
                <div class="edvora-ai-lang-switch" role="group" aria-label="Language selection">
                    <button type="button" class="edvora-ai-lang-btn is-active" data-lang-switch="en">EN</button>
                    <button type="button" class="edvora-ai-lang-btn" data-lang-switch="fa">فارسی</button>
                </div>
                <button type="button" class="edvora-ai-header__btn" id="edvoraAiClear" title="Clear Chat">
                    <i class="bi bi-trash3"></i>
                </button>
                <button type="button" class="edvora-ai-header__btn edvora-ai-close-btn" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        {{-- Chat Messages Body (Spacious, full height) --}}
        <div class="edvora-ai-chat-screen">
            <div class="edvora-ai-body" id="edvoraAiBody">
                {{-- Messages injected dynamically --}}
            </div>

            {{-- Quick Suggestion Chips (Minimalist, relevant to chosen topic) --}}
            <div class="edvora-ai-suggestions" id="edvoraAiSuggestions">
                {{-- Suggestions injected dynamically --}}
            </div>

            {{-- Chat Input Footer --}}
            <div class="edvora-ai-footer">
                @if($isGuest)
                <div class="edvora-ai-meter-bar" id="edvoraAiMeterBar">
                    <div class="edvora-ai-meter-info">
                        <span class="edvora-ai-meter-label" id="edvoraAiMeterLabel">
                            <i class="bi bi-speedometer2"></i> Guest Free Limit:
                        </span>
                        <span class="edvora-ai-meter-nums">
                            <span id="edvoraAiMeterNumsText"><strong>{{ $guestUsed }}</strong>/5 asked ({{ $guestRemaining }} left)</span>
                        </span>
                    </div>
                    <div class="edvora-ai-meter-track">
                        <div class="edvora-ai-meter-fill {{ $guestRemaining <= 1 ? 'is-danger' : ($guestRemaining <= 2 ? 'is-warning' : '') }}" 
                             id="edvoraAiMeterFill" 
                             style="width: {{ min(100, ($guestUsed / 5) * 100) }}%;"></div>
                    </div>
                </div>
                @endif
                <form class="edvora-ai-input-form" id="edvoraAiForm" onsubmit="return false;">
                    <input type="text"
                           class="edvora-ai-input"
                           id="edvoraAiInput"
                           placeholder="Type your question..."
                           autocomplete="off" />
                    <button type="button" class="edvora-ai-send-btn" id="edvoraAiSend" aria-label="Send">
                        <i class="bi bi-send-fill edvora-ai-send-icon"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    'use strict';

    function edvoraAiInit() {
        var triggerBtn      = document.getElementById('edvoraAiTrigger');
        var drawer          = document.getElementById('edvoraAiDrawer');
        var topicView       = document.getElementById('edvoraAiTopicView');
        var chatView        = document.getElementById('edvoraAiChatView');
        var backBtn         = document.getElementById('edvoraAiBackBtn');
        var backLabel       = document.getElementById('edvoraAiBackLabel');
        var closeBtns       = document.querySelectorAll('.edvora-ai-close-btn');
        var clearBtn        = document.getElementById('edvoraAiClear');
        var chatBody        = document.getElementById('edvoraAiBody');
        var form            = document.getElementById('edvoraAiForm');
        var inputEl         = document.getElementById('edvoraAiInput');
        var sendBtn         = document.getElementById('edvoraAiSend');
        var quickForm       = document.getElementById('edvoraAiQuickStartForm');
        var quickInput      = document.getElementById('edvoraAiQuickInput');
        var activePill      = document.getElementById('edvoraAiActivePill');
        var activePillName  = document.getElementById('edvoraAiActivePillName');
        var suggestionsEl   = document.getElementById('edvoraAiSuggestions');
        var heroTitle       = document.getElementById('edvoraAiHeroTitle');
        var heroSubtitle    = document.getElementById('edvoraAiHeroSubtitle');
        var badgeText       = document.getElementById('edvoraAiBadgeText');
        var gridHeading     = document.getElementById('edvoraAiGridHeading');
        var triggerText     = document.getElementById('edvoraAiTriggerText');

        var isGuest         = drawer.getAttribute('data-is-guest') === '1';
        var guestUsed       = parseInt(drawer.getAttribute('data-guest-used') || '0', 10);
        var guestRemaining  = parseInt(drawer.getAttribute('data-guest-remaining') || '5', 10);
        var loginUrl        = drawer.getAttribute('data-login-url') || '/login';
        var registerUrl     = drawer.getAttribute('data-register-url') || '/register';
        var guestBannerText   = document.getElementById('edvoraAiTopicBannerText');
        var guestBannerCount  = document.getElementById('edvoraAiTopicBannerCount');
        var headerLimitPill   = document.getElementById('edvoraAiHeaderLimit');
        var headerLimitText   = document.getElementById('edvoraAiHeaderLimitText');
        var meterBar          = document.getElementById('edvoraAiMeterBar');
        var meterLabel        = document.getElementById('edvoraAiMeterLabel');
        var meterNumsText     = document.getElementById('edvoraAiMeterNumsText');
        var meterFill         = document.getElementById('edvoraAiMeterFill');

        if (!triggerBtn || !drawer) return;

        var chatHistory  = [];
        var currentTopic = 'all';
        var currentLang  = localStorage.getItem('edvora_ai_lang') || 'en';

        var topicMeta = {
            all:      { icon: 'bi-chat-dots-fill',   nameEn: 'General Inquiry',    nameFa: 'راهنمای عمومی' },
            courses:  { icon: 'bi-mortarboard-fill', nameEn: 'Courses',            nameFa: 'دوره‌های آموزشی' },
            teachers: { icon: 'bi-people-fill',      nameEn: 'Instructors',        nameFa: 'اساتید و مربیان' },
            classes:  { icon: 'bi-broadcast',        nameEn: 'Live Classes',       nameFa: 'کلاس‌های زنده' },
            events:   { icon: 'bi-calendar-event-fill', nameEn: 'Events',          nameFa: 'رویدادها و وبینارها' },
            books:    { icon: 'bi-book-fill',        nameEn: 'Free Books',         nameFa: 'کتاب‌های رایگان' }
        };

        var translations = {
            en: {
                triggerText: 'Edvora AI Assistant',
                appTitle: 'Edvora AI Assistant',
                liveStatus: 'Live Database Access',
                badge: 'Edvora AI Live Assistant',
                heroTitle: 'Hello {{ auth()->check() ? auth()->user()->name : 'there' }}! 👋',
                heroSubtitle: 'What can we help you with today? Choose a category below for focused, accurate answers from our live database:',
                gridHeading: 'SELECT A CATEGORY TO START',
                backLabel: 'Topics',
                quickPlaceholder: 'Or type a question directly...',
                startChatBtn: 'Ask AI',
                clearNotice: 'Conversation cleared 😊 Which topic would you like to explore next?',
                errorNotice: 'An error occurred, please try again.',
                connError: 'Could not connect to the server. Please check your internet connection.',
                sourceLabel: 'Source: ',
                guestBanner: 'Guest Access: <strong>{remaining}</strong> of 5 free questions left',
                guestBannerAsked: '{used}/5 asked',
                meterLabel: '<i class="bi bi-speedometer2"></i> Guest Free Limit:',
                meterNums: '<strong>{used}</strong>/5 asked ({remaining} left)',
                limitReachedTitle: 'Free Guest Limit Reached (5/5)',
                limitReachedDesc: 'You have used all 5 free guest questions. Sign in or create a free account to continue asking unlimited questions with our live database assistant.',
                loginBtn: 'Sign In',
                registerBtn: 'Create Free Account',
                inputLockedPlaceholder: '5-question guest limit reached. Please sign in.',
                placeholders: {
                    all: 'Ask anything about Edvora Tech...',
                    courses: 'Ask about courses, curriculum, levels, certificates...',
                    teachers: 'Ask about instructors, mentors and qualifications...',
                    classes: 'Ask about ongoing live sessions and class rooms...',
                    events: 'Ask about upcoming workshops and webinars...',
                    books: 'Ask about free downloadable programming books...'
                },
                welcomeMessages: {
                    all: '<p>Hello! 👋 I am the <strong>Edvora Tech Smart Assistant</strong>. I have direct access to the live platform database. How can I assist you today?</p>',
                    courses: '<p>Hello! 👋 I\'m ready to answer any questions about our <strong>Courses & Curriculum</strong>. Ask me about course contents, requirements, mentors, or certificates!</p>',
                    teachers: '<p>Hello! 👋 Interested in learning from our <strong>Instructors & Mentors</strong>? Ask me about our teachers, their bios, and the subjects they teach.</p>',
                    classes: '<p>Hello! 👋 Need updates on <strong>Live Online Classes</strong>? Ask me about active sessions, class timings, and how to join!</p>',
                    events: '<p>Hello! 👋 Looking for <strong>Events & Webinars</strong>? Ask me about upcoming workshops, dates, speakers, and registration details.</p>',
                    books: '<p>Hello! 👋 Welcome to our <strong>Free Digital Library</strong>. Ask me what programming and skill books are available for free download!</p>'
                },
                suggestions: {
                    all: [
                        '📚 How many courses are there on Edvora?',
                        '👨‍🏫 Who are the top instructors?',
                        '🎉 What events are coming up?'
                    ],
                    courses: [
                        '🔒 Tell me about Cyber Security courses.',
                        '💻 What web development courses exist?',
                        '🎓 Which courses offer verified certificates?'
                    ],
                    teachers: [
                        '👨‍🏫 List all teachers and what they teach.',
                        '🌟 Who teaches programming and coding?'
                    ],
                    classes: [
                        '🔴 Are there any active live classes right now?',
                        '⏰ When are the next live class sessions?'
                    ],
                    events: [
                        '📅 When is the next live webinar or workshop?',
                        '🎉 Are all workshops free to join?'
                    ],
                    books: [
                        '📖 What programming books can I download for free?',
                        '📥 How do I download books from the library?'
                    ]
                }
            },
            fa: {
                triggerText: 'دستیار هوشمند ادورا',
                appTitle: 'دستیار هوشمند ادورا',
                liveStatus: 'دسترسی مستقیم به دیتابیس',
                badge: 'دستیار هوشمند زنده ادورا',
                heroTitle: 'سلام {{ auth()->check() ? auth()->user()->name : 'کاربر گرامی' }}! 👋',
                heroSubtitle: 'امروز چطور می‌توانم کمک‌تان کنم؟ برای دریافت دقیق‌ترین پاسخ، لطفاً یکی از بخش‌های زیر را انتخاب کنید:',
                gridHeading: 'برای شروع، یک بخش را انتخاب کنید',
                backLabel: 'موضوعات',
                quickPlaceholder: 'یا سوال خود را مستقیماً بنویسید...',
                startChatBtn: 'ارسال سوال',
                clearNotice: 'گفت‌وگو پاک شد 😊 مایلید درباره چه موضوعی گفت‌وگو کنیم؟',
                errorNotice: 'خطایی رخ داد، لطفاً دوباره تلاش کنید.',
                connError: 'ارتباط با سرور برقرار نشد. لطفاً اینترنت خود را بررسی کنید.',
                sourceLabel: 'منبع: ',
                guestBanner: 'کاربر مهمان: <strong>{remaining}</strong> سوال از ۵ سوال رایگان باقی‌مانده',
                guestBannerAsked: '{used} از ۵ پرسیده شده',
                meterLabel: '<i class="bi bi-speedometer2"></i> سقف سوالات مهمان:',
                meterNums: '<strong>{used}</strong> از ۵ پرسیده شده ({remaining} باقی‌مانده)',
                limitReachedTitle: 'سقف ۵ سوال رایگان مهمان به پایان رسید',
                limitReachedDesc: 'شما هر ۵ سوال رایگان کاربر مهمان را استفاده کرده‌اید. برای ادامه گفت‌وگو و پرسش‌های نامحدود، لطفاً وارد حساب کاربری خود شوید یا رایگان ثبت‌نام کنید.',
                loginBtn: 'ورود به حساب کاربری',
                registerBtn: 'ثبت‌نام رایگان',
                inputLockedPlaceholder: 'سقف ۵ سوال مهمان تمام شد. لطفاً وارد شوید.',
                placeholders: {
                    all: 'سوال خود را درباره ادورا تک بنویسید...',
                    courses: 'سوال درباره دوره‌ها، سرفصل‌ها، سطوح و مدارک...',
                    teachers: 'سوال درباره اساتید، سوابق و تخصص‌ها...',
                    classes: 'سوال درباره کلاس‌های آنلاین و جلسات زنده...',
                    events: 'سوال درباره وبینارها، کارگاه‌ها و زمان برگزاری...',
                    books: 'سوال درباره کتاب‌ها و منابع آموزشی قابل دانلود...'
                },
                welcomeMessages: {
                    all: '<p>سلام! 👋 من <strong>دستیار هوشمند ادورا تک</strong> هستم. با دسترسی مستقیم به دیتابیس زنده سایت، آماده پاسخ به سوالات شما هستم. چطور می‌توانم کمک‌تان کنم؟</p>',
                    courses: '<p>سلام! 👋 در خدمت شما هستم برای هرگونه سوال درباره <strong>دوره‌های آموزشی و سرفصل‌ها</strong>. مایلید درباره کدام دوره یا مهارت بیشتر بدانید؟</p>',
                    teachers: '<p>سلام! 👋 درباره <strong>اساتید و مربیان ادورا تک</strong> سوالی دارید؟ می‌توانید نام استاد، سوابق یا دوره‌هایی که تدریس می‌کنند را از من بپرسید.</p>',
                    classes: '<p>سلام! 👋 درباره <strong>کلاس‌های زنده و جلسات آنلاین</strong> در خدمتم. می‌توانید وضعیت جلسات در حال برگزاری یا زمان‌بندی را جویا شوید.</p>',
                    events: '<p>سلام! 👋 درباره <strong>رویدادها و وبینارهای آینده</strong> در خدمتم. تاریخ، موضوع یا نحوه شرکت در رویدادها را بپرسید.</p>',
                    books: '<p>سلام! 👋 به <strong>کتابخانه دیجیتال رایگان ادورا</strong> خوش آمدید. می‌توانید کتاب‌های قابل دانلود در هر حوزه را جویا شوید!</p>'
                },
                suggestions: {
                    all: [
                        '📚 چند دوره در سایت وجود دارد؟',
                        '👨‍🏫 اساتید سایت چه کسانی هستند؟',
                        '🎉 وبینارهای بعدی چیست؟'
                    ],
                    courses: [
                        '🔒 دوره‌ها و کلاس‌های امنیت سایبری (Cyber Security) چیست؟',
                        '💻 چه دوره‌هایی برای طراحی و برنامه‌نویسی وب دارید؟',
                        '🎓 کدام دوره‌ها مدرک معتبر رایگان ارائه می‌دهند؟'
                    ],
                    teachers: [
                        '👨‍🏫 لیست اساتید فعال و حوزه تدریس آن‌ها را بگو.',
                        '🌟 اساتید برجسته برنامه‌نویسی چه کسانی هستند؟'
                    ],
                    classes: [
                        '🔴 آیا همین الان کلاس زنده فعالی در حال برگزاری است؟',
                        '⏰ نحوه شرکت در جلسات زنده چگونه است؟'
                    ],
                    events: [
                        '📅 تاریخ و موضوع وبینار بعدی کی هست؟',
                        '🎉 رویدادهای آموزشی پیش‌رو را لیست کن.'
                    ],
                    books: [
                        '📖 چه کتاب‌های رایگانی برای دانلود وجود دارد؟',
                        '📥 چطور می‌توانم کتاب‌ها را دانلود کنم؟'
                    ]
                }
            }
        };

        /* ── View Switching ───────────────────────────────────── */
        function showTopicView() {
            chatView.classList.remove('is-active');
            topicView.classList.add('is-active');
            if (quickInput) quickInput.value = '';
        }

        function showChatView(topic) {
            currentTopic = topic || 'all';
            topicView.classList.remove('is-active');
            chatView.classList.add('is-active');

            updateChatHeaderPill();
            updatePlaceholder();
            renderSuggestions();

            // If empty conversation, insert topic-specific welcome message
            if (chatHistory.length === 0) {
                var t = translations[currentLang];
                var welcomeHtml = (t.welcomeMessages && t.welcomeMessages[currentTopic]) 
                    ? t.welcomeMessages[currentTopic] 
                    : t.welcomeMessages.all;

                chatBody.innerHTML =
                    '<div class="edvora-ai-msg edvora-ai-msg--bot">' +
                    '<div class="edvora-ai-msg__avatar"><i class="bi bi-robot"></i></div>' +
                    '<div class="edvora-ai-msg__content">' + welcomeHtml + '</div>' +
                    '</div>';
            }

            setTimeout(function () {
                if (inputEl) inputEl.focus();
            }, 100);
        }

        function updateChatHeaderPill() {
            if (!activePill || !activePillName) return;
            var meta = topicMeta[currentTopic] || topicMeta.all;
            var name = currentLang === 'fa' ? meta.nameFa : meta.nameEn;
            activePill.querySelector('i').className = 'bi ' + meta.icon;
            activePillName.textContent = name;
        }

        /* ── Set Language ─────────────────────────────────────── */
        function applyLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('edvora_ai_lang', lang);

            drawer.setAttribute('dir', lang === 'fa' ? 'rtl' : 'ltr');

            // Sync all language toggle buttons across views
            document.querySelectorAll('[data-lang-switch]').forEach(function (btn) {
                if (btn.getAttribute('data-lang-switch') === lang) {
                    btn.classList.add('is-active');
                } else {
                    btn.classList.remove('is-active');
                }
            });

            var t = translations[lang];

            if (triggerText) triggerText.textContent = t.triggerText;
            if (heroTitle) heroTitle.textContent = t.heroTitle;
            if (heroSubtitle) heroSubtitle.textContent = t.heroSubtitle;
            if (badgeText) badgeText.textContent = t.badge;
            if (gridHeading) gridHeading.textContent = t.gridHeading;
            if (backLabel) backLabel.textContent = t.backLabel;
            if (quickInput) quickInput.placeholder = t.quickPlaceholder;

            var quickBtnSpan = document.querySelector('#edvoraAiQuickStartBtn [data-i18n="start_chat_btn"]');
            if (quickBtnSpan) quickBtnSpan.textContent = t.startChatBtn;

            document.querySelectorAll('.edvora-ai-app-title').forEach(function (el) {
                el.innerHTML = t.appTitle + ' <i class="bi bi-patch-check-fill edvora-ai-header__verified-icon"></i>';
            });

            document.querySelectorAll('.edvora-ai-status-text').forEach(function (el) {
                el.textContent = t.liveStatus;
            });

            // Translate topic card titles and descriptions
            var cardTitles = {
                topic_courses_title: lang === 'fa' ? 'دوره‌های آموزشی' : 'Courses & Curriculum',
                topic_courses_desc:  lang === 'fa' ? 'سرفصل‌ها، سطوح، مدت زمان و مدارک' : 'Syllabus, skill levels, duration & certificates',
                topic_teachers_title: lang === 'fa' ? 'اساتید و مربیان' : 'Instructors & Mentors',
                topic_teachers_desc:  lang === 'fa' ? 'بیوگرافی، تخصص‌ها و دوره‌های اساتید' : 'Expert teacher bios, specialties & courses',
                topic_classes_title:  lang === 'fa' ? 'کلاس‌های زنده' : 'Live Classes',
                topic_classes_desc:   lang === 'fa' ? 'جلسات آنلاین فعال و زمان‌بندی' : 'Active interactive classes & schedule',
                topic_events_title:   lang === 'fa' ? 'رویدادها و وبینارها' : 'Events & Webinars',
                topic_events_desc:    lang === 'fa' ? 'کارگاه‌های آینده، تاریخ و نحوه ثبت‌نام' : 'Upcoming workshops, dates & registration',
                topic_books_title:    lang === 'fa' ? 'کتاب‌های رایگان' : 'Free Library Books',
                topic_books_desc:     lang === 'fa' ? 'کتاب‌های قابل دانلود برنامه‌نویسی و مهارت' : 'Downloadable programming & tech guides',
                topic_all_title:      lang === 'fa' ? 'راهنمای عمومی' : 'General Inquiry',
                topic_all_desc:       lang === 'fa' ? 'پرسش درباره پلتفرم و کلیات ادورا' : 'Ask anything about the Edvora Tech platform'
            };

            document.querySelectorAll('[data-i18n]').forEach(function (el) {
                var key = el.getAttribute('data-i18n');
                if (cardTitles[key]) {
                    el.textContent = cardTitles[key];
                }
            });

            updateChatHeaderPill();
            updatePlaceholder();
            renderSuggestions();

            if (isGuest) {
                updateGuestMeter(guestUsed, guestRemaining);
                var existingCard = document.getElementById('edvoraAiLimitCard');
                if (existingCard) {
                    var titleEl = existingCard.querySelector('.edvora-ai-limit-card__title');
                    var descEl  = existingCard.querySelector('.edvora-ai-limit-card__desc');
                    var primarySpan = existingCard.querySelector('.edvora-ai-auth-btn--primary span');
                    var secondarySpan = existingCard.querySelector('.edvora-ai-auth-btn--secondary span');
                    if (titleEl) titleEl.textContent = t.limitReachedTitle;
                    if (descEl) descEl.textContent = t.limitReachedDesc;
                    if (primarySpan) primarySpan.textContent = t.loginBtn;
                    if (secondarySpan) secondarySpan.textContent = t.registerBtn;
                }
                if (guestRemaining <= 0) {
                    if (inputEl) inputEl.placeholder = t.inputLockedPlaceholder;
                    if (quickInput) quickInput.placeholder = t.inputLockedPlaceholder;
                }
            }
        }

        /* ── Guest Question Limit Helpers ─────────────────────── */
        function updateGuestMeter(used, remaining) {
            if (!isGuest) return;
            guestUsed = used;
            guestRemaining = remaining;
            drawer.setAttribute('data-guest-used', used);
            drawer.setAttribute('data-guest-remaining', remaining);

            var t = translations[currentLang];
            if (guestBannerText) {
                guestBannerText.innerHTML = t.guestBanner.replace('{remaining}', remaining);
            }
            if (guestBannerCount) {
                guestBannerCount.textContent = t.guestBannerAsked.replace('{used}', used);
            }
            if (headerLimitText) {
                headerLimitText.textContent = remaining + '/5';
            }
            if (headerLimitPill) {
                if (remaining <= 0) {
                    headerLimitPill.classList.add('is-limit');
                } else {
                    headerLimitPill.classList.remove('is-limit');
                }
            }
            if (meterLabel) {
                meterLabel.innerHTML = t.meterLabel;
            }
            if (meterNumsText) {
                meterNumsText.innerHTML = t.meterNums.replace('{used}', used).replace('{remaining}', remaining);
            }
            if (meterFill) {
                var pct = Math.min(100, Math.max(0, (used / 5) * 100));
                meterFill.style.width = pct + '%';
                meterFill.classList.remove('is-warning', 'is-danger');
                if (remaining <= 1) {
                    meterFill.classList.add('is-danger');
                } else if (remaining <= 2) {
                    meterFill.classList.add('is-warning');
                }
            }
        }

        function lockChatDueToLimit(customMessage) {
            if (!isGuest) return;
            var t = translations[currentLang];

            if (inputEl) {
                inputEl.disabled = true;
                inputEl.placeholder = t.inputLockedPlaceholder;
            }
            if (sendBtn) {
                sendBtn.disabled = true;
            }
            if (form) {
                form.classList.add('is-locked');
            }
            if (quickInput) {
                quickInput.disabled = true;
                quickInput.placeholder = t.inputLockedPlaceholder;
            }
            var quickBtn = document.getElementById('edvoraAiQuickStartBtn');
            if (quickBtn) {
                quickBtn.disabled = true;
            }

            if (!document.getElementById('edvoraAiLimitCard')) {
                var card = document.createElement('div');
                card.className = 'edvora-ai-limit-card';
                card.id = 'edvoraAiLimitCard';
                card.innerHTML =
                    '<div class="edvora-ai-limit-card__icon"><i class="bi bi-lock-fill"></i></div>' +
                    '<h4 class="edvora-ai-limit-card__title">' + escHtml(t.limitReachedTitle) + '</h4>' +
                    '<p class="edvora-ai-limit-card__desc">' + escHtml(customMessage || t.limitReachedDesc) + '</p>' +
                    '<div class="edvora-ai-limit-card__actions">' +
                        '<a href="' + escHtml(loginUrl) + '" class="edvora-ai-auth-btn edvora-ai-auth-btn--primary">' +
                            '<i class="bi bi-box-arrow-in-right"></i> <span>' + escHtml(t.loginBtn) + '</span>' +
                        '</a>' +
                        '<a href="' + escHtml(registerUrl) + '" class="edvora-ai-auth-btn edvora-ai-auth-btn--secondary">' +
                            '<i class="bi bi-person-plus-fill"></i> <span>' + escHtml(t.registerBtn) + '</span>' +
                        '</a>' +
                    '</div>';
                chatBody.appendChild(card);
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        }

        function updatePlaceholder() {
            if (!inputEl) return;
            var t = translations[currentLang];
            var ph = (t.placeholders && t.placeholders[currentTopic]) 
                ? t.placeholders[currentTopic] 
                : t.placeholders.all;
            inputEl.placeholder = ph;
        }

        function renderSuggestions() {
            if (!suggestionsEl) return;
            var t = translations[currentLang];
            var list = (t.suggestions && t.suggestions[currentTopic]) 
                ? t.suggestions[currentTopic] 
                : t.suggestions.all;

            suggestionsEl.innerHTML = '';
            list.forEach(function (prompt) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'edvora-ai-chip';
                btn.textContent = prompt;
                btn.addEventListener('click', function () {
                    sendMessage(prompt);
                });
                suggestionsEl.appendChild(btn);
            });
        }

        /* ── Event Listeners ──────────────────────────────────── */
        // Topic Cards click -> Go directly to chat view
        document.querySelectorAll('[data-select-topic]').forEach(function (card) {
            card.addEventListener('click', function () {
                var topic = this.getAttribute('data-select-topic');
                showChatView(topic);
            });
        });

        // Quick Ask form on topic screen
        if (quickForm) {
            quickForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var text = quickInput ? quickInput.value.trim() : '';
                if (text) {
                    showChatView('all');
                    sendMessage(text);
                }
            });
        }

        // Back button on chat view -> Return to topic selection
        if (backBtn) {
            backBtn.addEventListener('click', function () {
                showTopicView();
            });
        }

        // Language switcher buttons
        document.querySelectorAll('[data-lang-switch]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var lang = this.getAttribute('data-lang-switch');
                applyLanguage(lang);
            });
        });

        // Open Drawer
        function openDrawer() {
            drawer.classList.add('is-active');
            drawer.setAttribute('aria-hidden', 'false');
            if (chatView.classList.contains('is-active')) {
                if (inputEl) inputEl.focus();
            } else {
                if (quickInput) quickInput.focus();
            }
        }

        // Close Drawer
        function closeDrawer() {
            drawer.classList.remove('is-active');
            drawer.setAttribute('aria-hidden', 'true');
        }

        triggerBtn.addEventListener('click', function () {
            drawer.classList.contains('is-active') ? closeDrawer() : openDrawer();
        });

        closeBtns.forEach(function (btn) {
            btn.addEventListener('click', closeDrawer);
        });

        // Clear Chat
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                chatHistory = [];
                var t = translations[currentLang];
                chatBody.innerHTML =
                    '<div class="edvora-ai-msg edvora-ai-msg--bot">' +
                    '<div class="edvora-ai-msg__avatar"><i class="bi bi-robot"></i></div>' +
                    '<div class="edvora-ai-msg__content"><p>' + t.clearNotice + '</p></div>' +
                    '</div>';
            });
        }

        // Send message from chat form
        if (sendBtn) {
            sendBtn.addEventListener('click', function () {
                var text = inputEl ? inputEl.value.trim() : '';
                if (text) sendMessage(text);
            });
        }

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var text = inputEl ? inputEl.value.trim() : '';
                if (text) sendMessage(text);
            });
        }

        if (inputEl) {
            inputEl.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    var text = this.value.trim();
                    if (text) sendMessage(text);
                }
            });
        }

        /* ── Helpers ─────────────────────────────────────────── */
        function escHtml(str) {
            return String(str)
                .replace(/&/g,'&amp;')
                .replace(/</g,'&lt;')
                .replace(/>/g,'&gt;')
                .replace(/"/g,'&quot;');
        }

        function parseMarkdown(text) {
            if (!text) return '';
            var html = escHtml(text)
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`([^`]+)`/g, '<code>$1</code>');
            html = html.replace(/^\s*[-*]\s+(.+)$/gm, '<li>$1</li>');
            if (html.indexOf('<li>') !== -1) {
                html = html.replace(/(<li>.*<\/li>(\n|$))+/g, function(m){ return '<ul>' + m + '</ul>'; });
            }
            html = html.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>');
            return '<p>' + html + '</p>';
        }

        function appendMessage(role, text, sources, topicUsed) {
            sources = sources || [];
            var div = document.createElement('div');
            div.className = 'edvora-ai-msg edvora-ai-msg--' + (role === 'user' ? 'user' : 'bot');
            var icon = role === 'user' ? 'bi-person-fill' : 'bi-robot';
            var srcHtml = '';
            var topicHtml = '';
            var t = translations[currentLang];

            if (role === 'user' && topicUsed && topicUsed !== 'all') {
                var meta = topicMeta[topicUsed] || topicMeta.all;
                var topicName = currentLang === 'fa' ? meta.nameFa : meta.nameEn;
                topicHtml = '<div class="edvora-ai-msg__topic-tag"><i class="bi ' + meta.icon + '"></i> ' + escHtml(topicName) + '</div>';
            }

            if (sources.length) {
                srcHtml = '<div class="edvora-ai-msg__sources"><i class="bi bi-database-check"></i> ' + t.sourceLabel +
                    sources.map(function (s) { return '<span class="edvora-ai-source-tag">' + escHtml(s) + '</span>'; }).join(' ') +
                    '</div>';
            }

            div.innerHTML =
                '<div class="edvora-ai-msg__avatar"><i class="bi ' + icon + '"></i></div>' +
                '<div class="edvora-ai-msg__content">' +
                    topicHtml +
                    (role === 'user' ? '<p>' + escHtml(text) + '</p>' : parseMarkdown(text)) +
                    srcHtml +
                '</div>';
            chatBody.appendChild(div);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function showTyping() {
            var d = document.createElement('div');
            d.className = 'edvora-ai-msg edvora-ai-msg--bot';
            d.id = 'edvoraAiTyping';
            d.innerHTML =
                '<div class="edvora-ai-msg__avatar"><i class="bi bi-robot"></i></div>' +
                '<div class="edvora-ai-msg__content">' +
                '<div class="edvora-ai-typing">' +
                '<div class="edvora-ai-typing__dot"></div>' +
                '<div class="edvora-ai-typing__dot"></div>' +
                '<div class="edvora-ai-typing__dot"></div>' +
                '</div></div>';
            chatBody.appendChild(d);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function hideTyping() {
            var el = document.getElementById('edvoraAiTyping');
            if (el) el.parentNode.removeChild(el);
        }

        /* ── Send to API ─────────────────────────────────────── */
        function sendMessage(userText, specifiedTopic) {
            if (isGuest && guestRemaining <= 0) {
                if (!drawer.classList.contains('is-active')) openDrawer();
                if (!chatView.classList.contains('is-active')) showChatView('all');
                lockChatDueToLimit();
                return;
            }

            var topicToSend = specifiedTopic || currentTopic;
            if (inputEl) inputEl.value = '';

            // Ensure we are on chat view and drawer is open
            if (!drawer.classList.contains('is-active')) openDrawer();
            if (!chatView.classList.contains('is-active')) showChatView(topicToSend);

            appendMessage('user', userText, [], topicToSend);
            chatHistory.push({ role: 'user', text: userText });
            if (sendBtn) sendBtn.disabled = true;
            showTyping();

            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch('/ai-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    message: userText,
                    topic: topicToSend,
                    language: currentLang,
                    history: chatHistory.slice(-6)
                })
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { status: res.status, data: data };
                }).catch(function () {
                    return { status: res.status, data: null };
                });
            })
            .then(function (result) {
                hideTyping();
                var data = result.data;
                var status = result.status;

                if (status === 429 || (data && data.limit_reached)) {
                    var used = (data && data.used !== undefined) ? data.used : 5;
                    var rem = (data && data.remaining !== undefined) ? data.remaining : 0;
                    updateGuestMeter(used, rem);
                    lockChatDueToLimit(data && data.message);
                    return;
                }

                if (data && data.success) {
                    appendMessage('bot', data.message, data.sources || []);
                    chatHistory.push({ role: 'model', text: data.message });

                    if (isGuest && data.used !== undefined && data.remaining !== undefined) {
                        updateGuestMeter(data.used, data.remaining);
                        if (data.remaining <= 0 || data.limit_reached) {
                            lockChatDueToLimit();
                        }
                    }
                } else {
                    var t = translations[currentLang];
                    appendMessage('bot', (data && data.message) || t.errorNotice);
                }
            })
            .catch(function (err) {
                hideTyping();
                var t = translations[currentLang];
                appendMessage('bot', t.connError);
                console.error('[EdvoraAI]', err);
            })
            .finally(function () {
                if (sendBtn && (!isGuest || guestRemaining > 0)) {
                    sendBtn.disabled = false;
                }
                if (inputEl && (!isGuest || guestRemaining > 0)) {
                    inputEl.focus();
                }
            });
        }

        /* expose globally for home buttons */
        window.edvoraAiOpenWithTopic = function (topic) {
            openDrawer();
            showChatView(topic);
        };

        window.edvoraAiSend = function (prompt, topic) {
            openDrawer();
            showChatView(topic);
            sendMessage(prompt, topic);
        };

        // Initialize with default or saved language
        applyLanguage(currentLang);
        if (isGuest) {
            updateGuestMeter(guestUsed, guestRemaining);
            if (guestRemaining <= 0) {
                lockChatDueToLimit();
            }
        }
        showTopicView();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', edvoraAiInit);
    } else {
        edvoraAiInit();
    }
})();
</script>
