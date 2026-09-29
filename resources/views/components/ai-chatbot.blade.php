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

{{-- Self-Contained Styles: Immune to Browser / Server Cache Issues --}}
<style>
/* Trigger Button */
#edvoraAiTrigger {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    left: auto !important;
    top: auto !important;
    z-index: 99999 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding: 10px 18px !important;
    border-radius: 999px !important;
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12), 0 0 15px rgba(31, 143, 255, 0.1) !important;
    cursor: pointer !important;
    font-family: 'Poppins', 'Vazirmatn', -apple-system, sans-serif !important;
    font-size: 0.88rem !important;
    font-weight: 600 !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    user-select: none !important;
    text-decoration: none !important;
    margin: 0 !important;
}
#edvoraAiTrigger:hover {
    transform: translateY(-2px) !important;
    background: #f8fafc !important;
    border-color: #1f8fff !important;
    color: #1f8fff !important;
    box-shadow: 0 12px 30px rgba(31, 143, 255, 0.2) !important;
}
#edvoraAiTrigger .trigger-icon {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1.15rem !important;
    color: #1f8fff !important;
    position: relative !important;
}
#edvoraAiTrigger .trigger-dot {
    position: absolute !important;
    top: -1px !important;
    right: -2px !important;
    width: 7px !important;
    height: 7px !important;
    border-radius: 50% !important;
    background: #10b981 !important;
    border: 1.5px solid #ffffff !important;
}

/* Chat Window Popup */
#edvoraAiWindow {
    position: fixed !important;
    bottom: 84px !important;
    right: 24px !important;
    left: auto !important;
    top: auto !important;
    width: 380px !important;
    max-width: calc(100vw - 32px) !important;
    height: 540px !important;
    max-height: calc(100vh - 110px) !important;
    z-index: 999999 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.16), 0 0 25px rgba(31, 143, 255, 0.08) !important;
    display: none;
    flex-direction: column !important;
    overflow: hidden !important;
    opacity: 0;
    visibility: hidden;
    transform: translateY(12px) scale(0.97);
    transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease !important;
    direction: ltr !important;
    font-family: 'Poppins', 'Vazirmatn', -apple-system, sans-serif !important;
    color: #0f172a !important;
    box-sizing: border-box !important;
    margin: 0 !important;
    padding: 0 !important;
}
#edvoraAiWindow.is-open {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) scale(1) !important;
}

/* Window Header */
#edvoraAiWindow .edvora-ai-header {
    padding: 14px 18px !important;
    background: #ffffff !important;
    border-bottom: 1px solid #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-shrink: 0 !important;
}
#edvoraAiWindow .brand-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
#edvoraAiWindow .avatar-icon {
    width: 34px !important;
    height: 34px !important;
    border-radius: 10px !important;
    background: linear-gradient(135deg, #1f8fff 0%, #0070e0 100%) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #ffffff !important;
    font-size: 1.1rem !important;
    box-shadow: 0 4px 10px rgba(31, 143, 255, 0.25) !important;
    flex-shrink: 0 !important;
}
#edvoraAiWindow .title-box h4 {
    font-size: 0.95rem !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    margin: 0 !important;
    line-height: 1.2 !important;
}
#edvoraAiWindow .title-box div {
    font-size: 0.72rem !important;
    color: #64748b !important;
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-top: 2px !important;
}
#edvoraAiWindow .status-dot {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: #10b981 !important;
    display: inline-block !important;
}
#edvoraAiWindow .header-controls {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}
#edvoraAiWindow .control-btn {
    background: transparent !important;
    border: none !important;
    color: #64748b !important;
    font-size: 0.95rem !important;
    width: 28px !important;
    height: 28px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    padding: 0 !important;
}
#edvoraAiWindow .control-btn:hover {
    color: #0f172a !important;
    background: #f1f5f9 !important;
}

