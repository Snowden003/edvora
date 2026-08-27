// Courses Page JavaScript
document.addEventListener('DOMContentLoaded', function () {
    initSearch();
});

// Initialize search with debounce and AJAX live search
function initSearch() {
    const searchInput = document.getElementById('searchCourses');
    const filterForm  = document.getElementById('filterForm');
    const gridContainer = document.getElementById('courses-grid-container');

    if (!filterForm || !gridContainer) return;

    let searchTimeout;

    // Intercept form submission
    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchFilteredCourses();
    });

    // Handle input on search bar
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchFilteredCourses();
            }, 500);
        });
    }

    // Intercept filter pill clicks
    const filterTabsWrapper = document.querySelector('.courses-filter-tabs-wrapper');
    if (filterTabsWrapper) {
        filterTabsWrapper.addEventListener('click', function(e) {
            const pill = e.target.closest('.filter-tab-pill');
            if (pill) {
                e.preventDefault();
                // Update the hidden filter input
                const url = new URL(pill.href);
                const filterVal = url.searchParams.get('filter') || 'all';
                const activeFilterInput = document.getElementById('activeFilterInput');
                if (activeFilterInput) activeFilterInput.value = filterVal;
                
                // We should update the active state visually immediately for responsiveness
                document.querySelectorAll('.filter-tab-pill').forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                
                fetchFilteredCourses(url.toString());
            }
        });
    }

    // Handle change events on the form's select/radio inputs
    filterForm.addEventListener('change', function(e) {
        // If it's the search input, let the input event handler handle it with debounce
        if (e.target !== searchInput) {
            fetchFilteredCourses();
        }
    });

    // Intercept pagination clicks inside the grid container
    gridContainer.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            fetchFilteredCourses(link.href);
        }
        
        // Also intercept reset filters if it's rendered inside the grid container
        const clearBtn = e.target.closest('.clear-filters-btn');
        if (clearBtn) {
            e.preventDefault();
            // Reset form
            filterForm.reset();
            const url = new URL(clearBtn.href);
            fetchFilteredCourses(url.toString());
        }
    });

    function fetchFilteredCourses(fetchUrl = null) {
        const url = fetchUrl ? new URL(fetchUrl) : new URL(filterForm.action);
        
        if (!fetchUrl) {
            const formData = new FormData(filterForm);
            for (const [key, value] of formData.entries()) {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            }
        }

        // Add a visual loading state
        gridContainer.style.opacity = '0.5';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            gridContainer.innerHTML = html;
            gridContainer.style.opacity = '1';
            
            // Update URL bar
            window.history.pushState({}, '', url);
        })
        .catch(err => {
            console.error('Error fetching courses:', err);
            gridContainer.style.opacity = '1';
        });
    }
}

