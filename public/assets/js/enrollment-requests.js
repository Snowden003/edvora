// Enrollment Requests JS - Teacher Panel

function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function viewStudentProfile(id, type) {
    var modalEl = document.getElementById('studentProfileModal');
    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    var content = document.getElementById('studentProfileContent');
    content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    modal.show();

    var url = (type === 'user')
        ? '/teacher/students/' + id + '/profile'
        : '/teacher/enrollment-requests/' + id + '/student';

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        var profileDetails = '';
        if (data.profile_complete) {
            profileDetails = '<div class="mt-3 text-start">' +
                '<hr>' +
                '<h6 class="fw-bold mb-2"><i class="bi bi-person-vcard me-1"></i> Profile Details</h6>' +
                (data.father_name ? '<p class="mb-1 small"><strong>Father:</strong> ' + data.father_name + '</p>' : '') +
                (data.national_id ? '<p class="mb-1 small"><strong>National ID:</strong> ' + data.national_id + '</p>' : '') +
                (data.phone ? '<p class="mb-1 small"><strong>Phone:</strong> ' + data.phone + '</p>' : '') +
                (data.education ? '<p class="mb-1 small"><strong>Education:</strong> ' + data.education + '</p>' : '') +
                (data.school ? '<p class="mb-1 small"><strong>School:</strong> ' + data.school + '</p>' : '') +
                (data.address ? '<p class="mb-1 small"><strong>Address:</strong> ' + data.address + '</p>' : '') +
            '</div>';
        } else {
            profileDetails = '<div class="mt-3"><span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Profile Incomplete</span></div>';
        }

        content.innerHTML = '<div class="student-profile-card">' +
            '<div class="text-center mb-4">' +
                '<img src="' + data.avatar + '" alt="' + data.name + '" class="student-avatar mb-3">' +
                '<h4 class="fw-bold mb-1">' + data.name + '</h4>' +
                '<p class="text-muted mb-0">' + (data.email || '') + '</p>' +
            '</div>' +
            (data.bio ? '<p class="text-center text-muted mb-4">' + data.bio + '</p>' : '') +
            '<div class="row g-3 mb-3">' +
                '<div class="col-4"><div class="student-stat"><h5>' + data.xp + '</h5><small>XP</small></div></div>' +
                '<div class="col-4"><div class="student-stat"><h5>' + data.enrolled_courses + '</h5><small>Courses</small></div></div>' +
                '<div class="col-4"><div class="student-stat"><h5>' + data.joined_at + '</h5><small>Joined</small></div></div>' +
            '</div>' +
            (data.department ? '<div class="text-center mb-2"><span class="badge bg-light text-dark px-3 py-2"><i class="bi bi-building me-1"></i>' + data.department + '</span></div>' : '') +
            profileDetails +
        '</div>';
    })
    .catch(function () {
        content.innerHTML = '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i><p>Failed to load student profile.</p></div>';
    });
}

function approveRequest(requestId) {
    if (!confirm('Are you sure you want to approve this student? An approval email will be sent.')) return;

    var url = '/teacher/enrollment-requests/' + requestId + '/approve';
    var card = document.getElementById('request-' + requestId);

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
        if (data.status === 'approved') {
            showToast(data.message, 'success');
            // Update the card UI
            var actionsCol = card.querySelector('.col-md-3.text-end');
            actionsCol.innerHTML = '<small class="text-success"><i class="bi bi-check-circle me-1"></i>Approved just now</small>';
            var statusCol = card.querySelector('.col-md-2.text-center');
            statusCol.innerHTML = '<span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Approved</span>';
            card.classList.add('approved');
        } else {
            showToast(data.message || 'Something went wrong.', 'danger');
        }
    })
    .catch(function () {
        showToast('An error occurred. Please try again.', 'danger');
    });
}

function openRejectModal(requestId) {
    document.getElementById('rejectRequestId').value = requestId;
    document.getElementById('rejectionReason').value = '';
    var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

function submitRejection() {
    var requestId = document.getElementById('rejectRequestId').value;
    var reason = document.getElementById('rejectionReason').value.trim();

    if (!reason || reason.length < 5) {
        showToast('Please provide a reason (at least 5 characters).', 'warning');
        return;
    }

    var url = '/teacher/enrollment-requests/' + requestId + '/reject';
    var card = document.getElementById('request-' + requestId);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ reason: reason }),
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.status === 'rejected') {
            showToast(data.message, 'success');
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
            modal.hide();
            // Update the card UI
            var actionsCol = card.querySelector('.col-md-3.text-end');
            actionsCol.innerHTML = '<small class="text-muted"><i class="bi bi-info-circle me-1"></i>' + reason.substring(0, 40) + '...</small>';
            var statusCol = card.querySelector('.col-md-2.text-center');
            statusCol.innerHTML = '<span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Rejected</span>';
            card.classList.add('rejected');
        } else {
            showToast(data.message || 'Something went wrong.', 'danger');
        }
    })
    .catch(function () {
        showToast('An error occurred. Please try again.', 'danger');
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

function switchEnrollmentTab(tabName) {
    var tabInput = document.getElementById('filterTabInput');
    if (tabInput) {
        tabInput.value = tabName;
    }
    document.querySelectorAll('.enroll-nav-tab').forEach(function (btn) {
        if (btn.getAttribute('data-tab') === tabName) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    var enrolledPane = document.getElementById('pane-enrolled');
    var requestsPane = document.getElementById('pane-requests');
    if (enrolledPane && requestsPane) {
        if (tabName === 'enrolled') {
            enrolledPane.classList.remove('d-none');
            requestsPane.classList.add('d-none');
        } else {
            enrolledPane.classList.add('d-none');
            requestsPane.classList.remove('d-none');
        }
    }
    var url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}
