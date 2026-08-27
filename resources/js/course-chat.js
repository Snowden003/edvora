const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

const createAvatar = (user, className) => {
    if (user.avatar) {
        const img = document.createElement('img');
        img.className = className;
        img.alt = user.name;
        img.src = user.avatar;
        return img;
    }
    const div = document.createElement('div');
    div.className = `${className} ${className}--letter`;
    div.textContent = (user.name || '?').charAt(0).toUpperCase();
    return div;
};

const isTeacher = (user) => user.role === 'teacher';

const timeLabel = (value) => new Intl.DateTimeFormat(undefined, { hour: '2-digit', minute: '2-digit' }).format(new Date(value));

const renderMember = (user) => {
    const element = document.createElement('div');
    element.className = 'course-chat__member';
    const info = document.createElement('div');
    info.className = 'course-chat__member-info';
    const nameRow = document.createElement('div');
    nameRow.className = 'course-chat__member-name-row';
    const name = document.createElement('span');
    name.className = 'course-chat__member-name';
    name.textContent = user.name;
    nameRow.appendChild(name);
    if (isTeacher(user)) {
        const teacherBadge = document.createElement('span');
        teacherBadge.className = 'course-chat__badge course-chat__badge--teacher';
        teacherBadge.textContent = 'Instructor';
        nameRow.appendChild(teacherBadge);
    }
    const onlineBadge = document.createElement('span');
    onlineBadge.className = 'course-chat__badge course-chat__badge--online';
    onlineBadge.textContent = 'Online';
    info.appendChild(nameRow);
    info.appendChild(onlineBadge);
    element.appendChild(createAvatar(user, 'course-chat__member-avatar'));
    element.appendChild(info);
    return element;
};

// ── Notification Sound (Web Audio API – no file needed) ──────────────
const playNotificationSound = () => {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const notes = [523.25, 659.25, 783.99]; // C5 E5 G5 chime
        notes.forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, ctx.currentTime + i * 0.12);
            gain.gain.setValueAtTime(0, ctx.currentTime + i * 0.12);
            gain.gain.linearRampToValueAtTime(0.18, ctx.currentTime + i * 0.12 + 0.01);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + i * 0.12 + 0.35);
            osc.start(ctx.currentTime + i * 0.12);
            osc.stop(ctx.currentTime + i * 0.12 + 0.4);
        });
    } catch (e) { /* AudioContext not supported */ }
};

// ── Badge helpers (localStorage) ─────────────────────────────────────
const BADGE_KEY = (courseId) => `chat_unread_${courseId}`;

const getBadgeCount = (courseId) => parseInt(localStorage.getItem(BADGE_KEY(courseId)) || '0', 10);

const setBadgeCount = (courseId, count) => {
    if (count <= 0) {
        localStorage.removeItem(BADGE_KEY(courseId));
    } else {
        localStorage.setItem(BADGE_KEY(courseId), String(count));
    }
    updateSidebarBadges(courseId, count);
};

const incrementBadge = (courseId) => setBadgeCount(courseId, getBadgeCount(courseId) + 1);

const clearBadge = (courseId) => setBadgeCount(courseId, 0);

const updateSidebarBadges = (courseId, count) => {
    // Find all sidebar chat links for this course and update/create badge
    document.querySelectorAll(`[data-chat-badge-course="${courseId}"]`).forEach(el => {
        if (count > 0) {
            el.textContent = count > 99 ? '99+' : count;
            el.style.display = 'inline-flex';
        } else {
            el.style.display = 'none';
        }
    });
};

// Inject CSS for the badge once
const injectBadgeStyles = () => {
    if (document.getElementById('chat-badge-styles')) return;
    const style = document.createElement('style');
    style.id = 'chat-badge-styles';
    style.textContent = `
        .chat-unread-badge {
            display: none;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 999px;
            margin-left: 4px;
            line-height: 1;
            box-shadow: 0 0 0 2px rgba(239,68,68,0.25);
            animation: chatBadgePop 0.3s ease;
            flex-shrink: 0;
        }
        @keyframes chatBadgePop {
            0%   { transform: scale(0.5); opacity: 0; }
            70%  { transform: scale(1.2); }
            100% { transform: scale(1);   opacity: 1; }
        }
    `;
    document.head.appendChild(style);
};

// Inject badge elements next to all sidebar chat links for a course
const injectBadgeElements = (courseId) => {
    injectBadgeStyles();
    document.querySelectorAll('a[href*="/courses/"][href*="chat"]').forEach(link => {
        // Check if this link is for our course
        if (!link.href.includes(`/courses/${courseId}`) && !link.dataset.courseChatLink) return;
        if (link.querySelector(`[data-chat-badge-course="${courseId}"]`)) return;

        const badge = document.createElement('span');
        badge.className = 'chat-unread-badge';
        badge.dataset.chatBadgeCourse = courseId;
        badge.style.display = 'none';
        link.appendChild(badge);
    });

    // Also inject in any generic chat sidebar buttons
    document.querySelectorAll('.action-chat, .edvora-action-btn.action-chat').forEach(link => {
        if (link.querySelector(`[data-chat-badge-course="${courseId}"]`)) return;
        const badge = document.createElement('span');
        badge.className = 'chat-unread-badge';
        badge.dataset.chatBadgeCourse = courseId;
        badge.style.display = 'none';
        link.appendChild(badge);
    });
};

// ── Detect if user is currently on the chat page ──────────────────────
const isOnChatPage = () => {
    return document.querySelector('[data-course-chat]') !== null;
};