/* Guest Bar */
#edvoraAiWindow .guest-bar {
    padding: 6px 18px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    font-size: 0.75rem !important;
    color: #64748b !important;
}
#edvoraAiWindow .guest-bar a {
    color: #1f8fff !important;
    text-decoration: none !important;
    font-weight: 600 !important;
}

/* Messages Area */
#edvoraAiWindow .messages-area {
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 16px !important;
    background: #f8fafc !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
    scroll-behavior: smooth !important;
    box-sizing: border-box !important;
}
#edvoraAiWindow .messages-area::-webkit-scrollbar {
    width: 5px !important;
}
#edvoraAiWindow .messages-area::-webkit-scrollbar-thumb {
    background: #cbd5e1 !important;
    border-radius: 4px !important;
}

/* Message Rows */
#edvoraAiWindow .msg-row {
    display: flex !important;
    gap: 8px !important;
    max-width: 90% !important;
    animation: msgFadeIn 0.2s ease forwards !important;
}
@keyframes msgFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* USER Bubble: Blue with PURE WHITE TEXT */
#edvoraAiWindow .msg-row.is-user {
    align-self: flex-end !important;
    flex-direction: row-reverse !important;
}
#edvoraAiWindow .msg-row.is-user .bubble {
    background: linear-gradient(135deg, #1f8fff 0%, #0066cc 100%) !important;
    color: #ffffff !important;
    border-radius: 16px 16px 4px 16px !important;
    padding: 10px 14px !important;
    font-size: 0.88rem !important;
    line-height: 1.5 !important;
    box-shadow: 0 4px 12px rgba(31, 143, 255, 0.25) !important;
    word-break: break-word !important;
}
#edvoraAiWindow .msg-row.is-user .bubble p,
#edvoraAiWindow .msg-row.is-user .bubble span {
    color: #ffffff !important;
    margin: 0 !important;
}

/* BOT Bubble: Dark Slate with PURE WHITE TEXT */
#edvoraAiWindow .msg-row.is-bot {
    align-self: flex-start !important;
}
#edvoraAiWindow .bot-avatar-tiny {
    width: 26px !important;
    height: 26px !important;
    border-radius: 8px !important;
    background: #0f172a !important;
    color: #00f0ff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 0.82rem !important;
    flex-shrink: 0 !important;
    margin-top: 2px !important;
}
#edvoraAiWindow .msg-row.is-bot .bubble {
    background: #0f172a !important;
    color: #ffffff !important;
    border-radius: 16px 16px 16px 4px !important;
    padding: 11px 14px !important;
    font-size: 0.88rem !important;
    line-height: 1.6 !important;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15) !important;
    word-break: break-word !important;
}
#edvoraAiWindow .msg-row.is-bot .bubble p,
#edvoraAiWindow .msg-row.is-bot .bubble span,
#edvoraAiWindow .msg-row.is-bot .bubble li {
    color: #ffffff !important;
}
#edvoraAiWindow .msg-row.is-bot .bubble strong {
    color: #00f0ff !important;
    font-weight: 600 !important;
}

/* Clickable Links inside Bot Bubble */
#edvoraAiWindow a.bot-link {
    color: #00f0ff !important;
    font-weight: 600 !important;
    text-decoration: underline !important;
    text-underline-offset: 2px !important;
    transition: all 0.2s ease !important;
}
#edvoraAiWindow a.bot-link:hover {
    color: #ffffff !important;
    text-decoration: none !important;
    text-shadow: 0 0 8px rgba(0, 240, 255, 0.6) !important;
}

/* Bi-directional Text Support */
#edvoraAiWindow .bubble[dir="auto"],
#edvoraAiWindow .bubble {
    unicode-bidi: plaintext !important;
    text-align: start !important;
}
#edvoraAiWindow .bubble p {
    margin: 0 0 8px 0 !important;
}
#edvoraAiWindow .bubble p:last-child {
    margin-bottom: 0 !important;
}
#edvoraAiWindow .bubble ul {
    margin: 6px 0 !important;
    padding-left: 18px !important;
}
#edvoraAiWindow .bubble[dir="rtl"] ul {
    padding-left: 0 !important;
    padding-right: 18px !important;
}

