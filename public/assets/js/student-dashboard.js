// Student Dashboard JavaScript - UI only (data rendered server-side)
document.addEventListener('DOMContentLoaded', function () {
    initSearch();
    initSidebar();
});

// --- Sidebar Toggle Functionality ---
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const mobileToggle = document.getElementById('sidebarMobileToggle');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');

    if (!sidebar) return;

    // Desktop: load saved collapsed state
    if (sidebarCollapse) {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent?.classList.add('sidebar-collapsed');
        }

        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            mainContent?.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Mobile: open sidebar via hamburger button
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
        });
    }

    // Mobile: close sidebar via overlay click
    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // Mobile: close sidebar when a nav link is clicked
    sidebar.querySelectorAll('.edvora-nav-link, .edvora-sublink').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        });
    });
}

function initSearch() {
    const searchInput = document.getElementById('searchCourses');
    const filterForm  = document.getElementById('filterForm');
    if (!searchInput || !filterForm) return;

    let timeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => filterForm.submit(), 400);
    });
}

function saveProfile() {
    bootstrap.Modal.getInstance(document.getElementById('profileModal'))?.hide();
    showToast('Profile updated!', 'success');
}

function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1055';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;

    container.appendChild(toast);
    new bootstrap.Toast(toast).show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}

// --- Notifications modal ---
const notifBadge    = document.getElementById('notifBadge');
const notifCountText = document.getElementById('notifCountText');

let notifications = [];

function renderNotifications() {
    const list = document.getElementById('notificationsList');
    if (!list) return;
    list.innerHTML = '';

    notifications
        .slice()
        .sort((a, b) => (b.unread - a.unread) || (new Date(b.date) - new Date(a.date)))
        .forEach(n => {
            const item = document.createElement('div');
            item.className = 'list-group-item d-flex justify-content-between align-items-start';
            item.innerHTML = `
                <div class="ms-2 me-auto">
                    <div class="${n.unread ? 'fw-semibold' : ''}">${n.title}</div>
                    <div class="small text-muted">${n.body}</div>
                    <div class="small text-muted mt-1" style="font-size:.78rem">${n.date}</div>
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                    ${n.unread ? '<span class="badge bg-primary">New</span>' : ''}
                    <button class="btn btn-sm btn-outline-secondary" data-action="toggle" data-id="${n.id}">
                        ${n.unread ? 'Mark read' : 'Mark unread'}
                    </button>
                </div>`;
            list.appendChild(item);
        });

    const unread = notifications.filter(n => n.unread).length;
    if (notifBadge) {
        notifBadge.textContent = unread > 0 ? unread : '';
        notifBadge.style.display = unread > 0 ? 'inline-flex' : 'none';
    }
    if (notifCountText) {
        notifCountText.textContent = unread === 0 ? 'No unread' : unread + ' unread';
    }
}

document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[data-action="toggle"]');
    if (!btn) return;
    const id = Number(btn.getAttribute('data-id'));
    notifications = notifications.map(n => n.id === id ? { ...n, unread: !n.unread } : n);
    renderNotifications();
});

document.getElementById('btnMarkAllRead')?.addEventListener('click', function () {
    notifications = notifications.map(n => ({ ...n, unread: false }));
    renderNotifications();
});

renderNotifications();

// --- Cancel Enrollment Request ---
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.cancel-request-btn');
    if (!btn) return;

    if (!confirm('Are you sure you want to cancel this enrollment request?')) return;

    var url = btn.dataset.url;
    var listItem = btn.closest('.list-group-item');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    var meta = document.querySelector('meta[name="csrf-token"]');
    var token = meta ? meta.getAttribute('content') : '';

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.status === 'cancelled') {
            listItem.style.transition = 'opacity 0.3s, transform 0.3s';
            listItem.style.opacity = '0';
            listItem.style.transform = 'translateX(20px)';
            setTimeout(function () { listItem.remove(); }, 300);
            showToast(data.message, 'success');
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-x-lg me-1"></i>Cancel';
            showToast(data.message || 'Something went wrong.', 'danger');
        }
    })
    .catch(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-x-lg me-1"></i>Cancel';
        showToast('An error occurred. Please try again.', 'danger');
    });
});

// --- Dashboard Notification: Mark single as read ---
document.addEventListener('click', function (e) {
    var item = e.target.closest('.notif-item');
    if (!item || !item.classList.contains('unread')) return;

    var url = item.dataset.markUrl;
    if (!url) return;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            item.classList.remove('unread');
            var badge = item.querySelector('.notif-new-badge');
            if (badge) badge.remove();

            // Update unread count badge
            var countBadge = document.querySelector('.notif-unread-badge');
            if (countBadge) {
                var current = parseInt(countBadge.textContent) - 1;
                if (current <= 0) {
                    countBadge.remove();
                } else {
                    countBadge.textContent = current;
                }
            }
        }
    })
    .catch(function (err) { console.error('Error marking notification:', err); });
});

// --- Dashboard Notification: Mark all as read ---
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.btn-mark-all-read');
    if (!btn) return;

    var url = btn.dataset.url;
    if (!url) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Marking...';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.success) {
            document.querySelectorAll('.notif-item.unread').forEach(function (el) {
                el.classList.remove('unread');
                var badge = el.querySelector('.notif-new-badge');
                if (badge) badge.remove();
            });
            var countBadge = document.querySelector('.notif-unread-badge');
            if (countBadge) countBadge.remove();
            btn.closest('.text-center').remove();
        }
    })
    .catch(function (err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-all me-1"></i>Mark All as Read';
        console.error('Error:', err);
    });
});
