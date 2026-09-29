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

// ── Notification Sound ────────────────────────────────────────────────
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
    document.querySelectorAll(`[data-chat-badge-course="${courseId}"]`).forEach(el => {
        if (count > 0) {
            el.textContent = count > 99 ? '99+' : count;
            el.style.display = 'inline-flex';
        } else {
            el.style.display = 'none';
        }
    });
};

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

const injectBadgeElements = (courseId) => {
    injectBadgeStyles();
    document.querySelectorAll('a[href*="/courses/"][href*="chat"]').forEach(link => {
        if (!link.href.includes(`/courses/${courseId}`) && !link.dataset.courseChatLink) return;
        if (link.querySelector(`[data-chat-badge-course="${courseId}"]`)) return;

        const badge = document.createElement('span');
        badge.className = 'chat-unread-badge';
        badge.dataset.chatBadgeCourse = courseId;
        badge.style.display = 'none';
        link.appendChild(badge);
    });

    document.querySelectorAll('.action-chat, .edvora-action-btn.action-chat').forEach(link => {
        if (link.querySelector(`[data-chat-badge-course="${courseId}"]`)) return;
        const badge = document.createElement('span');
        badge.className = 'chat-unread-badge';
        badge.dataset.chatBadgeCourse = courseId;
        badge.style.display = 'none';
        link.appendChild(badge);
    });
};

// ── Toast & Desktop Notifications ───────────────────────────────────────
const showChatToast = (message, courseId) => {
    let container = document.getElementById('chat-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'chat-toast-container';
        container.style.cssText = 'position:fixed;top:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:10px;max-width:380px;width:calc(100% - 48px);pointer-events:none;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.style.cssText = 'pointer-events:auto;background:#ffffff;border:1px solid #e0e7ff;border-left:5px solid #6366f1;border-radius:14px;padding:14px 16px;box-shadow:0 12px 35px rgba(15,23,42,0.18);display:flex;align-items:flex-start;gap:12px;cursor:pointer;transition:transform 0.2s ease, box-shadow 0.2s ease;';
    
    toast.onmouseenter = () => { toast.style.transform = 'translateY(-2px)'; };
    toast.onmouseleave = () => { toast.style.transform = 'translateY(0)'; };

    const avatar = createAvatar(message.user, 'course-chat__avatar');
    avatar.style.cssText = 'width:40px;height:40px;border-radius:50%;flex-shrink:0;';

    const content = document.createElement('div');
    content.style.cssText = 'flex:1;min-width:0;';
    
    const title = document.createElement('div');
    title.style.cssText = 'font-weight:700;font-size:0.88rem;color:#1e293b;display:flex;justify-content:space-between;align-items:center;';
    title.innerHTML = `<span style="display:flex;align-items:center;gap:6px;"><i class="bi bi-chat-dots-fill" style="color:#6366f1;"></i> ${message.user.name}</span><span style="font-size:0.7rem;color:#94a3b8;font-weight:400;">Just now</span>`;

    const body = document.createElement('div');
    body.style.cssText = 'font-size:0.83rem;color:#475569;margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
    body.textContent = message.body;

    content.appendChild(title);
    content.appendChild(body);
    toast.appendChild(avatar);
    toast.appendChild(content);

    toast.addEventListener('click', () => {
        if (window.switchCourseTab) {
            window.switchCourseTab('chat');
        } else {
            window.location.href = `/teacher/courses/${courseId}#tab-chat`;
        }
        toast.remove();
    });

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 6000);
};

const sendDesktopNotification = (message) => {
    if ('Notification' in window && Notification.permission === 'granted') {
        try {
            new Notification(`New message from ${message.user.name}`, {
                body: message.body,
                icon: message.user.avatar || '/extension_icon.png'
            });
        } catch (e) {}
    } else if ('Notification' in window && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }
};

const isChatTabVisible = (root) => {
    const tabPane = root.closest('.cd-tab-pane, .tab-pane, .tab-panel');
    if (!tabPane) return true;
    return tabPane.classList.contains('active') && getComputedStyle(tabPane).display !== 'none';
};

window.clearCourseChatBadge = (courseId) => {
    clearBadge(courseId);
};

const isOnChatPage = () => {
    return document.querySelector('[data-course-chat]') !== null;
};