/* Suggestion Chips */
#edvoraAiWindow .chips-container {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 6px !important;
    margin-top: 4px !important;
}
#edvoraAiWindow .suggestion-chip {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 12px !important;
    padding: 6px 11px !important;
    color: #334155 !important;
    font-size: 0.76rem !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    user-select: none !important;
    text-align: left !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
}
#edvoraAiWindow .suggestion-chip:hover {
    background: #1f8fff !important;
    border-color: #1f8fff !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 3px 8px rgba(31, 143, 255, 0.25) !important;
}

/* Typing Dots */
#edvoraAiWindow .typing-box {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    padding: 4px 6px !important;
}
#edvoraAiWindow .typing-box span {
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
    background: #00f0ff !important;
    opacity: 0.6 !important;
    animation: edvoraDot 1.2s infinite ease-in-out both !important;
}
#edvoraAiWindow .typing-box span:nth-child(1) { animation-delay: -0.32s; }
#edvoraAiWindow .typing-box span:nth-child(2) { animation-delay: -0.16s; }
@keyframes edvoraDot {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

/* Footer / Input */
#edvoraAiWindow .footer-bar {
    padding: 12px 16px !important;
    background: #ffffff !important;
    border-top: 1px solid #f1f5f9 !important;
    flex-shrink: 0 !important;
}
#edvoraAiWindow .input-capsule {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 14px !important;
    padding: 4px 6px 4px 14px !important;
    transition: all 0.2s ease !important;
    margin: 0 !important;
}
#edvoraAiWindow .input-capsule:focus-within {
    border-color: #1f8fff !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(31, 143, 255, 0.12) !important;
}
#edvoraAiWindow .text-input {
    flex: 1 !important;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    color: #0f172a !important;
    font-size: 0.88rem !important;
    font-family: inherit !important;
    padding: 8px 0 !important;
}
#edvoraAiWindow .text-input::placeholder {
    color: #94a3b8 !important;
}
#edvoraAiWindow .send-action-btn {
    width: 34px !important;
    height: 34px !important;
    border-radius: 10px !important;
    background: #1f8fff !important;
    border: none !important;
    color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1.15rem !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    flex-shrink: 0 !important;
    padding: 0 !important;
}
#edvoraAiWindow .send-action-btn:hover:not(:disabled) {
    background: #0070e0 !important;
    transform: scale(1.05) !important;
}
#edvoraAiWindow .send-action-btn:disabled {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
}

#edvoraAiWindow .voice-action-btn {
    width: 34px !important;
    height: 34px !important;
    border-radius: 10px !important;
    background: rgba(31, 143, 255, 0.1) !important;
    border: 1px solid rgba(31, 143, 255, 0.25) !important;
    color: #1f8fff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1rem !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    flex-shrink: 0 !important;
    padding: 0 !important;
}
#edvoraAiWindow .voice-action-btn:hover {
    background: rgba(31, 143, 255, 0.2) !important;
}
#edvoraAiWindow .voice-action-btn.is-recording {
    background: #ef4444 !important;
    color: #ffffff !important;
    border-color: #ef4444 !important;
    animation: edvoraVoicePulse 1.2s infinite ease-in-out !important;
}
@keyframes edvoraVoicePulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.85; }
}

/* Mobile & Tablet Responsive */
@media (max-width: 991.98px) {
    #edvoraAiTrigger {
        bottom: calc(84px + env(safe-area-inset-bottom, 0px)) !important;
        right: 16px !important;
        padding: 9px 14px !important;
        font-size: 0.82rem !important;
    }
    body.sidebar-open #edvoraAiTrigger {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
}
@media (max-width: 480px) {
    #edvoraAiWindow {
        bottom: 0 !important;
        right: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        height: 85vh !important;
        max-height: 85vh !important;
        border-radius: 20px 20px 0 0 !important;
        border-bottom: none !important;
    }
}
</style>

