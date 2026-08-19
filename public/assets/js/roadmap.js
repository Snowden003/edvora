// Roadmap Page JavaScript

// Intersection Observer for animations
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Observe timeline items
    document.querySelectorAll('.timeline-item').forEach(item => {
        observer.observe(item);
    });

    // Progress button interactions
    document.querySelectorAll('.progress-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-check me-2"></i>Loading...';
            this.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-play me-2"></i>Start Learning';
                this.style.background = 'linear-gradient(135deg, #1F8FFF, #1F8FFF)';
            }, 2000);
        });
    });

    // Timeline content hover effects
    document.querySelectorAll('.timeline-content').forEach(content => {
        content.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        content.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add parallax effect to floating elements
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.floating-element');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px) rotate(${scrolled * 0.1}deg)`;
        });
    });

    // Add loading animation to page
    const loadingOverlay = document.createElement('div');
    loadingOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #1F8FFF, #1F8FFF);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 1;
        transition: opacity 0.5s ease;
    `;
    
    const spinner = document.createElement('div');
    spinner.innerHTML = '<i class="fas fa-route" style="font-size: 3rem; color: white; animation: spin 1s linear infinite;"></i>';
    loadingOverlay.appendChild(spinner);
    document.body.appendChild(loadingOverlay);

    // Add spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

    // Hide loading overlay after page load
    setTimeout(() => {
        loadingOverlay.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(loadingOverlay);
        }, 500);
    }, 1000);
});

// Add stage completion tracking
class RoadmapProgress {
    constructor() {
        this.completedStages = JSON.parse(localStorage.getItem('roadmapProgress') || '[]');
        this.initializeProgress();
    }

    initializeProgress() {
        this.completedStages.forEach(stageId => {
            const button = document.querySelector(`[data-stage="${stageId}"]`);
            if (button) {
                this.markAsCompleted(button);
            }
        });
    }

    markAsCompleted(button) {
        button.innerHTML = '<i class="fas fa-check me-2"></i>Completed';
        button.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
        button.disabled = true;
    }

    completeStage(stageId) {
        if (!this.completedStages.includes(stageId)) {
            this.completedStages.push(stageId);
            localStorage.setItem('roadmapProgress', JSON.stringify(this.completedStages));
        }
    }

    getProgress() {
        return {
            completed: this.completedStages.length,
            total: 6,
            percentage: Math.round((this.completedStages.length / 6) * 100)
        };
    }
}

// Initialize progress tracking
const roadmapProgress = new RoadmapProgress();

// Add progress indicator
function addProgressIndicator() {
    const progress = roadmapProgress.getProgress();
    const indicator = document.createElement('div');
    indicator.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: rgba(0, 0, 0, 0.06);
        backdrop-filter: blur(20px);
        border-radius: 15px;
        padding: 15px;
        color: black;
        font-weight: 600;
        z-index: 1000;
        border: 1px solid rgba(0, 0, 0, 0.09)
    `;
    
    indicator.innerHTML = `
        <div style="text-align: center;">
            <i class="fas fa-chart-line mb-2" style="font-size: 1.5rem;"></i>
            <div>Progress</div>
            <div style="font-size: 1.5rem; color: #1F8FFF;">${progress.percentage}%</div>
            <div style="font-size: 0.8rem;">${progress.completed}/${progress.total} stages</div>
        </div>
    `;
    
    document.body.appendChild(indicator);
}

// Add progress indicator when page loads
window.addEventListener('load', addProgressIndicator);
