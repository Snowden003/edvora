// Sidebar Toggle for Mobile
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar-premium');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                });
            }

            // Desktop Sidebar Collapse
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const wrapper = document.querySelector('.dashboard-wrapper');
            if (sidebarCollapse && sidebar && wrapper) {
                // Load saved state
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    wrapper.classList.add('sidebar-collapsed');
                }

                sidebarCollapse.addEventListener('click', function () {
                    sidebar.classList.toggle('collapsed');
                    wrapper.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function (event) {
                if (!sidebar || !toggleBtn) return;
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickInsideToggle = toggleBtn.contains(event.target);

                if (!isClickInsideSidebar && !isClickInsideToggle && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            });
        });