// Your Courses JavaScript
document.addEventListener('DOMContentLoaded', function () {
    initSidebar();
    initSearchDebounce();
    animateProgressBars();
});

// Debounced search — submit form after user stops typing
function initSearchDebounce() {
    const searchInput = document.getElementById('searchCourses');
    const form = document.getElementById('filterForm');
    if (!searchInput || !form) return;

    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 500);
    });
}

// Animate progress bars on load
function animateProgressBars() {
    document.querySelectorAll('.progress-bar-fill').forEach(bar => {
        const target = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = target; }, 150);
    });
}

// Sidebar Logic
function initSidebar() {
    const sidebarToggle = document.getElementById('sidebarMobileToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');

    // Open sidebar
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.add('active');
            if (sidebarOverlay) sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    // Close sidebar via close button
    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.remove('active');
            if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Close sidebar via overlay
    if (sidebarOverlay && sidebar) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && (!sidebarToggle || !sidebarToggle.contains(e.target))) {
                sidebar.classList.remove('active');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });
}