// ── Main Course Chat Component Logic ────────────────────────────────────
const initCourseChat = (root) => {
    const courseId = root.dataset.courseId;
    const messagesUrl = root.dataset.messagesUrl;
    const sendUrl = root.dataset.sendUrl;
    const currentUserId = Number(root.dataset.currentUserId);
    const userRole = root.dataset.userRole || 'student';
    const isCurrentUserTeacher = root.dataset.isTeacher === '1';

    const messages = root.querySelector('[data-chat-messages]');
    const members = root.querySelector('[data-online-members]');
    const onlineCount = root.querySelector('[data-online-count]');
    const status = root.querySelector('[data-chat-status]');
    const form = root.querySelector('[data-chat-form]');
    const input = root.querySelector('[data-chat-input]');
    const submit = root.querySelector('[data-chat-submit]');

    const pinnedBar = root.querySelector('[data-pinned-bar]');
    const pinnedText = root.querySelector('[data-pinned-text]');
    const pinnedJumpBtn = root.querySelector('[data-pinned-jump]');

    const messagesMap = new Map(); // id -> { data, element }
    let onlineUsers = [];

    if (isChatTabVisible(root)) {
        clearBadge(courseId);
    }
    
    injectBadgeElements(courseId);
    const savedCount = getBadgeCount(courseId);
    if (savedCount > 0) updateSidebarBadges(courseId, savedCount);

    const setStatus = (text) => { status.textContent = text; };

    const renderMembers = () => {
        members.replaceChildren(...onlineUsers.map(renderMember));
        onlineCount.textContent = onlineUsers.length;
    };

    const updatePinnedBar = () => {
        if (!pinnedBar) return;
        // Find latest pinned message
        let latestPinned = null;
        for (const item of Array.from(messagesMap.values()).reverse()) {
            if (item.data.is_pinned) {
                latestPinned = item.data;
                break;
            }
        }

        if (latestPinned) {
            pinnedText.textContent = latestPinned.body;
            pinnedBar.style.display = 'flex';
            pinnedJumpBtn.onclick = () => {
                const targetEl = messagesMap.get(latestPinned.id)?.element;
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const bubble = targetEl.querySelector('.course-chat__bubble');
                    if (bubble) {
                        bubble.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
                        bubble.style.transform = 'scale(1.05)';
                        setTimeout(() => { bubble.style.transform = 'scale(1)'; }, 400);
                    }
                }
            };
        } else {
            pinnedBar.style.display = 'none';
        }
    };

    const togglePinMessage = async (msgData) => {
        try {
            const url = `/courses/${courseId}/chat/messages/${msgData.id}/pin`;
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to toggle pin');
            renderOrUpdateMessage(data.message);
            updatePinnedBar();
        } catch (e) {
            setStatus(e.message || 'Unable to pin/unpin message.');
        }
    };

    const deleteMessage = async (msgData) => {
        if (!confirm('Are you sure you want to delete this message?')) return;
        try {
            const url = `/courses/${courseId}/chat/messages/${msgData.id}`;
            const res = await fetch(url, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (!res.ok) throw new Error('Failed to delete message');
            removeMessageFromDom(msgData.id);
            updatePinnedBar();
        } catch (e) {
            setStatus(e.message || 'Unable to delete message.');
        }
    };

    const editMessage = (msgData, bubbleEl) => {
        const existingForm = bubbleEl.querySelector('.course-chat__edit-box');
        if (existingForm) return;

        const originalText = msgData.body;
        bubbleEl.innerHTML = '';

        const editBox = document.createElement('div');
        editBox.className = 'course-chat__edit-box';

        const textarea = document.createElement('textarea');
        textarea.className = 'course-chat__edit-input';
        textarea.value = originalText;
        textarea.rows = 2;

        const actionRow = document.createElement('div');
        actionRow.className = 'course-chat__edit-actions';

        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'course-chat__edit-btn course-chat__edit-btn--cancel';
        cancelBtn.textContent = 'Cancel';

        const saveBtn = document.createElement('button');
        saveBtn.type = 'button';
        saveBtn.className = 'course-chat__edit-btn course-chat__edit-btn--save';
        saveBtn.textContent = 'Save';

        actionRow.appendChild(cancelBtn);
        actionRow.appendChild(saveBtn);
        editBox.appendChild(textarea);
        editBox.appendChild(actionRow);
        bubbleEl.appendChild(editBox);

        textarea.focus();

        cancelBtn.onclick = () => {
            bubbleEl.innerHTML = '';
            bubbleEl.textContent = originalText;
        };

        saveBtn.onclick = async () => {
            const newText = textarea.value.trim();
            if (!newText || newText === originalText) {
                bubbleEl.innerHTML = '';
                bubbleEl.textContent = originalText;
                return;
            }
            saveBtn.disabled = true;
            try {
                const url = `/courses/${courseId}/chat/messages/${msgData.id}`;
                const res = await fetch(url, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ body: newText })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to edit message');
                renderOrUpdateMessage(data.message);
                updatePinnedBar();
            } catch (e) {
                setStatus(e.message || 'Unable to update message.');
                bubbleEl.innerHTML = '';
                bubbleEl.textContent = originalText;
            }
        };
    };

    const renderOrUpdateMessage = (message, scroll = false) => {
        const isMine = Number(message.user.id) === currentUserId;
        const canEdit = isMine || userRole === 'admin';
        const canDelete = isMine || isCurrentUserTeacher || userRole === 'admin';
        const canPin = isMine || isCurrentUserTeacher || userRole === 'admin';

        let existing = messagesMap.get(message.id);

        if (existing) {
            // Update existing message
            existing.data = message;
            const element = existing.element;

            const bubble = element.querySelector('.course-chat__bubble');
            if (bubble) {
                bubble.textContent = message.body;
                if (message.is_pinned) {
                    bubble.classList.add('course-chat__bubble--pinned');
                } else {
                    bubble.classList.remove('course-chat__bubble--pinned');
                }
            }

            const meta = element.querySelector('.course-chat__meta');
            if (meta) {
                let pinnedInd = meta.querySelector('.course-chat__pinned-indicator');
                if (message.is_pinned && !pinnedInd) {
                    pinnedInd = document.createElement('span');
                    pinnedInd.className = 'course-chat__pinned-indicator';
                    pinnedInd.innerHTML = '<i class="bi bi-pin-angle-fill"></i> Pinned';
                    meta.appendChild(pinnedInd);
                } else if (!message.is_pinned && pinnedInd) {
                    pinnedInd.remove();
                }

                let editedTag = meta.querySelector('.course-chat__edited-tag');
                if (message.is_edited && !editedTag) {
                    editedTag = document.createElement('span');
                    editedTag.className = 'course-chat__edited-tag';
                    editedTag.textContent = '(edited)';
                    meta.appendChild(editedTag);
                }
            }

            const pinBtn = element.querySelector('.course-chat__action-btn--pin');
            if (pinBtn) {
                if (message.is_pinned) {
                    pinBtn.classList.add('is-active');
                    pinBtn.title = 'Unpin message';
                } else {
                    pinBtn.classList.remove('is-active');
                    pinBtn.title = 'Pin message';
                }
            }

            updatePinnedBar();
            return;
        }

        // Create new message DOM element
        const element = document.createElement('div');
        element.className = `course-chat__message${isMine ? ' course-chat__message--mine' : ''}`;
        element.dataset.messageId = message.id;

        const content = document.createElement('div');
        content.className = 'course-chat__message-wrapper';

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

        if (message.is_pinned) {
            const pinnedInd = document.createElement('span');
            pinnedInd.className = 'course-chat__pinned-indicator';
            pinnedInd.innerHTML = '<i class="bi bi-pin-angle-fill"></i> Pinned';
            meta.appendChild(pinnedInd);
        }

        const time = document.createElement('time');
        time.textContent = timeLabel(message.created_at);
        meta.appendChild(time);

        if (message.is_edited) {
            const editedTag = document.createElement('span');
            editedTag.className = 'course-chat__edited-tag';
            editedTag.textContent = '(edited)';
            meta.appendChild(editedTag);
        }

        const bubbleContainer = document.createElement('div');
        bubbleContainer.className = 'course-chat__bubble-container';

        const bubble = document.createElement('div');
        bubble.className = `course-chat__bubble${message.is_pinned ? ' course-chat__bubble--pinned' : ''}`;
        bubble.textContent = message.body;

        // Message action buttons (Pin, Edit, Delete)
        if (canPin || canEdit || canDelete) {
            const actions = document.createElement('div');
            actions.className = 'course-chat__actions';

            if (canPin) {
                const pinBtn = document.createElement('button');
                pinBtn.type = 'button';
                pinBtn.className = `course-chat__action-btn course-chat__action-btn--pin${message.is_pinned ? ' is-active' : ''}`;
                pinBtn.title = message.is_pinned ? 'Unpin message' : 'Pin message';
                pinBtn.innerHTML = '<i class="bi bi-pin-angle-fill"></i>';
                pinBtn.onclick = (e) => {
                    e.stopPropagation();
                    togglePinMessage(message);
                };
                actions.appendChild(pinBtn);
            }

            if (canEdit) {
                const editBtn = document.createElement('button');
                editBtn.type = 'button';
                editBtn.className = 'course-chat__action-btn course-chat__action-btn--edit';
                editBtn.title = 'Edit message';
                editBtn.innerHTML = '<i class="bi bi-pencil-fill"></i>';
                editBtn.onclick = (e) => {
                    e.stopPropagation();
                    editMessage(messagesMap.get(message.id)?.data || message, bubble);
                };
                actions.appendChild(editBtn);
            }

            if (canDelete) {
                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'course-chat__action-btn course-chat__action-btn--delete';
                delBtn.title = 'Delete message';
                delBtn.innerHTML = '<i class="bi bi-trash-fill"></i>';
                delBtn.onclick = (e) => {
                    e.stopPropagation();
                    deleteMessage(messagesMap.get(message.id)?.data || message);
                };
                actions.appendChild(delBtn);
            }

            bubbleContainer.appendChild(actions);
        }

        bubbleContainer.appendChild(bubble);
        content.appendChild(meta);
        content.appendChild(bubbleContainer);

        element.appendChild(createAvatar(message.user, 'course-chat__avatar'));
        element.appendChild(content);

        messages.querySelector('.course-chat__empty')?.remove();
        messages.append(element);
        messagesMap.set(message.id, { data: message, element });

        if (scroll) {
            messages.scrollTop = messages.scrollHeight;
        }

        updatePinnedBar();
    };

    const removeMessageFromDom = (messageId) => {
        const item = messagesMap.get(messageId);
        if (item) {
            item.element.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            item.element.style.opacity = '0';
            item.element.style.transform = 'scale(0.95)';
            setTimeout(() => {
                item.element.remove();
                messagesMap.delete(messageId);
                if (messagesMap.size === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'course-chat__empty';
                    empty.textContent = 'No messages yet. Start the conversation.';
                    messages.append(empty);
                }
                updatePinnedBar();
            }, 200);
        }
    };

    // Load message history
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
                chatMessages.forEach(m => renderOrUpdateMessage(m, false));
                messages.scrollTop = messages.scrollHeight;
            }
        })
        .catch(() => setStatus('Unable to load message history.'));

    // Form submit
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
            renderOrUpdateMessage(data.message, true);
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

    // Echo presence channel listener
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
            renderOrUpdateMessage(message, true);
            
            if (!isMine) {
                const visible = isChatTabVisible(root);
                if (!visible || document.hidden) {
                    playNotificationSound();
                    incrementBadge(courseId);
                    showChatToast(message, courseId);
                    sendDesktopNotification(message);
                }
            }
        })
        .listen('.course.message.updated', ({ message }) => {
            renderOrUpdateMessage(message, false);
        })
        .listen('.course.message.deleted', ({ message_id }) => {
            removeMessageFromDom(message_id);
        })
        .error((error) => {
            console.error('Pusher / Echo live chat error:', error);
            const status = error?.status || error?.error?.status;
            if (status === 403) {
                setStatus('Access denied: You are not enrolled or authorized for this course chat.');
            } else if (status === 419) {
                setStatus('Session expired. Please refresh the page to reconnect.');
            } else if (status === 401) {
                setStatus('Authentication required. Please log in again.');
            } else {
                setStatus('Live chat connection failed. Reconnecting may restore it.');
            }
        });
};

const initBackgroundChatListener = () => {
    if (!window.Echo) return;

    document.querySelectorAll('[data-bg-chat-course]').forEach(el => {
        const courseId = el.dataset.bgChatCourse;
        const currentUserId = Number(el.dataset.bgChatUser || '0');

        injectBadgeElements(courseId);

        const saved = getBadgeCount(courseId);
        if (saved > 0) updateSidebarBadges(courseId, saved);

        window.Echo.channel(`course-chat-bg.${courseId}`)
            .listen('.course.message.created', ({ message }) => {
                if (Number(message.user.id) === currentUserId) return;
                playNotificationSound();
                incrementBadge(courseId);
                showChatToast(message, courseId);
                sendDesktopNotification(message);
            });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-course-chat]').forEach(initCourseChat);

    if (!isOnChatPage()) {
        initBackgroundChatListener();
    }
});
