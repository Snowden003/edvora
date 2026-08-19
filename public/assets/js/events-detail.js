// Events Detail Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initEventDetail();
});

// Event data database
const eventsData = {
    'global-tech-conference': {
        title: 'Global Tech Conference 2024',
        category: 'Conference',
        date: 'Dec 15-17, 2024',
        duration: '3 Days',
        location: 'Online & San Francisco',
        attendees: '2,500+',
        price: 'FREE',
        description: `
            <p>Join industry leaders and innovators for a 3-day conference featuring the latest trends in technology, AI, and digital transformation. This premium event brings together thought leaders, entrepreneurs, and tech enthusiasts from around the globe.</p>
            <p>Discover cutting-edge technologies, network with industry professionals, and gain insights that will shape the future of your career and business.</p>
            <p>The conference features keynote presentations, panel discussions, hands-on workshops, and extensive networking opportunities. Don't miss this chance to be part of the tech revolution!</p>
        `,
        speakers: [
            {
                name: 'Dr. Alex Johnson',
                title: 'AI Research Director, TechCorp',
                image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face',
                bio: 'Leading expert in artificial intelligence and machine learning with 15+ years of experience in developing cutting-edge AI solutions.'
            },
            {
                name: 'Sarah Chen',
                title: 'CEO, InnovateTech',
                image: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face',
                bio: 'Successful entrepreneur and thought leader in digital transformation and startup ecosystem development.'
            }
        ],
        learningPoints: [
            { title: 'AI & Machine Learning Trends', description: 'Latest developments and practical applications' },
            { title: 'Digital Transformation', description: 'Strategies for business modernization' },
            { title: 'Networking Opportunities', description: 'Connect with industry leaders and peers' },
            { title: 'Future Tech Insights', description: 'Emerging technologies and market trends' }
        ],
        features: [
            { icon: 'bi-camera-video', text: 'Live Streaming Available' },
            { icon: 'bi-file-earmark-pdf', text: 'Digital Resources Included' },
            { icon: 'bi-award', text: 'Certificate of Attendance' },
            { icon: 'bi-people', text: 'Networking Opportunities' }
        ]
    },
    'ai-workshop-series': {
        title: 'AI Workshop Series 2024',
        category: 'Workshop',
        date: 'Jan 20-22, 2025',
        duration: '3 Days',
        location: 'Online',
        attendees: '500+',
        price: 'FREE',
        description: `
            <p>Dive deep into the world of Artificial Intelligence with our comprehensive workshop series. Learn from industry experts and get hands-on experience with the latest AI tools and technologies.</p>
            <p>This intensive workshop covers machine learning fundamentals, neural networks, deep learning, and practical AI applications in various industries.</p>
            <p>Perfect for developers, data scientists, and tech enthusiasts looking to advance their AI skills.</p>
        `,
        speakers: [
            {
                name: 'Dr. Maria Rodriguez',
                title: 'ML Engineer, Google',
                image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&h=150&fit=crop&crop=face',
                bio: 'Senior Machine Learning Engineer with expertise in deep learning and computer vision applications.'
            },
            {
                name: 'James Park',
                title: 'AI Researcher, OpenAI',
                image: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face',
                bio: 'Research scientist specializing in natural language processing and large language models.'
            }
        ],
        learningPoints: [
            { title: 'Machine Learning Fundamentals', description: 'Core concepts and algorithms' },
            { title: 'Deep Learning Techniques', description: 'Neural networks and advanced architectures' },
            { title: 'Practical AI Applications', description: 'Real-world use cases and implementations' },
            { title: 'AI Tools & Frameworks', description: 'TensorFlow, PyTorch, and modern AI tools' }
        ],
        features: [
            { icon: 'bi-laptop', text: 'Hands-on Coding Sessions' },
            { icon: 'bi-download', text: 'Downloadable Resources' },
            { icon: 'bi-award', text: 'Completion Certificate' },
            { icon: 'bi-chat-dots', text: 'Q&A Sessions' }
        ]
    },
    'startup-pitch-night': {
        title: 'Startup Pitch Night',
        category: 'Networking',
        date: 'Feb 15, 2025',
        duration: '1 Day',
        location: 'New York City',
        attendees: '300+',
        price: 'FREE',
        description: `
            <p>Join us for an exciting evening where innovative startups pitch their ideas to investors, mentors, and fellow entrepreneurs. This is the perfect opportunity to discover the next big thing in tech!</p>
            <p>Network with investors, meet potential co-founders, and get inspired by groundbreaking startup ideas across various industries.</p>
            <p>Whether you're an entrepreneur, investor, or just passionate about innovation, this event offers valuable insights into the startup ecosystem.</p>
        `,
        speakers: [
            {
                name: 'Michael Thompson',
                title: 'Partner, Venture Capital',
                image: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&h=150&fit=crop&crop=face',
                bio: 'Experienced venture capitalist with a track record of successful investments in early-stage startups.'
            },
            {
                name: 'Lisa Wang',
                title: 'Serial Entrepreneur',
                image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&h=150&fit=crop&crop=face',
                bio: 'Successful entrepreneur who has founded and exited multiple tech companies.'
            }
        ],
        learningPoints: [
            { title: 'Pitch Presentation Skills', description: 'Learn effective pitching techniques' },
            { title: 'Investment Insights', description: 'Understand what investors look for' },
            { title: 'Startup Ecosystem', description: 'Navigate the entrepreneurial landscape' },
            { title: 'Networking Strategies', description: 'Build valuable professional connections' }
        ],
        features: [
            { icon: 'bi-mic', text: 'Live Pitch Presentations' },
            { icon: 'bi-people', text: 'Investor Networking' },
            { icon: 'bi-trophy', text: 'Pitch Competition' },
            { icon: 'bi-cup-hot', text: 'Refreshments & Cocktails' }
        ]
    }
};

function initEventDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = urlParams.get('event') || 'global-tech-conference';
    
    loadEventData(eventId);
    initEventListeners();
}

function loadEventData(eventId) {
    const event = eventsData[eventId];
    
    if (!event) {

        return;
    }

    // Update page title and breadcrumb
    document.title = `${event.title} - Edvora`;
    document.getElementById('breadcrumbTitle').textContent = event.title;

    // Update hero section
    document.getElementById('eventCategory').textContent = event.category;
    document.getElementById('eventTitle').textContent = event.title;
    document.getElementById('eventDate').textContent = event.date;
    document.getElementById('eventDuration').textContent = event.duration;
    document.getElementById('eventLocation').textContent = event.location;
    document.getElementById('eventAttendees').textContent = event.attendees;

    // Update description
    document.getElementById('eventDescription').innerHTML = event.description;

    // Update speakers
    updateSpeakers(event.speakers);

    // Update learning points
    updateLearningPoints(event.learningPoints);

    // Update price
    document.getElementById('eventPrice').textContent = event.price;

    // Update features
    updateFeatures(event.features);
}

function updateSpeakers(speakers) {
    const speakersContainer = document.getElementById('eventSpeakers');
    speakersContainer.innerHTML = speakers.map(speaker => `
        <div class="col-md-6">
            <div class="speaker-card text-center">
                <img src="${speaker.image}" alt="${speaker.name}" class="speaker-avatar">
                <h5 class="fw-bold text-primary">${speaker.name}</h5>
                <p class="text-muted mb-2">${speaker.title}</p>
                <p class="small">${speaker.bio}</p>
            </div>
        </div>
    `).join('');
}

function updateLearningPoints(learningPoints) {
    const learningContainer = document.getElementById('eventLearning');
    learningContainer.innerHTML = learningPoints.map(point => `
        <div class="col-md-6">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                    <i class="bi bi-check"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">${point.title}</h6>
                    <p class="text-muted small mb-0">${point.description}</p>
                </div>
            </div>
        </div>
    `).join('');
}

function updateFeatures(features) {
    const featuresContainer = document.getElementById('eventFeatures');
    featuresContainer.innerHTML = features.map(feature => `
        <div class="d-flex align-items-center gap-3">
            <i class="bi ${feature.icon} text-primary"></i>
            <span>${feature.text}</span>
        </div>
    `).join('');
}

function initEventListeners() {
    // Add any additional event listeners here

}

// Event registration function
async function registerForEvent(eventId, clickEvent) {
    // Prevent any default form submission
    if (clickEvent) {
        clickEvent.preventDefault();
        clickEvent.stopPropagation();
    }

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        console.error('CSRF token not found');
        showToast('Security token missing. Please refresh the page.', 'error');
        return;
    }

    // Get the button that was clicked
    const button = clickEvent?.target || document.querySelector('.register-btn');
    const originalButtonContent = button ? button.innerHTML : '';

    // Disable button and show loading spinner
    if (button) {
        button.disabled = true;
        button.style.opacity = '0.7';
        button.style.cursor = 'not-allowed';
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            <span>Registering...</span>
        `;
    }

    try {
        console.log('Sending registration request for event:', eventId);

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

        console.log('Response status:', response.status);

        const data = await response.json();
        console.log('Response data:', data);

        if (response.ok) {
            showToast(data.message || 'Registration successful! Check your email for confirmation.', 'success');
            // Change button to success state
            if (button) {
                button.innerHTML = '<i class="fas fa-check me-2"></i>Registered!';
                button.classList.remove('register-btn');
                button.classList.add('btn-success');
            }
        } else {
            // Restore button on error
            if (button) {
                button.disabled = false;
                button.style.opacity = '1';
                button.style.cursor = 'pointer';
                button.innerHTML = originalButtonContent;
            }

            if (response.status === 401) {
                // Not authenticated - redirect to login
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
            } else if (response.status === 400) {
                // Bad request - already registered or event full
                showToast(data.message || 'Registration failed.', 'warning');
            } else {
                showToast(data.message || 'Registration failed. Please try again.', 'error');
            }
        }
    } catch (error) {
        console.error('Registration error:', error);
        showToast('An error occurred. Please try again.', 'error');

        // Restore button on error
        if (button) {
            button.disabled = false;
            button.style.opacity = '1';
            button.style.cursor = 'pointer';
            button.innerHTML = originalButtonContent;
        }
    }
}

// Add to wishlist function
function addToWishlist() {
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = urlParams.get('event') || 'global-tech-conference';
    
    // Get existing wishlist
    let wishlist = JSON.parse(localStorage.getItem('eventWishlist') || '[]');
    
    if (!wishlist.includes(eventId)) {
        wishlist.push(eventId);
        localStorage.setItem('eventWishlist', JSON.stringify(wishlist));
        showToast('Event added to wishlist!', 'success');
    } else {
        showToast('Event already in wishlist!', 'info');
    }
}

// Share event function
function shareEvent() {
    if (navigator.share) {
        const event = eventsData[new URLSearchParams(window.location.search).get('event') || 'global-tech-conference'];
        navigator.share({
            title: event.title,
            text: `Check out this amazing event: ${event.title}`,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Event link copied to clipboard!', 'success');
        });
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    // Bootstrap bg classes mapping
    const bgClasses = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'info'
    };

    // Icon mapping
    const iconClasses = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };

    const bgClass = bgClasses[type] || 'info';
    const iconClass = iconClasses[type] || 'info-circle';

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${bgClass} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${iconClass} me-2"></i>
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
    window.location.href = `events-detail.html?event=${eventSlug}`;
}
