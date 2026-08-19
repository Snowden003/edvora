// Home Page - Computer Science Roadmap JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize roadmap hover effects
    initRoadmapHoverEffects();
});

function initRoadmapHoverEffects() {
    // Learning card hover effects
    const learningCards = document.querySelectorAll('.learning-card');
    
    learningCards.forEach(card => {
        // Mouse enter effect
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.02)';
            
            // Apply specific hover shadow based on card type
            if (this.classList.contains('learning-card-blue')) {
                this.style.boxShadow = '0 20px 60px rgba(31, 143, 255, 0.25), 0 0 0 1px rgba(31, 143, 255, 0.2)';
            } else if (this.classList.contains('learning-card-orange')) {
                this.style.boxShadow = '0 20px 60px rgba(31, 143, 255, 0.25), 0 0 0 1px rgba(31, 143, 255, 0.2)';
            } else if (this.classList.contains('learning-card-yellow')) {
                this.style.boxShadow = '0 20px 60px rgba(31, 143, 255, 0.25), 0 0 0 1px rgba(31, 143, 255, 0.2)';
            }
        });
        
        // Mouse leave effect
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            
            // Reset to original shadow based on card type
            if (this.classList.contains('learning-card-blue')) {
                this.style.boxShadow = '0 10px 40px rgba(31, 143, 255, 0.15), 0 0 0 1px rgba(31, 143, 255, 0.1)';
            } else if (this.classList.contains('learning-card-orange')) {
                this.style.boxShadow = '0 10px 40px rgba(31, 143, 255, 0.15), 0 0 0 1px rgba(31, 143, 255, 0.1)';
            } else if (this.classList.contains('learning-card-yellow')) {
                this.style.boxShadow = '0 10px 40px rgba(31, 143, 255, 0.15), 0 0 0 1px rgba(31, 143, 255, 0.1)';
            }
        });
    });
    
    // Premium CTA button hover effects
    const ctaButton = document.querySelector('.premium-cta-btn');
    
    if (ctaButton) {
        ctaButton.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
            this.style.boxShadow = '0 25px 60px rgba(31, 143, 255, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.2)';
        });
        
        ctaButton.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 15px 40px rgba(31, 143, 255, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1)';
        });
    }
}

// Intersection Observer for scroll animations (disabled to fix layout issues)
function initScrollAnimations() {
    // Scroll animations disabled to prevent layout conflicts
    // Cards will use CSS hover animations instead
    return;
}
