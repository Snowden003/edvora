// Course Detail JavaScript
document.addEventListener('DOMContentLoaded', function () {
    initCurriculumToggle();
    initWishlist();
    initDirectEnroll();
});

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function initCurriculumToggle() {
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (!icon) return;
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            icon.className = isExpanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
        });
    });
}

function initDirectEnroll() {
    const enrollBtn = document.getElementById('enrollBtn');
    if (!enrollBtn) return;

    enrollBtn.addEventListener('click', function () {
        const url = this.dataset.url;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enrolling...';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.status === 'profile_incomplete') {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-mortarboard me-2"></i>Enroll Now';
                showToast(data.message + ' Redirecting to your profile...', 'warning');
                setTimeout(function () { window.location.href = data.redirect; }, 2000);
            } else if (data.status === 'enrolled') {
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Enrolled Successfully';
                btn.classList.remove('enroll-btn');
                btn.classList.add('btn-success');
                btn.disabled = true;
                showToast(data.message, 'success');
                setTimeout(function () { window.location.reload(); }, 1500);
            } else if (data.status === 'already_enrolled') {
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Already Enrolled';
                btn.classList.remove('enroll-btn');
                btn.classList.add('btn-success');
                btn.disabled = true;
                showToast(data.message, 'info');
            } else if (data.status === 'course_completed') {
                btn.disabled = true;
                btn.classList.remove('enroll-btn', 'btn-enroll-primary');
                btn.classList.add('btn-secondary');
                btn.innerHTML = '<i class="bi bi-calendar-x-fill me-2"></i>Enrollment Closed';
                showToast(data.message, 'warning');
            } else if (data.status === 'course_full') {
                btn.disabled = true;
                btn.classList.remove('enroll-btn', 'btn-enroll-primary');
                btn.classList.add('btn-secondary');
                btn.innerHTML = '<i class="bi bi-people-fill me-2"></i>Course Full';
                showToast(data.message, 'warning');
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-mortarboard me-2"></i>Enroll Now';
                showToast(data.message || 'Something went wrong.', 'danger');
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-mortarboard me-2"></i>Enroll Now';
            showToast('An error occurred. Please try again.', 'danger');
        });
    });
}

function initWishlist() {
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (!wishlistBtn) return;

    wishlistBtn.addEventListener('click', function () {
        const url = this.dataset.url;
        const btn = this;
        btn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            btn.disabled = false;
            if (data.status === 'added') {
                btn.dataset.wishlisted = '1';
                btn.innerHTML = '<i class="bi bi-heart-fill me-2"></i>In Wishlist';
                btn.classList.remove('btn-outline-light');
                btn.classList.add('btn-success');
                showToast(data.message, 'success');
            } else if (data.status === 'removed') {
                btn.dataset.wishlisted = '0';
                btn.innerHTML = '<i class="bi bi-heart me-2"></i>Add to Wishlist';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-light');
                showToast(data.message, 'info');
            } else {
                showToast(data.message || 'Something went wrong.', 'danger');
            }
        })
        .catch(function () {
            btn.disabled = false;
            showToast('An error occurred. Please try again.', 'danger');
        });
    });
}

function showToast(message, type) {
    var container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }

    var toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-' + type + ' border-0';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = '<div class="d-flex"><div class="toast-body">' + message + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';

    container.appendChild(toast);
    new bootstrap.Toast(toast).show();

    toast.addEventListener('hidden.bs.toast', function () { toast.remove(); });
}
