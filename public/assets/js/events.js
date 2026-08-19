// Events Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    initializeCategoryFilters();
    initializeSearch();
});

let allEvents = [];
let displayedEvents = [];
let eventsPerPage = 9;
let currentPage = 1;
let eventsApiUrl = '';

// Load events from API
async function loadEvents() {
    const grid = document.getElementById('eventsGrid');
    if (grid) {
        eventsApiUrl = grid.dataset.eventsUrl || '/api/events';
    }

    try {
        const response = await fetch(eventsApiUrl);
        if (!response.ok) {
            throw new Error('Failed to load events');
        }
        allEvents = await response.json();
        displayedEvents = allEvents.slice(0, eventsPerPage);
        renderEvents();

        // Show/hide empty state
        const emptyState = document.getElementById('emptyState');
        const loadMoreBtn = document.getElementById('loadMoreEvents');
        if (emptyState) {
            if (allEvents.length === 0) {
                emptyState.style.display = 'block';
                if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                if (loadMoreBtn) loadMoreBtn.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error loading events:', error);
        showToast('Failed to load events. Please try again later.', 'error');
    }
}

// Render events grid
function renderEvents() {
    const grid = document.getElementById('eventsGrid');
    if (!grid) return;

    grid.innerHTML = displayedEvents.map(event => `
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm event-card course-card">
                ${event.featured ? '<div class="position-absolute top-0 start-0 m-3 z-3"><span class="badge bg-danger">Featured</span></div>' : ''}
                <div class="position-relative">
                    <img src="${event.image}" class="card-img-top" alt="${event.title}" style="height: 200px; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-primary">${formatCategory(event.category)}</span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: #1F8FFF;">${event.title}</h5>
                    <p class="text-muted mb-3" style="font-size: 0.9rem;">${event.description.substring(0, 100)}...</p>
                    
                    <div class="row g-2 mb-3 small">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 me-2 text-primary"></i>
                                <span>${formatDate(event.date)}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-2 text-primary"></i>
                                <span>${event.duration}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-primary"></i>
                                <span>${event.location}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people me-2 text-primary"></i>
                                <span>${event.attendees} attendees</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="/events/${event.slug}" class="btn btn-primary" style="background-color: #1F8FFF; border-color: #1F8FFF;">
                            Register Now
                        </a>
                        <a href="/events/${event.slug}" class="btn btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    // Update load more button
    updateLoadMoreButton();
}

// Helper function to update empty state visibility
function updateEmptyState(eventCount) {
    const emptyState = document.getElementById('emptyState');
    const loadMoreBtn = document.getElementById('loadMoreEvents');
    const grid = document.getElementById('eventsGrid');

    if (emptyState) {
        if (eventCount === 0) {
            emptyState.style.display = 'block';
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            if (grid) grid.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            if (grid) grid.style.display = 'flex';
        }
    }
}

// Initialize category filters
function initializeCategoryFilters() {
    const categoryButtons = document.querySelectorAll('[data-category]');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter events
            const category = this.getAttribute('data-category');
            filterEventsByCategory(category);
        });
    });
}

// Initialize search
function initializeSearch() {
    const searchInput = document.getElementById('eventSearch');
    if (!searchInput) return;

    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    });
}

// Filter events by category
function filterEventsByCategory(category) {
    let filteredEvents = category === 'all' ? allEvents : allEvents.filter(event => event.category === category);

    // Reset pagination
    currentPage = 1;
    displayedEvents = filteredEvents.slice(0, eventsPerPage);
    renderEvents();

    // Update empty state visibility
    updateEmptyState(filteredEvents.length);

    // Clear search when filtering by category
    const searchInput = document.getElementById('eventSearch');
    if (searchInput) {
        searchInput.value = '';
    }
}

// Apply search filters
function applyFilters() {
    const searchTerm = document.getElementById('eventSearch')?.value.toLowerCase() || '';
    const activeCategory = document.querySelector('[data-category].active')?.getAttribute('data-category') || 'all';

    let filteredEvents = activeCategory === 'all' ? allEvents : allEvents.filter(event => event.category === activeCategory);

    if (searchTerm) {
        filteredEvents = filteredEvents.filter(event =>
            event.title.toLowerCase().includes(searchTerm) ||
            event.description.toLowerCase().includes(searchTerm) ||
            event.location.toLowerCase().includes(searchTerm)
        );
    }

    // Reset pagination
    currentPage = 1;
    displayedEvents = filteredEvents.slice(0, eventsPerPage);
    renderEvents();

    // Update empty state visibility
    updateEmptyState(filteredEvents.length);
}

// Load more events
function loadMoreEvents() {
    const searchTerm = document.getElementById('eventSearch')?.value.toLowerCase() || '';
    const activeCategory = document.querySelector('[data-category].active')?.getAttribute('data-category') || 'all';
    
    let filteredEvents = activeCategory === 'all' ? allEvents : allEvents.filter(event => event.category === activeCategory);
    
    if (searchTerm) {
        filteredEvents = filteredEvents.filter(event => 
            event.title.toLowerCase().includes(searchTerm) ||
            event.description.toLowerCase().includes(searchTerm) ||
            event.location.toLowerCase().includes(searchTerm) ||
            event.speakers.some(speaker => speaker.toLowerCase().includes(searchTerm))
        );
    }
    
    currentPage++;
    const startIndex = (currentPage - 1) * eventsPerPage;
    const endIndex = startIndex + eventsPerPage;
    const newEvents = filteredEvents.slice(startIndex, endIndex);
    
    displayedEvents = [...displayedEvents, ...newEvents];
    renderEvents();
}

// Update load more button
function updateLoadMoreButton() {
    const loadMoreBtn = document.getElementById('loadMoreEvents');
    if (!loadMoreBtn) return;

    const searchTerm = document.getElementById('eventSearch')?.value.toLowerCase() || '';
    const activeCategory = document.querySelector('[data-category].active')?.getAttribute('data-category') || 'all';
    
    let filteredEvents = activeCategory === 'all' ? allEvents : allEvents.filter(event => event.category === activeCategory);
    
    if (searchTerm) {
        filteredEvents = filteredEvents.filter(event => 
            event.title.toLowerCase().includes(searchTerm) ||
            event.description.toLowerCase().includes(searchTerm) ||
            event.location.toLowerCase().includes(searchTerm) ||
            event.speakers.some(speaker => speaker.toLowerCase().includes(searchTerm))
        );
    }

    if (displayedEvents.length >= filteredEvents.length) {
        loadMoreBtn.style.display = 'none';
    } else {
        loadMoreBtn.style.display = 'block';
        loadMoreBtn.onclick = loadMoreEvents;
    }
}

// Show event details modal
function showEventDetails(eventId) {
    const event = allEvents.find(e => e.id === eventId);
    if (!event) return;

    const modalBody = document.getElementById('eventModalBody');
    if (!modalBody) return;

    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-5">
                <img src="${event.image}" alt="${event.title}" class="img-fluid rounded mb-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Event Details</h6>
                        <div class="row g-2 small">
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Date</small>
                                        <strong>${formatDate(event.date)}${event.endDate ? ' - ' + formatDate(event.endDate) : ''}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock me-2 text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Duration</small>
                                        <strong>${event.duration}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt me-2 text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Location</small>
                                        <strong>${event.location}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people me-2 text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Attendees</small>
                                        <strong>${event.attendees}+</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <div class="fw-bold fs-4" style="color: #1F8FFF;">
                                ${event.price === 0 ? 'Free Event' : `$${event.price}`}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">${formatCategory(event.category)}</span>
                    ${event.featured ? '<span class="badge bg-danger">Featured</span>' : ''}
                </div>
                
                <h4 class="fw-bold mb-3" style="color: #1F8FFF;">${event.title}</h4>
                <p class="text-muted mb-4">${event.description}</p>
                
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Speakers</h6>
                    <div class="d-flex flex-wrap gap-2">
                        ${event.speakers.map(speaker => 
                            `<span class="badge bg-light text-dark">${speaker}</span>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Agenda</h6>
                    <ul class="list-unstyled">
                        ${event.agenda.map(item => 
                            `<li class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>${item}</li>`
                        ).join('')}
                    </ul>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Registration includes:</strong> Access to all sessions, networking opportunities, digital materials, and certificate of attendance.
                </div>
            </div>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
}

// Utility functions
function formatCategory(category) {
    const categories = {
        'workshop': 'Workshop',
        'webinar': 'Webinar',
        'conference': 'Conference',
        'networking': 'Networking',
        'bootcamp': 'Bootcamp'
    };
    return categories[category] || category;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

// Add hover effects to event cards
// document.addEventListener('DOMContentLoaded', function() {
//     const style = document.createElement('style');
//     style.textContent = `
//         .event-card {
//             transition: transform 0.3s ease, box-shadow 0.3s ease;
//             cursor: pointer;
//         }
        
//         .event-card:hover {
//             transform: translateY(-5px);
//             box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
//         }
        
//         .event-card:hover .btn {
//             background-color: #1F8FFF !important;
//             border-color: #1F8FFF !important;
//         }
//     `;
//     document.head.appendChild(style);
// });

// Generate event slug for URL
function getEventSlug(event) {
    return event.title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
}

// Navigate to event detail page
function goToEventDetail(eventSlug) {
    window.location.href = `/events/${eventSlug}`;
}

// Register for event function
async function registerForEvent(eventId) {
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const response = await fetch(`/events/${eventId}/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (response.ok) {
            showToast(data.message || 'Registration successful! Check your email for confirmation.', 'success');
        } else {
            if (response.status === 401) {
                // Not authenticated - redirect to login
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
            } else {
                showToast(data.message || 'Registration failed. Please try again.', 'error');
            }
        }
    } catch (error) {
        console.error('Registration error:', error);
        showToast('An error occurred. Please try again.', 'error');
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    // Add to page
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    toastContainer.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove from DOM after hiding
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
