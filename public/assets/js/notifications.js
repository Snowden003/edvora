// Notifications Page

function getNotificationBaseUrl() {
  var role = document.body.dataset.userRole || 'student';
  return '/' + role + '/notifications';
}

function getCsrfToken() {
  var meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
}

function markAsRead(notificationId) {
  var baseUrl = getNotificationBaseUrl();
  fetch(baseUrl + '/' + notificationId + '/read', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken()
    }
  })
  .then(function (response) { return response.json(); })
  .then(function (data) {
    if (data.success) {
      var element = document.querySelector('[data-notification-id="' + notificationId + '"]');
      if (element) {
        element.classList.remove('unread');
        var badge = element.querySelector('.badge.bg-primary');
        if (badge && badge.textContent.trim() === 'New') badge.remove();
        var unreadDot = element.querySelector('.badge.bg-primary.rounded-circle');
        if (unreadDot) unreadDot.remove();
        var title = element.querySelector('h6');
        if (title) title.classList.remove('fw-bold');
      }
    }
  })
  .catch(function (error) { console.error('Error:', error); });
}

function markAllAsRead() {
  var baseUrl = getNotificationBaseUrl();
  fetch(baseUrl + '/read-all', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken()
    }
  })
  .then(function (response) { return response.json(); })
  .then(function (data) {
    if (data.success) location.reload();
  })
  .catch(function (error) { console.error('Error:', error); });
}

function refreshNotifications() {
  location.reload();
}

function deleteNotification(event, notificationId) {
  event.stopPropagation();
  var baseUrl = getNotificationBaseUrl();
  fetch(baseUrl + '/' + notificationId, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken()
    }
  })
  .then(function (response) { return response.json(); })
  .then(function (data) {
    if (data.success) {
      var element = document.querySelector('[data-notification-id="' + notificationId + '"]');
      if (element) {
        element.style.transition = 'opacity 0.25s ease';
        element.style.opacity = '0';
        setTimeout(function () {
          element.remove();
          var remaining = document.querySelectorAll('.notification-item');
          if (remaining.length === 0) location.reload();
        }, 260);
      }
    }
  })
  .catch(function (error) { console.error('Error:', error); });
}

function deleteAllNotifications() {
  if (!confirm('Are you sure you want to delete all notifications? This cannot be undone.')) return;
  var baseUrl = getNotificationBaseUrl();
  fetch(baseUrl + '/delete-all', {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken()
    }
  })
  .then(function (response) { return response.json(); })
  .then(function (data) {
    if (data.success) location.reload();
  })
  .catch(function (error) { console.error('Error:', error); });
}

var _lastUnreadCount = parseInt(
  (document.querySelector('.badge.bg-danger') || {}).textContent || '0'
);

function pollUnreadCount() {
  var baseUrl = getNotificationBaseUrl();
  fetch(baseUrl + '/unread-count')
    .then(function (response) { return response.json(); })
    .then(function (data) {
      var count = data.count || 0;

      // Update all badge elements in sidebar/navbar
      document.querySelectorAll('[data-notif-badge]').forEach(function (el) {
        if (count > 0) {
          el.textContent = count;
          el.style.display = '';
        } else {
          el.style.display = 'none';
        }
      });

      // Update page header badge
      var headerBadge = document.querySelector('.badge.bg-danger');
      if (headerBadge) {
        if (count > 0) {
          headerBadge.textContent = count + ' unread';
          headerBadge.style.display = '';
        } else {
          headerBadge.style.display = 'none';
        }
      }

      // Browser push notification when new notification arrives
      if (count > _lastUnreadCount) {
        if ('Notification' in window && Notification.permission === 'granted') {
          new Notification('Edvora - New Notification', {
            body: 'You have ' + count + ' unread notification(s).',
            icon: '/favicon.ico'
          });
        }
        // Reload list if on notifications page
        if (window.location.pathname.includes('/notifications')) {
          location.reload();
        }
      }

      _lastUnreadCount = count;
    })
    .catch(function () {});
}

// Poll every 10 seconds
setInterval(pollUnreadCount, 10000);
pollUnreadCount();

// Request browser notification permission
if ('Notification' in window && Notification.permission === 'default') {
  Notification.requestPermission();
}