{{-- Minimal Floating Trigger Button --}}
<button type="button" id="edvoraAiTrigger" aria-label="Open AI Assistant">
    <span class="trigger-icon">
        <i class="bi bi-stars"></i>
        <span class="trigger-dot"></span>
    </span>
    <span>Ask AI</span>
</button>

{{-- Minimal Chat Window (Light Theme, Compact Floating Popup) --}}
<div id="edvoraAiWindow" style="display: none !important;" aria-hidden="true"
     data-is-guest="{{ $isGuest ? '1' : '0' }}"
     data-guest-used="{{ $guestUsed }}"
     data-guest-remaining="{{ $guestRemaining }}"
     data-login-url="{{ route('login') }}"
     data-register-url="{{ route('register') }}">

    {{-- Clean Light Header --}}
    <div class="edvora-ai-header">
        <div class="brand-wrap">
            <div class="avatar-icon">
                <i class="bi bi-robot"></i>
            </div>
            <div class="title-box">
                <h4>Edvora AI</h4>
                <div>
                    <span class="status-dot"></span>
                    <span>Assistant • پشتیبان آنلاین</span>
                </div>
            </div>
        </div>

        <div class="header-controls">
            <button type="button" class="control-btn" id="edvoraAiClearBtn" title="Clear chat">
                <i class="bi bi-trash3"></i>
            </button>
            <button type="button" class="control-btn" id="edvoraAiCloseBtn" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    {{-- Guest Status Bar --}}
    @if($isGuest)
    <div class="guest-bar" id="edvoraAiGuestBar">
        <span id="edvoraAiGuestInfo">{{ $guestRemaining }} of 5 free questions remaining</span>
        <a href="{{ route('login') }}" id="edvoraAiGuestLogin">Log in</a>
    </div>
    @endif

    {{-- Messages Area --}}
    <div class="messages-area" id="edvoraAiMessages">
        {{-- Messages injected dynamically --}}
    </div>

    {{-- Footer Input --}}
    <div class="footer-bar">
        <form class="input-capsule" id="edvoraAiForm" onsubmit="return false;">
            <input type="text"
                   class="text-input"
                   id="edvoraAiInput"
                   placeholder="Type your question in English or فارسی..."
                   autocomplete="off"
                   dir="auto" />
            <button type="button" class="voice-action-btn" id="edvoraAiMicBtn" title="ورودی صوتی (ضبط ویس به فارسی)">
                <i class="bi bi-mic-fill"></i>
            </button>
            <button type="submit" class="send-action-btn" id="edvoraAiSendBtn" aria-label="Send">
                <i class="bi bi-arrow-up-short"></i>
            </button>
        </form>
    </div>
</div>

