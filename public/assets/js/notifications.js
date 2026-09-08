/**
 * Edvora - Global Real-Time Notification & Live Class Engine
 * 
 * Provides:
 * 1. Global real-time polling (/notifications/live-check) every 5 seconds on all pages.
 * 2. Floating live class banner (#edvora-live-class-banner) with instant Google Meet link.
 * 3. Rich in-app floating toasts (#edvora-toast-container) with sound chime & direct actions.
 * 4. Native browser push notifications.
 * 5. Dynamic navbar bell & badge counter updates.
 * 6. Mark read / delete operations.
 */

(function () {
  'use strict';

  // --- Configuration & State ---
  var POLL_INTERVAL = 5000; // 5 seconds
  var lastPollTimestamp = null;
  var seenNotificationIds = new Set();
  var dismissedLiveSessions = new Set();
  var audioContext = null;
  var isAudioUnlocked = false;

  // Initialize dismissed sessions from sessionStorage
  try {
    var storedDismissed = sessionStorage.getItem('edvora_dismissed_sessions');
    if (storedDismissed) {
      JSON.parse(storedDismissed).forEach(function (id) {
        dismissedLiveSessions.add(Number(id));
      });
    }
  } catch (e) {}

  // --- Helpers ---
  function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function getNotificationBaseUrl() {
    return '/notifications';
  }

  // --- Web Audio Synthesizer Chime ---
  function unlockAudio() {
    if (isAudioUnlocked) return;
    try {
      var AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (AudioCtx) {
        if (!audioContext) {
          audioContext = new AudioCtx();
        }
        if (audioContext.state === 'suspended') {
          audioContext.resume();
        }
        isAudioUnlocked = true;
      }
    } catch (e) {
      console.warn('Audio context init error:', e);
    }
  }

  // Unlock audio on first user gesture
  ['click', 'touchstart', 'keydown'].forEach(function (evt) {
    document.addEventListener(evt, unlockAudio, { once: true, passive: true });
  });

  function playNotificationSound(type) {
    if (!audioContext) {
      unlockAudio();
    }
    if (!audioContext) return;

    try {
      if (audioContext.state === 'suspended') {
        audioContext.resume();
      }

      var now = audioContext.currentTime;
      var osc = audioContext.createOscillator();
      var gain = audioContext.createGain();
      osc.connect(gain);
      gain.connect(audioContext.destination);

      if (type === 'class_started') {
        // High-energy 3-tone chime for live class
        osc.frequency.setValueAtTime(523.25, now); // C5
        osc.frequency.setValueAtTime(659.25, now + 0.12); // E5
        osc.frequency.setValueAtTime(783.99, now + 0.24); // G5
        gain.gain.setValueAtTime(0.35, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
        osc.start(now);
        osc.stop(now + 0.55);
      } else {
        // Subtle pleasant 2-tone chime for general alerts
        osc.frequency.setValueAtTime(659.25, now); // E5
        osc.frequency.setValueAtTime(880, now + 0.12); // A5
        gain.gain.setValueAtTime(0.25, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
        osc.start(now);
        osc.stop(now + 0.45);
      }
    } catch (err) {
      // Browsers may block if no interaction occurred yet
    }
  }

  // --- Browser Notification API ---
  function requestBrowserPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission();
    }
  }

  function showBrowserNotification(title, body, url) {
    if (!('Notification' in window) || Notification.permission !== 'granted') {
      return;
    }
    try {
      var notif = new Notification(title, {
        body: body,
        icon: '/assets/images/logo1.jpg',
        badge: '/favicon.ico',
        tag: 'edvora-' + Date.now()
      });

      if (url) {
        notif.onclick = function () {
          window.focus();
          if (url.startsWith('http')) {
            window.open(url, '_blank');
          } else {
            window.location.href = url;
          }
          notif.close();
        };
      }
    } catch (e) {
      console.warn('Native notification failed:', e);
    }
  }

  // --- DOM: Toast Container & Display ---
  function getOrCreateToastContainer() {
    var container = document.getElementById('edvora-toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'edvora-toast-container';
      document.body.appendChild(container);
    }
    return container;
  }

  function showToast(notif) {
    var container = getOrCreateToastContainer();
    var toast = document.createElement('div');
    toast.className = 'edvora-toast toast-' + (notif.type || 'general');
    toast.setAttribute('data-toast-id', notif.id);

    var iconClass = notif.icon || 'bi bi-bell-fill';
    var isMeet = notif.type === 'class_started' && notif.action_url;

    var actionHtml = '';
    if (notif.action_url) {
      var isExternal = notif.action_url.startsWith('http');
      var targetAttr = isExternal ? 'target="_blank" rel="noopener noreferrer"' : '';
      var btnClass = isMeet ? 'toast-action-btn btn-meet' : 'toast-action-btn';
      actionHtml = '<a href="' + escapeHtml(notif.action_url) + '" ' + targetAttr + ' class="' + btnClass + '">' +
        escapeHtml(notif.action_text || 'Open') + ' ↗</a>';
    }

    toast.innerHTML =
      '<div class="toast-icon-wrap"><i class="' + escapeHtml(iconClass) + '"></i></div>' +
      '<div class="toast-body-wrap">' +
        '<div class="toast-title">' + escapeHtml(notif.title) + '</div>' +
        '<div class="toast-msg">' + escapeHtml(notif.message) + '</div>' +
        actionHtml +
      '</div>' +
      '<button class="toast-close-btn" aria-label="Close">' +
        '<i class="bi bi-x-lg"></i>' +
      '</button>';

    var closeBtn = toast.querySelector('.toast-close-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        removeToast(toast);
      });
    }

    // Auto mark as read on action click
    var actionLink = toast.querySelector('a.toast-action-btn');
    if (actionLink && notif.id) {
      actionLink.addEventListener('click', function () {
        markAsRead(notif.id);
      });
    }

    container.appendChild(toast);

    // Auto dismiss after 9 seconds
    setTimeout(function () {
      removeToast(toast);
    }, 9000);
  }

  function removeToast(toast) {
    if (!toast || !toast.parentElement) return;
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(40px) scale(0.95)';
    setTimeout(function () {
      if (toast.parentElement) {
        toast.parentElement.removeChild(toast);
      }
    }, 280);
  }

  // --- DOM: Floating Live Class Banner ---
  function updateLiveClassBanner(activeClass) {
    var banner = document.getElementById('edvora-live-class-banner');

    if (!activeClass) {
      if (banner) {
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(-20px) scale(0.95)';
        setTimeout(function () {
          if (banner.parentElement) banner.parentElement.removeChild(banner);
        }, 300);
      }
      return;
    }

    // Check if dismissed
    if (dismissedLiveSessions.has(Number(activeClass.session_id))) {
      return;
    }

    var meetLink = activeClass.meet_link || '#';

    if (!banner) {
      banner = document.createElement('div');
      banner.id = 'edvora-live-class-banner';
      document.body.appendChild(banner);
    }

    banner.innerHTML =
      '<div class="live-banner-header">' +
        '<span class="live-pill">' +
          '<span class="live-pulse-dot"></span> Live Class Now' +
        '</span>' +
        '<button class="live-banner-close" aria-label="Dismiss banner">' +
          '<i class="bi bi-x-lg"></i>' +
        '</button>' +
      '</div>' +
      '<div class="live-banner-title">' + escapeHtml(activeClass.course_title) + '</div>' +
      '<div class="live-banner-msg">Your teacher is hosting a live Google Meet class right now. Click below to join immediately.</div>' +
      '<div class="live-banner-actions">' +
        '<a href="' + escapeHtml(meetLink) + '" target="_blank" rel="noopener noreferrer" class="btn-live-join">' +
          '<i class="bi bi-camera-video-fill"></i> Join Google Meet Now ↗' +
        '</a>' +
        '<button class="btn-live-dismiss">Dismiss</button>' +
      '</div>';

    var closeBtn = banner.querySelector('.live-banner-close');
    var dismissBtn = banner.querySelector('.btn-live-dismiss');

    function dismiss() {
      dismissedLiveSessions.add(Number(activeClass.session_id));
      try {
        sessionStorage.setItem('edvora_dismissed_sessions', JSON.stringify(Array.from(dismissedLiveSessions)));
      } catch (e) {}
      banner.style.opacity = '0';
      banner.style.transform = 'translateY(-20px) scale(0.95)';
      setTimeout(function () {
        if (banner.parentElement) banner.parentElement.removeChild(banner);
      }, 300);
    }

    if (closeBtn) closeBtn.addEventListener('click', dismiss);
    if (dismissBtn) dismissBtn.addEventListener('click', dismiss);
  }

  // --- Badge Updates ---
  function updateBadges(unreadCount) {
    // 1. Navbar badge
    var navBadges = document.querySelectorAll('.edvora-nav-notif-badge, [data-notif-badge]');
    navBadges.forEach(function (badge) {
      if (unreadCount > 0) {
        badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
        badge.style.display = 'inline-flex';
        badge.classList.add('has-unread');
      } else {
        badge.textContent = '0';
        badge.style.display = 'none';
        badge.classList.remove('has-unread');
      }
    });

    // 2. Page header badges (e.g. in /notifications page or student dashboard)
    var headerBadges = document.querySelectorAll('#notifCountText, .badge.bg-danger');
    headerBadges.forEach(function (el) {
      if (el.id === 'notifCountText') {
        el.textContent = unreadCount + ' unread';
      }
    });
  }

  // --- Real-Time Polling Engine ---
  var isPolling = false;
  function pollLiveCheck() {
    if (isPolling) return;
    isPolling = true;

    var url = getNotificationBaseUrl() + '/live-check';
    if (lastPollTimestamp) {
      url += '?since=' + encodeURIComponent(lastPollTimestamp);
    }

    fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(function (res) {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then(function (data) {
      isPolling = false;

      // Update badge counts
      if (typeof data.unread_count === 'number') {
        updateBadges(data.unread_count);
      }

      // Check for active live class
      updateLiveClassBanner(data.active_class);

      // Process new notifications
      if (data.notifications && data.notifications.length > 0) {
        var hasNew = false;
        var playSoundType = 'general';

        data.notifications.forEach(function (notif) {
          if (seenNotificationIds.has(notif.id)) return;
          seenNotificationIds.add(notif.id);
          hasNew = true;

          if (notif.type === 'class_started') {
            playSoundType = 'class_started';
          }

          // Show floating toast
          showToast(notif);

          // Native browser notification
          var targetUrl = notif.action_url || (window.location.origin + '/notifications');
          showBrowserNotification('Edvora: ' + notif.title, notif.message, targetUrl);

          // Dispatch custom DOM event in case specific pages want to listen
          try {
            window.dispatchEvent(new CustomEvent('edvora:new-notification', { detail: notif }));
          } catch (e) {}
        });

        if (hasNew) {
          playNotificationSound(playSoundType);

          // If current page is /notifications, reload to show latest cards
          if (window.location.pathname.endsWith('/notifications') || window.location.pathname.endsWith('/notifications/')) {
            // Optional: refresh after brief delay so user sees toast
            // location.reload();
          }
        }
      }

      if (data.timestamp) {
        lastPollTimestamp = data.timestamp;
      }
    })
    .catch(function (err) {
      isPolling = false;
      // Fail silently to avoid console spam when offline
    });
  }

  // --- CRUD Functions (Global Scope for inline HTML onclicks) ---
  window.markAsRead = function (notificationId) {
    var baseUrl = getNotificationBaseUrl();
    fetch(baseUrl + '/' + notificationId + '/read', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        var el = document.querySelector('[data-notification-id="' + notificationId + '"]');
        if (el) {
          el.classList.remove('unread');
          var badge = el.querySelector('.badge.bg-primary');
          if (badge && badge.textContent.trim() === 'New') badge.remove();
          var dot = el.querySelector('.badge.bg-primary.rounded-circle');
          if (dot) dot.remove();
          var title = el.querySelector('h6');
          if (title) title.classList.remove('fw-bold');
        }
        // Decrement badge count
        var badgeEl = document.querySelector('.edvora-nav-notif-badge');
        if (badgeEl) {
          var current = parseInt(badgeEl.textContent, 10) || 0;
          if (current > 0) updateBadges(current - 1);
        }
      }
    })
    .catch(function (err) { console.error(err); });
  };

  window.markAllAsRead = function () {
    var baseUrl = getNotificationBaseUrl();
    fetch(baseUrl + '/read-all', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        updateBadges(0);
        location.reload();
      }
    })
    .catch(function (err) { console.error(err); });
  };

  window.refreshNotifications = function () {
    location.reload();
  };

  window.deleteNotification = function (event, notificationId) {
    if (event) event.stopPropagation();
    var baseUrl = getNotificationBaseUrl();
    fetch(baseUrl + '/' + notificationId, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        var el = document.querySelector('[data-notification-id="' + notificationId + '"]');
        if (el) {
          el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
          el.style.opacity = '0';
          el.style.transform = 'translateX(20px)';
          setTimeout(function () {
            el.remove();
            var remaining = document.querySelectorAll('.notification-item');
            if (remaining.length === 0) location.reload();
          }, 260);
        }
      }
    })
    .catch(function (err) { console.error(err); });
  };

  window.deleteAllNotifications = function () {
    if (!confirm('Are you sure you want to delete all notifications? This cannot be undone.')) return;
    var baseUrl = getNotificationBaseUrl();
    fetch(baseUrl + '/delete-all', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        updateBadges(0);
        location.reload();
      }
    })
    .catch(function (err) { console.error(err); });
  };

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // --- Initialization ---
  document.addEventListener('DOMContentLoaded', function () {
    // Request permission when user clicks notification bell in navbar
    var navBell = document.querySelector('.edvora-nav-notif-link');
    if (navBell) {
      navBell.addEventListener('click', function () {
        requestBrowserPermission();
      });
    }

    // Pre-populate seen IDs from existing DOM cards on /notifications page
    document.querySelectorAll('[data-notification-id]').forEach(function (el) {
      var id = parseInt(el.getAttribute('data-notification-id'), 10);
      if (id) seenNotificationIds.add(id);
    });

    // Start polling immediately and then every POLL_INTERVAL
    pollLiveCheck();
    setInterval(pollLiveCheck, POLL_INTERVAL);
  });

})();
