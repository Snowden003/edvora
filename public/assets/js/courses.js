// Courses Page JavaScript
document.addEventListener('DOMContentLoaded', function () {
    initSearch();
});

// Initialize search with debounce
function initSearch() {
    const searchInput = document.getElementById('searchCourses');
    const filterForm  = document.getElementById('filterForm');
    if (!searchInput || !filterForm) return;

    let searchTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => filterForm.submit(), 400);
    });
}
