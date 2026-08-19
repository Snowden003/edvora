// Video Guide Section JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initVideoGuide();
});

function initVideoGuide() {
    const videoPlaceholder = document.querySelector('.video-placeholder');
    const playButton = document.querySelector('.video-play-button');
    const featureItems = document.querySelectorAll('.video-feature-item');
    const infoCards = document.querySelectorAll('.video-info-card');

    // Video play button interaction
    if (playButton) {
        playButton.addEventListener('click', function(e) {
            e.preventDefault();
            handleVideoPlay();
        });
    }

    // Video placeholder click
    if (videoPlaceholder) {
        videoPlaceholder.addEventListener('click', function(e) {
            if (!e.target.closest('.video-play-button')) {
                handleVideoPlay();
            }
        });
    }

    // Feature items hover effects
    featureItems.forEach((item, index) => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
            this.style.boxShadow = '0 10px 30px rgba(31, 143, 255, 0.2)';
        });

        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = 'none';
        });

        // Staggered entrance animation
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 200);
    });

    // Info cards interaction
    infoCards.forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';
            this.style.boxShadow = '0 25px 80px rgba(31, 143, 255, 0.2)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 10px 40px rgba(31, 143, 255, 0.1)';
        });
    });

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                // Trigger specific animations based on element
                if (entry.target.classList.contains('video-header')) {
                    animateVideoHeader();
                } else if (entry.target.classList.contains('video-container')) {
                    animateVideoContainer();
                } else if (entry.target.classList.contains('video-info-card')) {
                    animateInfoCard(entry.target);
                }
            }
        });
    }, observerOptions);

    // Observe elements
    const elementsToObserve = document.querySelectorAll('.video-header, .video-container, .video-info-card');
    elementsToObserve.forEach(el => observer.observe(el));

    // Initialize feature items with hidden state
    featureItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
    });
}

function handleVideoPlay() {
    const playButton = document.querySelector('.video-play-button');
    const placeholder = document.querySelector('.video-placeholder');
    
    // Add click animation
    if (playButton) {
        playButton.style.transform = 'scale(0.9)';
        playButton.style.background = 'rgba(255, 255, 255, 0.8)';
        
        setTimeout(() => {
            playButton.style.transform = 'scale(1)';
            playButton.style.background = 'rgba(255, 255, 255, 0.95)';
        }, 150);
    }

    // Add ripple effect
    createRippleEffect(event);
    
    // Show video modal or redirect (placeholder for now)
    showVideoModal();
}

function createRippleEffect(event) {
    const placeholder = document.querySelector('.video-placeholder');
    if (!placeholder) return;

    const ripple = document.createElement('div');
    const rect = placeholder.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;

    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: rippleEffect 0.6s ease-out;
        pointer-events: none;
        z-index: 10;
    `;

    placeholder.appendChild(ripple);

    setTimeout(() => {
        ripple.remove();
    }, 600);
}

function showVideoModal() {
    // Create modal backdrop
    const modal = document.createElement('div');
    modal.className = 'video-modal';
    modal.innerHTML = `
        <div class="video-modal-backdrop">
            <div class="video-modal-content">
                <div class="video-modal-header">
                    <h4>How to Use Edvora Platform</h4>
                    <button class="video-modal-close">&times;</button>
                </div>
                <div class="video-modal-body">
                    <div class="video-coming-soon">
                        <div class="coming-soon-icon">
                            <i class="bi bi-camera-video"></i>
                        </div>
                        <h3>Video Coming Soon!</h3>
                        <p>We're preparing an amazing tutorial video to help you get the most out of Edvora platform.</p>
                        <div class="coming-soon-features">
                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>Complete platform walkthrough</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>Course enrollment process</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>Progress tracking tips</span>
                            </div>
                            <div class="feature-item">
                                <i class="bi bi-check-circle"></i>
                                <span>Community interaction guide</span>
                            </div>
                        </div>
                        <button class="btn-notify-me">
                            <i class="bi bi-bell"></i>
                            Notify Me When Ready
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal styles
    const modalStyles = document.createElement('style');
    modalStyles.textContent = `
        .video-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            animation: modalFadeIn 0.3s ease;
        }
        
        .video-modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .video-modal-content {
            background: white;
            border-radius: 20px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .video-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .video-modal-header h4 {
            margin: 0;
            color: #1F8FFF;
            font-weight: 700;
        }
        
        .video-modal-close {
            background: none;
            border: none;
            font-size: 2rem;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .video-modal-close:hover {
            background: #f8f9fa;
            color: #1F8FFF;
        }
        
        .video-modal-body {
            padding: 2rem;
        }
        
        .video-coming-soon {
            text-align: center;
        }
        
        .coming-soon-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #1F8FFF, #1F8FFF);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 3rem;
            color: white;
            animation: iconPulse 2s ease-in-out infinite;
        }
        
        .video-coming-soon h3 {
            color: #1F8FFF;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .video-coming-soon p {
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .coming-soon-features {
            text-align: left;
            max-width: 400px;
            margin: 0 auto 2rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            color: #495057;
        }
        
        .feature-item i {
            color: #28a745;
            font-size: 1.2rem;
        }
        
        .btn-notify-me {
            background: linear-gradient(135deg, #1F8FFF, #1F8FFF);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 auto;
        }
        
        .btn-notify-me:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(31, 143, 255, 0.3);
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes modalSlideIn {
            from { 
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes rippleEffect {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
    `;

    document.head.appendChild(modalStyles);
    document.body.appendChild(modal);

    // Close modal functionality
    const closeBtn = modal.querySelector('.video-modal-close');
    const backdrop = modal.querySelector('.video-modal-backdrop');
    const notifyBtn = modal.querySelector('.btn-notify-me');

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) closeModal();
    });

    notifyBtn.addEventListener('click', () => {
        notifyBtn.innerHTML = '<i class="bi bi-check-circle"></i> You\'ll be notified!';
        notifyBtn.style.background = '#28a745';
        setTimeout(closeModal, 1500);
    });

    function closeModal() {
        modal.style.animation = 'modalFadeIn 0.3s ease reverse';
        setTimeout(() => {
            modal.remove();
            modalStyles.remove();
        }, 300);
    }

    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
}

function animateVideoHeader() {
    const badge = document.querySelector('.video-badge');
    const title = document.querySelector('.video-title');
    const subtitle = document.querySelector('.video-subtitle');

    if (badge) {
        badge.style.animation = 'videoBadgeGlow 3s ease-in-out infinite, slideInUp 0.8s ease';
    }
    
    if (title) {
        setTimeout(() => {
            title.style.animation = 'videoTitleGlow 4s ease-in-out infinite, slideInUp 0.8s ease';
        }, 200);
    }
    
    if (subtitle) {
        setTimeout(() => {
            subtitle.style.opacity = '1';
            subtitle.style.transform = 'translateY(0)';
        }, 400);
    }
}

function animateVideoContainer() {
    const container = document.querySelector('.video-container');
    if (container) {
        container.style.animation = 'videoContainerFloat 6s ease-in-out infinite, slideInUp 1s ease';
    }
}

function animateInfoCard(card) {
    card.style.opacity = '1';
    card.style.transform = 'translateY(0)';
}

// Add entrance animation styles
const entranceStyles = document.createElement('style');
entranceStyles.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .video-subtitle {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .video-info-card {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
`;

document.head.appendChild(entranceStyles);