<script>
(function () {
    'use strict';

    function initEdvoraAi() {
        var triggerBtn = document.getElementById('edvoraAiTrigger');
        var windowEl   = document.getElementById('edvoraAiWindow');
        var closeBtn   = document.getElementById('edvoraAiCloseBtn');
        var clearBtn   = document.getElementById('edvoraAiClearBtn');
        var messagesEl = document.getElementById('edvoraAiMessages');
        var formEl     = document.getElementById('edvoraAiForm');
        var inputEl    = document.getElementById('edvoraAiInput');
        var sendBtn    = document.getElementById('edvoraAiSendBtn');
        var guestBar   = document.getElementById('edvoraAiGuestBar');
        var guestInfo  = document.getElementById('edvoraAiGuestInfo');

        if (!triggerBtn || !windowEl) return;

        var isGuest = windowEl.getAttribute('data-is-guest') === '1';
        var guestRemaining = parseInt(windowEl.getAttribute('data-guest-remaining') || '5', 10);
        var loginUrl = windowEl.getAttribute('data-login-url') || '/login';
        var registerUrl = windowEl.getAttribute('data-register-url') || '/register';

        var chatHistory = [];

        var defaultSuggestions = [
            'دوره‌های آموزشی موجود',
            'Available courses',
            'اساتید و مربیان ادورا',
            'کتاب‌ها و منابع رایگان'
        ];

        // Open Window
        function openWindow() {
            windowEl.style.setProperty('display', 'flex', 'important');
            requestAnimationFrame(function () {
                windowEl.classList.add('is-open');
            });
            windowEl.setAttribute('aria-hidden', 'false');
            if (inputEl) inputEl.focus();
            if (messagesEl.children.length === 0) {
                renderInitialWelcome();
            }
        }

        // Close Window
        function closeWindow() {
            windowEl.classList.remove('is-open');
            windowEl.setAttribute('aria-hidden', 'true');
            setTimeout(function () {
                if (!windowEl.classList.contains('is-open')) {
                    windowEl.style.setProperty('display', 'none', 'important');
                }
            }, 220);
        }

        triggerBtn.addEventListener('click', function () {
            windowEl.classList.contains('is-open') ? closeWindow() : openWindow();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', closeWindow);
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                chatHistory = [];
                messagesEl.innerHTML = '';
                renderInitialWelcome();
            });
        }

        // Render Initial Welcome + Suggestions
        function renderInitialWelcome() {
            messagesEl.innerHTML = '';
            
            var welcomeMsg = document.createElement('div');
            welcomeMsg.className = 'msg-row is-bot';
            welcomeMsg.innerHTML =
                '<div class="bot-avatar-tiny"><i class="bi bi-robot"></i></div>' +
                '<div class="bubble" dir="auto">' +
                '<p>سلام! من دستیار هوشمند ادورا هستم. هر سوالی در مورد دوره‌ها، اساتید یا امکانات سایت دارید به فارسی یا انگلیسی بپرسید.</p>' +
                '<p style="opacity: 0.85; font-size: 0.82rem; margin-top: 4px;">Hello! Ask anything about Edvora courses, instructors, or events.</p>' +
                '</div>';
            messagesEl.appendChild(welcomeMsg);

            var chipsWrap = document.createElement('div');
            chipsWrap.className = 'chips-container';
            defaultSuggestions.forEach(function (prompt) {
                var chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'suggestion-chip';
                chip.setAttribute('dir', 'auto');
                chip.textContent = prompt;
                chip.addEventListener('click', function () {
                    handleUserSend(prompt);
                });
                chipsWrap.appendChild(chip);
            });
            messagesEl.appendChild(chipsWrap);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function formatMarkdown(text) {
            if (!text) return '';
            var html = escHtml(text);
            
            // Markdown Links: [Title](url) -> Clickable Links
            html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, function (match, title, url) {
                var cleanUrl = url.trim();
                return '<a href="' + cleanUrl + '" class="bot-link" target="_blank" rel="noopener">' + title + ' <i class="bi bi-box-arrow-up-right" style="font-size: 0.72rem;"></i></a>';
            });

            // Bold, Italic, Code
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                       .replace(/\*(.*?)\*/g, '<em>$1</em>')
                       .replace(/`([^`]+)`/g, '<code>$1</code>');

            // Bullet lists
            html = html.replace(/^\s*[-*]\s+(.+)$/gm, '<li>$1</li>');
            if (html.indexOf('<li>') !== -1) {
                html = html.replace(/(<li>.*<\/li>(\n|$))+/g, function (m) { return '<ul>' + m + '</ul>'; });
            }

            html = html.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>');
            return '<p>' + html + '</p>';
        }

        function appendUserMessage(text) {
            var msg = document.createElement('div');
            msg.className = 'msg-row is-user';
            msg.innerHTML =
                '<div class="bubble" dir="auto">' +
                '<p>' + escHtml(text) + '</p>' +
                '</div>';
            messagesEl.appendChild(msg);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function appendBotMessage(text) {
            var msg = document.createElement('div');
            msg.className = 'msg-row is-bot';
            msg.innerHTML =
                '<div class="bot-avatar-tiny"><i class="bi bi-robot"></i></div>' +
                '<div class="bubble" dir="auto">' +
                formatMarkdown(text) +
                '</div>';
            messagesEl.appendChild(msg);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function showTypingIndicator() {
            var typing = document.createElement('div');
            typing.className = 'msg-row is-bot';
            typing.id = 'edvoraAiTyping';
            typing.innerHTML =
                '<div class="bot-avatar-tiny"><i class="bi bi-robot"></i></div>' +
                '<div class="bubble">' +
                '<div class="typing-box"><span></span><span></span><span></span></div>' +
                '</div>';
            messagesEl.appendChild(typing);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function hideTypingIndicator() {
            var el = document.getElementById('edvoraAiTyping');
            if (el && el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }

        function updateGuestBar() {
            if (!isGuest || !guestBar || !guestInfo) return;
            if (guestRemaining <= 0) {
                guestInfo.textContent = 'Free guest limit reached (5/5). Please log in.';
            } else {
                guestInfo.textContent = guestRemaining + ' of 5 free questions remaining';
            }
        }

        function showLimitModal(customMsg) {
            var limitCard = document.createElement('div');
            limitCard.className = 'msg-row is-bot';
            limitCard.innerHTML =
                '<div class="bot-avatar-tiny"><i class="bi bi-lock-fill" style="color: #f59e0b;"></i></div>' +
                '<div class="bubble" dir="auto" style="background: #1e293b; border: 1px solid rgba(245, 158, 11, 0.4);">' +
                '<strong style="color: #fbbf24; display: block; margin-bottom: 4px;"><i class="bi bi-shield-exclamation me-1"></i> سقف سوالات مهمان تمام شد (Guest Limit Reached)</strong>' +
                '<p style="font-size: 0.84rem; color: #cbd5e1; margin-bottom: 10px;">' + escHtml(customMsg || 'شما به سقف ۵ سوال رایگان کاربر مهمان رسیده‌اید. برای ادامه نامحدود لطفاً وارد حساب خود شوید.') + '</p>' +
                '<div style="display: flex; gap: 8px; flex-wrap: wrap;">' +
                    '<a href="' + escHtml(loginUrl) + '" style="background: #1f8fff; color: #fff; padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; text-decoration: none; font-weight: 600;">ورود / Log in</a>' +
                    '<a href="' + escHtml(registerUrl) + '" style="background: rgba(255,255,255,0.15); color: #fff; padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; text-decoration: none; font-weight: 600;">ثبت‌نام / Sign Up</a>' +
                '</div>' +
                '</div>';
            messagesEl.appendChild(limitCard);
            messagesEl.scrollTop = messagesEl.scrollHeight;

            if (inputEl) {
                inputEl.disabled = true;
                inputEl.placeholder = 'Limit reached. Please log in.';
            }
            if (sendBtn) sendBtn.disabled = true;
        }

        function handleUserSend(text) {
            if (!text || text.trim() === '') return;
            text = text.trim();

            if (isGuest && guestRemaining <= 0) {
                showLimitModal();
                return;
            }

            if (inputEl) inputEl.value = '';

            appendUserMessage(text);
            chatHistory.push({ role: 'user', text: text });
            showTypingIndicator();
            if (sendBtn) sendBtn.disabled = true;

            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

            var hasPersian = /[\u0600-\u06FF]/.test(text);
            var langToSend = hasPersian ? 'fa' : 'en';

            fetch('/ai-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    message: text,
                    topic: 'all',
                    language: langToSend,
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
                hideTypingIndicator();
                var data = result.data;
                var status = result.status;

                if (status === 429 || (data && data.limit_reached)) {
                    guestRemaining = 0;
                    updateGuestBar();
                    showLimitModal(data && data.message);
                    return;
                }

                if (data && data.success) {
                    var responseMsg = (data.message && data.message.trim()) 
                        ? data.message.trim() 
                        : (hasPersian ? 'اطلاعات درخواستی در سامانه ادورا ثبت شده است.' : 'Information is available on the Edvora platform.');
                    
                    appendBotMessage(responseMsg);
                    chatHistory.push({ role: 'model', text: responseMsg });

                    if (isGuest && data.remaining !== undefined) {
                        guestRemaining = data.remaining;
                        updateGuestBar();
                        if (guestRemaining <= 0) {
                            showLimitModal();
                        }
                    }
                } else {
                    var errorMsg = (data && data.message) ? data.message : (hasPersian ? 'متأسفانه در پردازش سوال خطایی رخ داد.' : 'Sorry, an error occurred while processing.');
                    appendBotMessage(errorMsg);
                }
            })
            .catch(function (err) {
                hideTypingIndicator();
                var connError = hasPersian 
                    ? 'خطا در برقراری ارتباط با سرور. لطفاً اتصال اینترنت خود را بررسی کنید.' 
                    : 'Connection error. Please check your internet connection.';
                appendBotMessage(connError);
                console.error('[EdvoraAI Error]', err);
            })
            .finally(function () {
                if (sendBtn && (!isGuest || guestRemaining > 0)) {
                    sendBtn.disabled = false;
                }
            });
        }

        if (formEl) {
            formEl.addEventListener('submit', function (e) {
                e.preventDefault();
                var text = inputEl ? inputEl.value : '';
                handleUserSend(text);
            });
        }

        if (inputEl) {
            inputEl.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    var text = this.value;
                    handleUserSend(text);
                }
            });
        }

        // Voice Input (Speech Recognition)
        var micBtn = document.getElementById('edvoraAiMicBtn');
        var isRecordingVoice = false;
        var speechInstance = null;

        if (micBtn && inputEl) {
            micBtn.addEventListener('click', function () {
                var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

                if (!SpeechRecognition) {
                    alert('مرورگر شما از ورودی صوتی پشتیبانی نمی‌کند. لطفاً از مرورگر جدیدتر مانند Chrome یا Edge استفاده فرمایید.');
                    return;
                }

                if (isRecordingVoice) {
                    if (speechInstance) speechInstance.stop();
                    isRecordingVoice = false;
                    micBtn.classList.remove('is-recording');
                    micBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                    return;
                }

                try {
                    var recognition = new SpeechRecognition();
                    recognition.lang = 'fa-IR';
                    recognition.continuous = true;
                    recognition.interimResults = true;

                    recognition.onstart = function () {
                        isRecordingVoice = true;
                        micBtn.classList.add('is-recording');
                        micBtn.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
                    };

                    recognition.onresult = function (e) {
                        var transcript = '';
                        for (var i = e.resultIndex; i < e.results.length; i++) {
                            transcript += e.results[i][0].transcript;
                        }
                        if (transcript) {
                            inputEl.value = transcript;
                        }
                    };

                    recognition.onerror = function (e) {
                        console.error('[Voice Input Error]', e);
                        isRecordingVoice = false;
                        micBtn.classList.remove('is-recording');
                        micBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                    };

                    recognition.onend = function () {
                        isRecordingVoice = false;
                        micBtn.classList.remove('is-recording');
                        micBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                    };

                    speechInstance = recognition;
                    recognition.start();
                } catch (err) {
                    console.error('[Voice Exception]', err);
                    isRecordingVoice = false;
                    micBtn.classList.remove('is-recording');
                    micBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEdvoraAi);
    } else {
        initEdvoraAi();
    }
})();
</script>
