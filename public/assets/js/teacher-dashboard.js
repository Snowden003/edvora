// Teacher Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function () {
    initSidebar();
    initCharts();
    initNavigation();
});

// --- Sidebar Toggle Functionality ---
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const mobileToggle = document.getElementById('sidebarMobileToggle');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');

    if (!sidebar || sidebar.dataset.sbInit === '1') return;
    sidebar.dataset.sbInit = '1';

    // Desktop: load saved collapsed state
    if (sidebarCollapse) {
        const isCollapsed = localStorage.getItem('teacherSidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent?.classList.add('sidebar-collapsed');
        }

        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            mainContent?.classList.toggle('sidebar-collapsed');
            localStorage.setItem('teacherSidebarCollapsed', sidebar.classList.contains('collapsed'));
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

    // Mobile: close sidebar when any nav link or action is clicked
    sidebar.querySelectorAll('.edvora-nav-link, .edvora-sublink, .edvora-sublink-all, .edvora-course-title, .edvora-action-btn, .edvora-view-all-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        });
    });
}
 
// Initialize Charts
function initCharts() {
    const chartEl = document.getElementById('engagementChart');
    if (!chartEl) return;

    const data = window.teacherDashboardData || {};
    const active = data.courseStatusActive || 0;
    const draft = data.courseStatusDraft || 0;
    const completed = data.courseStatusCompleted || 0;

    const engagementCtx = chartEl.getContext('2d');
    new Chart(engagementCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Draft', 'Completed'],
            datasets: [{
                data: [active, draft, completed],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// Navigation
function initNavigation() {
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();

                // Remove active class from all links
                navLinks.forEach(l => l.classList.remove('active'));

                // Add active class to clicked link
                this.classList.add('active');

                // Scroll to section
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
}

// Course Functions
function viewCourse(courseId) {

    showToast(`Viewing course ${courseId}`, 'info');
}

function editCourse(courseId) {

    showToast(`Editing course ${courseId}`, 'info');
}

function viewStudents(courseId) {

    showToast(`Viewing students for course ${courseId}`, 'info');
}

function duplicateCourse(courseId) {

    showToast('Course duplicated successfully!', 'success');
}

// Schedule Functions
function scheduleClass() {
    const form = document.getElementById('scheduleClassForm');
    const formData = new FormData(form);

    const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleClassModal'));
    modal.hide();

    showToast('Class scheduled successfully!', 'success');
}

function startClass(classId) {

    showToast('Starting live class...', 'info');
    // In a real app, this would open the live class interface
}

function viewClassDetails(classId) {

    showToast(`Viewing details for class ${classId}`, 'info');
}

// Review Functions
function reviewAssignment(reviewId) {

    showToast('Opening assignment for review...', 'info');
    // In a real app, this would open the assignment review interface
}

// Notification Functions
function markAllRead() {

    showToast('All notifications marked as read', 'success');
}

// Utility Functions
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '1055';
        document.body.appendChild(toastContainer);
    }

    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
