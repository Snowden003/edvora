// Premium Contact Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initContactForm();
    initLiveChat();
    initPremiumAnimations();
    initFormEnhancements();
});

// Initialize Contact Form
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            firstName: document.getElementById('firstName').value,
            lastName: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value
        };

        // Basic validation
        if (!formData.firstName || !formData.lastName || !formData.email || !formData.subject || !formData.message) {
            showAlert('Please fill in all required fields.', 'danger');
            return;
        }

        if (!isValidEmail(formData.email)) {
            showAlert('Please enter a valid email address.', 'danger');
            return;
        }

        // Show loading state
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
        submitBtn.disabled = true;

        // Simulate form submission
        setTimeout(() => {
            showAlert('Thank you for your message! We\'ll get back to you within 24 hours.', 'success');
            contactForm.reset();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Log the form data (in real app, this would be sent to server)

        }, 2000);
    });
}

// Initialize Live Chat
function initLiveChat() {
    const chatButton = document.querySelector('.btn-outline-info');
    if (!chatButton) return;

    chatButton.addEventListener('click', function() {
        // Simulate live chat opening
        showAlert('Live chat feature coming soon! Please use the contact form or email us directly.', 'info');
    });
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show Alert
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert alert at the top of the form
    const form = document.getElementById('contactForm');
    if (form) {
        form.insertBefore(alertDiv, form.firstChild);
    }

    // Auto-dismiss success alerts
    if (type === 'success') {
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Newsletter subscription
function subscribeNewsletter() {
    const emailInput = document.querySelector('footer input[type="email"]');
    if (!emailInput) return;

    const email = emailInput.value;
    if (!email) {
        alert('Please enter your email address.');
        return;
    }

    if (!isValidEmail(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    // Simulate subscription
    alert('Thank you for subscribing to our newsletter!');
    emailInput.value = '';
}

// Initialize Premium Animations
function initPremiumAnimations() {
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    // Observe animated elements
    const animatedElements = document.querySelectorAll('[style*="animation"]');
    animatedElements.forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });

    // Add a subtle parallax effect to background elements
    const parallaxElements = document.querySelectorAll('.hero-bg-elements > div');
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        parallaxElements.forEach(element => {
            const speed = parseFloat(element.getAttribute('data-speed') || '0.1');
            const initialTop = parseFloat(element.style.top) || 0;
            // We only apply a subtle vertical translation, letting the CSS handle the main animation
            element.style.setProperty('--parallax-y', `${-scrolled * speed}px`);
        });
    });
}

// Initialize Form Enhancements
function initFormEnhancements() {
    const inputs = document.querySelectorAll('.premium-input, .premium-select, .premium-textarea');
    
    inputs.forEach(input => {
        // Add focus animations
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
            addInputGlow(this);
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
            removeInputGlow(this);
        });

        // Add typing animation
        input.addEventListener('input', function() {
            addTypingEffect(this);
        });
    });

    // Enhanced submit button
    const submitBtn = document.querySelector('.premium-submit-btn');
    if (submitBtn) {
        submitBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });

        submitBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    }
}

// Add input glow effect
function addInputGlow(input) {
    const glow = document.createElement('div');
    glow.className = 'input-glow-effect';
    glow.style.cssText = `
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #1F8FFF, #1F8FFF, #1F8FFF);
        border-radius: 17px;
        opacity: 0;
        z-index: -1;
        filter: blur(8px);
        transition: opacity 0.3s ease;
    `;
    
    input.parentElement.appendChild(glow);
    setTimeout(() => glow.style.opacity = '0.6', 10);
}

// Remove input glow effect
function removeInputGlow(input) {
    const glow = input.parentElement.querySelector('.input-glow-effect');
    if (glow) {
        glow.style.opacity = '0';
        setTimeout(() => glow.remove(), 300);
    }
}

// Add typing effect
function addTypingEffect(input) {
    input.style.transform = 'scale(1.01)';
    setTimeout(() => {
        input.style.transform = 'scale(1)';
    }, 100);
}

// Enhanced form submission with premium effects
function enhancedFormSubmit(formData) {
    const submitBtn = document.querySelector('.premium-submit-btn');
    const btnContent = submitBtn.querySelector('.btn-content');
    const btnText = btnContent.querySelector('.btn-text');
    
    // Create loading animation
    const loadingSpinner = document.createElement('div');
    loadingSpinner.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
    loadingSpinner.style.animation = 'spin 1s linear infinite';
    
    // Add CSS for spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    
    // Replace button content
    const originalIcon = btnContent.querySelector('.btn-icon').innerHTML;
    btnContent.querySelector('.btn-icon').innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
    btnContent.querySelector('.btn-icon').style.animation = 'spin 1s linear infinite';
    btnText.textContent = 'Sending...';
    submitBtn.disabled = true;
    
    // Simulate sending
    setTimeout(() => {
        // Success animation
        btnContent.querySelector('.btn-icon').innerHTML = '<i class="bi bi-check-circle-fill"></i>';
        btnContent.querySelector('.btn-icon').style.animation = 'none';
        btnText.textContent = 'Sent Successfully!';
        
        setTimeout(() => {
            // Reset button
            btnContent.querySelector('.btn-icon').innerHTML = originalIcon;
            btnText.textContent = 'Send Message';
            submitBtn.disabled = false;
        }, 2000);
    }, 2000);
}

// Add event listener for newsletter form
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('footer form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            subscribeNewsletter();
        });
    }
});
