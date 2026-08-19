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

            // Filters Interaction
            const filterBtns = document.querySelectorAll('.filter-btn');
            const examCards = document.querySelectorAll('.exam-card-wrapper');
            const searchInput = document.getElementById('examSearch');

            function filterExams() {
                const activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
                const searchQuery = searchInput.value.toLowerCase();

                examCards.forEach(card => {
                    const status = card.getAttribute('data-status');
                    const title = card.querySelector('.exam-title').innerText.toLowerCase();
                    const course = card.querySelector('.exam-course-tag').innerText.toLowerCase();

                    const matchesFilter = (activeFilter === 'all' || status === activeFilter);
                    const matchesSearch = (title.includes(searchQuery) || course.includes(searchQuery));

                    if (matchesFilter && matchesSearch) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            }

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterExams();
                });
            });

            searchInput.addEventListener('input', filterExams);
        });