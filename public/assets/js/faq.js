// FAQ Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initFAQSearch();
    initCategoryFilters();
});

// Initialize FAQ Search
function initFAQSearch() {
    const searchInput = document.getElementById('faqSearch');
    if (!searchInput) return;

    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterFAQs(this.value.toLowerCase());
        }, 300);
    });
}

// Initialize Category Filters
function initCategoryFilters() {
    const categoryButtons = document.querySelectorAll('[data-category]');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter FAQs
            const category = this.getAttribute('data-category');
            filterFAQsByCategory(category);
        });
    });
}

// Filter FAQs by search term
function filterFAQs(searchTerm) {
    const faqItems = document.querySelectorAll('.faq-item');
    let visibleCount = 0;
    
    faqItems.forEach(item => {
        const question = item.querySelector('.accordion-button').textContent.toLowerCase();
        const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show no results message if needed
    showNoResultsMessage(visibleCount === 0);
}

// Filter FAQs by category
function filterFAQsByCategory(category) {
    const faqItems = document.querySelectorAll('.faq-item');
    let visibleCount = 0;
    
    faqItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        
        if (category === 'all' || itemCategory === category) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Clear search when filtering by category
    const searchInput = document.getElementById('faqSearch');
    if (searchInput) {
        searchInput.value = '';
    }
    
    showNoResultsMessage(visibleCount === 0);
}

// Show/hide no results message
function showNoResultsMessage(show) {
    let noResultsDiv = document.getElementById('noResults');
    
    if (show && !noResultsDiv) {
        noResultsDiv = document.createElement('div');
        noResultsDiv.id = 'noResults';
        noResultsDiv.className = 'text-center py-5';
        noResultsDiv.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <i class="bi bi-search fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No Results Found</h4>
                    <p class="text-muted">Try adjusting your search terms or browse different categories.</p>
                    <button class="btn btn-primary" onclick="clearSearch()" style="background-color: #1F8FFF; border-color: #1F8FFF;">
                        Clear Search
                    </button>
                </div>
            </div>
        `;
        
        const accordion = document.getElementById('faqAccordion');
        accordion.parentNode.insertBefore(noResultsDiv, accordion.nextSibling);
    } else if (!show && noResultsDiv) {
        noResultsDiv.remove();
    }
}

// Clear search and show all FAQs
function clearSearch() {
    const searchInput = document.getElementById('faqSearch');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset to show all categories
    const allButton = document.querySelector('[data-category="all"]');
    if (allButton) {
        allButton.click();
    }
}

// Start live chat (placeholder function)
function startLiveChat() {
    window.location.href = '/contact';
}

// Smooth scroll to FAQ when coming from external links
function scrollToFAQ(faqId) {
    const faqElement = document.getElementById(faqId);
    if (faqElement) {
        faqElement.scrollIntoView({ behavior: 'smooth' });
        
        // Open the accordion item
        const button = faqElement.querySelector('.accordion-button');
        if (button && button.classList.contains('collapsed')) {
            button.click();
        }
    }
}

// Check URL for FAQ anchor and scroll to it
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#faq')) {
        setTimeout(() => {
            scrollToFAQ(hash.substring(1));
        }, 500);
    }
});

// Add click tracking for analytics (placeholder)
function trackFAQClick(question) {

    // In a real application, you would send this to your analytics service
}

// Add event listeners to track FAQ interactions
document.addEventListener('DOMContentLoaded', function() {
    const accordionButtons = document.querySelectorAll('.accordion-button');
    
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const question = this.textContent.trim();
            trackFAQClick(question);
        });
    });
});
