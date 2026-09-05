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

{{-- Minimal Floating Trigger Button --}}
<button type="button" class="edvora-ai-trigger" id="edvoraAiTrigger" aria-label="Open AI Assistant">
    <span class="edvora-ai-trigger__icon">
        <i class="bi bi-stars"></i>
        <span class="edvora-ai-trigger__dot"></span>
    </span>
    <span id="edvoraAiTriggerText">Ask AI</span>
</button>

{{-- Minimal Chat Window (Light Theme, English/Persian Adaptive) --}}
<div class="edvora-ai-window" id="edvoraAiWindow" aria-hidden="true"
     data-is-guest="{{ $isGuest ? '1' : '0' }}"
     data-guest-used="{{ $guestUsed }}"
     data-guest-remaining="{{ $guestRemaining }}"
     data-login-url="{{ route('login') }}"
     data-register-url="{{ route('register') }}">

    {{-- Clean Light Header --}}
    <div class="edvora-ai-header">
        <div class="edvora-ai-brand">
            <div class="edvora-ai-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div class="edvora-ai-title-wrap">
                <h4 class="edvora-ai-title">Edvora AI</h4>
                <div class="edvora-ai-subtitle">
                    <span class="edvora-ai-status-dot"></span>
                    <span>Assistant • پشتیبان آنلاین</span>
                </div>
            </div>
        </div>

        <div class="edvora-ai-controls">
            {{-- Clear Chat --}}
            <button type="button" class="edvora-ai-icon-btn" id="edvoraAiClearBtn" title="Clear chat">
                <i class="bi bi-trash3"></i>
            </button>

            {{-- Close Window --}}
            <button type="button" class="edvora-ai-icon-btn" id="edvoraAiCloseBtn" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    {{-- Guest Usage Notice --}}
    @if($isGuest)
    <div class="edvora-ai-guest-bar" id="edvoraAiGuestBar">
        <span id="edvoraAiGuestInfo">{{ $guestRemaining }} of 5 free questions remaining</span>
        <a href="{{ route('login') }}" id="edvoraAiGuestLogin">Log in</a>
    </div>
    @endif

    {{-- Messages Body --}}
    <div class="edvora-ai-messages" id="edvoraAiMessages">
        {{-- Messages injected dynamically --}}
    </div>

    {{-- Footer Input --}}
    <div class="edvora-ai-footer">
        <form class="edvora-ai-input-form" id="edvoraAiForm" onsubmit="return false;">
            <input type="text"
                   class="edvora-ai-input"
                   id="edvoraAiInput"
                   placeholder="Type your question in English or فارسی..."
                   autocomplete="off"
                   dir="auto" />
            <button type="submit" class="edvora-ai-send-btn" id="edvoraAiSendBtn" aria-label="Send">
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
        var guestUsed = parseInt(windowEl.getAttribute('data-guest-used') || '0', 10);
        var guestRemaining = parseInt(windowEl.getAttribute('data-guest-remaining') || '5', 10);
        var loginUrl = windowEl.getAttribute('data-login-url') || '/login';
        var registerUrl = windowEl.getAttribute('data-register-url') || '/register';

        var chatHistory = [];

        // Initial welcome prompts
        var defaultSuggestions = [
            'دوره‌های آموزشی موجود',
            'Available courses',
            'اساتید و مربیان ادورا',
            'کتاب‌ها و منابع رایگان'
        ];

        // Open & Close Window
        function openWindow() {
            windowEl.classList.add('is-open');
            windowEl.setAttribute('aria-hidden', 'false');
            if (inputEl) inputEl.focus();
            if (messagesEl.children.length === 0) {
                renderInitialWelcome();
            }
        }

        function closeWindow() {
            windowEl.classList.remove('is-open');
            windowEl.setAttribute('aria-hidden', 'true');
        }

        triggerBtn.addEventListener('click', function () {
            windowEl.classList.contains('is-open') ? closeWindow() : openWindow();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', closeWindow);
        }

        // Clear Chat
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
            
            // Bot Welcome (Bilingual greeting)
            var welcomeMsg = document.createElement('div');
            welcomeMsg.className = 'edvora-ai-msg edvora-ai-msg--bot';
            welcomeMsg.innerHTML =
                '<div class="edvora-ai-msg-avatar"><i class="bi bi-robot"></i></div>' +
                '<div class="edvora-ai-bubble" dir="auto">' +
                '<p>سلام! من دستیار هوشمند ادورا هستم. هر سوالی در مورد دوره‌ها، اساتید یا آموزش دارید به فارسی یا انگلیسی بپرسید.</p>' +
                '<p style="opacity: 0.85; font-size: 0.82rem; margin-top: 4px;">Hello! Ask anything about Edvora courses, instructors, or events.</p>' +
                '</div>';
            messagesEl.appendChild(welcomeMsg);

            // Suggestions Chips
            var chipsWrap = document.createElement('div');
            chipsWrap.className = 'edvora-ai-suggestions';
            defaultSuggestions.forEach(function (prompt) {
                var chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'edvora-ai-chip';
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

        // HTML Escaping
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        // Markdown Formatter (Bold, Links, Lists, Linebreaks)
        function formatMarkdown(text) {
            if (!text) return '';
            var html = escHtml(text);
            
            // Markdown links: [Title](url) -> Clickable <a href="url">
            html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, function (match, title, url) {
                var cleanUrl = url.trim();
                return '<a href="' + cleanUrl + '" class="edvora-ai-link" target="_blank" rel="noopener">' + title + ' <i class="bi bi-box-arrow-up-right" style="font-size: 0.72rem;"></i></a>';
            });

            // Bold & Italic & Code
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                       .replace(/\*(.*?)\*/g, '<em>$1</em>')
                       .replace(/`([^`]+)`/g, '<code>$1</code>');

            // Bullet Lists
            html = html.replace(/^\s*[-*]\s+(.+)$/gm, '<li>$1</li>');
            if (html.indexOf('<li>') !== -1) {
                html = html.replace(/(<li>.*<\/li>(\n|$))+/g, function (m) { return '<ul>' + m + '</ul>'; });
            }

            // Paragraphs & Breaks
            html = html.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>');
            return '<p>' + html + '</p>';
        }

        function appendUserMessage(text) {
            var msg = document.createElement('div');
            msg.className = 'edvora-ai-msg edvora-ai-msg--user';
            msg.innerHTML =
                '<div class="edvora-ai-bubble" dir="auto">' +
                '<p>' + escHtml(text) + '</p>' +
                '</div>';
            messagesEl.appendChild(msg);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function appendBotMessage(text) {
            var msg = document.createElement('div');
            msg.className = 'edvora-ai-msg edvora-ai-msg--bot';

            msg.innerHTML =
                '<div class="edvora-ai-msg-avatar"><i class="bi bi-robot"></i></div>' +
                '<div class="edvora-ai-bubble" dir="auto">' +
                formatMarkdown(text) +
                '</div>';
            messagesEl.appendChild(msg);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function showTypingIndicator() {
            var typing = document.createElement('div');
            typing.className = 'edvora-ai-msg edvora-ai-msg--bot';
            typing.id = 'edvoraAiTyping';
            typing.innerHTML =
                '<div class="edvora-ai-msg-avatar"><i class="bi bi-robot"></i></div>' +
                '<div class="edvora-ai-bubble">' +
                '<div class="edvora-ai-typing"><span></span><span></span><span></span></div>' +
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
            limitCard.className = 'edvora-ai-msg edvora-ai-msg--bot';
            limitCard.innerHTML =
                '<div class="edvora-ai-msg-avatar"><i class="bi bi-lock-fill" style="color: #f59e0b;"></i></div>' +
                '<div class="edvora-ai-bubble" dir="auto" style="background: #1e293b; border: 1px solid rgba(245, 158, 11, 0.4);">' +
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

        // Send Message Handler
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

            // Auto detect whether user typed Persian or English
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

        // Form Submit
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
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEdvoraAi);
    } else {
        initEdvoraAi();
    }
})();
</script>