const initCourseChat = (root) => {
    const courseId = root.dataset.courseId;
    const messagesUrl = root.dataset.messagesUrl;
    const sendUrl = root.dataset.sendUrl;
    const currentUserId = Number(root.dataset.currentUserId);
    const messages = root.querySelector('[data-chat-messages]');
    const members = root.querySelector('[data-online-members]');
    const onlineCount = root.querySelector('[data-online-count]');
    const status = root.querySelector('[data-chat-status]');
    const form = root.querySelector('[data-chat-form]');
    const input = root.querySelector('[data-chat-input]');
    const submit = root.querySelector('[data-chat-submit]');
    const displayedMessageIds = new Set();
    let onlineUsers = [];

    // Clear badge when user opens the chat page
    clearBadge(courseId);

    const setStatus = (text) => { status.textContent = text; };

    const renderMembers = () => {
        members.replaceChildren(...onlineUsers.map(renderMember));
        onlineCount.textContent = onlineUsers.length;
    };

    const renderMessage = (message, fromBroadcast = false) => {
        if (displayedMessageIds.has(message.id)) return;
        displayedMessageIds.add(message.id);
        const isMine = Number(message.user.id) === currentUserId;
        const element = document.createElement('div');
        element.className = `course-chat__message${isMine ? ' course-chat__message--mine' : ''}`;
        const content = document.createElement('div');
        const meta = document.createElement('div');
        meta.className = 'course-chat__meta';
        const name = document.createElement('span');
        name.className = 'course-chat__name';
        name.textContent = isMine ? 'You' : message.user.name;
        meta.appendChild(name);
        if (!isMine && isTeacher(message.user)) {
            const teacherBadge = document.createElement('span');
            teacherBadge.className = 'course-chat__badge course-chat__badge--teacher';
            teacherBadge.textContent = 'Instructor';
            meta.appendChild(teacherBadge);
        }
        const time = document.createElement('time');
        time.textContent = timeLabel(message.created_at);
        meta.appendChild(time);
        const bubble = document.createElement('div');
        bubble.className = 'course-chat__bubble';
        bubble.textContent = message.body;
        content.appendChild(meta);
        content.appendChild(bubble);
        element.appendChild(createAvatar(message.user, 'course-chat__avatar'));
        element.appendChild(content);
        messages.querySelector('.course-chat__empty')?.remove();
        messages.append(element);
        messages.scrollTop = messages.scrollHeight;
    };

    fetch(messagesUrl, { headers: { Accept: 'application/json' } })
        .then(async (response) => {
            if (!response.ok) throw new Error('Unable to load messages');
            return response.json();
        })
        .then(({ messages: chatMessages }) => {
            if (chatMessages.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'course-chat__empty';
                empty.textContent = 'No messages yet. Start the conversation.';
                messages.append(empty);
            } else {
                chatMessages.forEach(m => renderMessage(m, false));
            }
        })
        .catch(() => setStatus('Unable to load message history.'));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const body = input.value.trim();
        if (!body) return;
        submit.disabled = true;
        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ body }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Unable to send message');
            renderMessage(data.message, false);
            input.value = '';
            input.style.height = '';
        } catch (error) {
            setStatus(error.message || 'Unable to send message.');
        } finally {
            submit.disabled = false;
            input.focus();
        }
    });

    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 108)}px`;
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    if (!window.Echo) {
        setStatus('Live chat is unavailable. Check the Pusher connection.');
        return;
    }

    window.Echo.join(`course-chat.${courseId}`)
        .here((users) => {
            onlineUsers = users;
            renderMembers();
            setStatus('Live chat connected.');
        })
        .joining((user) => {
            if (!onlineUsers.some((onlineUser) => Number(onlineUser.id) === Number(user.id))) {
                onlineUsers.push(user);
                renderMembers();
            }
        })
        .leaving((user) => {
            onlineUsers = onlineUsers.filter((onlineUser) => Number(onlineUser.id) !== Number(user.id));
            renderMembers();
        })
        .listen('.course.message.created', ({ message }) => {
            const isMine = Number(message.user.id) === currentUserId;
            renderMessage(message, true);
            // Notification only for others' messages when tab is hidden
            if (!isMine && document.hidden) {
                playNotificationSound();
            }
        })
        .error(() => setStatus('Live chat connection failed. Reconnecting may restore it.'));
};

// ── Background listener: for pages that are NOT the chat page ─────────
// Listens via Echo on all courses the user is enrolled in,
// increments badge & plays sound when a message arrives.
const initBackgroundChatListener = () => {
    if (!window.Echo) return;

    // Find all sidebar chat links that carry a course-id attr
    document.querySelectorAll('[data-bg-chat-course]').forEach(el => {
        const courseId = el.dataset.bgChatCourse;
        const currentUserId = Number(el.dataset.bgChatUser || '0');

        injectBadgeElements(courseId);

        // Restore badge count from localStorage on page load
        const saved = getBadgeCount(courseId);
        if (saved > 0) updateSidebarBadges(courseId, saved);

        window.Echo.channel(`course-chat-bg.${courseId}`)
            .listen('.course.message.created', ({ message }) => {
                if (Number(message.user.id) === currentUserId) return; // own message
                playNotificationSound();
                incrementBadge(courseId);
            });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    // Init full chat widget if on the chat page
    document.querySelectorAll('[data-course-chat]').forEach(initCourseChat);

    // Init background listeners on all other pages (via sidebar)
    if (!isOnChatPage()) {
        initBackgroundChatListener();
    }
});
