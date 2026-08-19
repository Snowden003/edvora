// Monthly Chart Function
function initMonthlyChart() {
    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;

    // Get dynamic data from data attributes
    const chartData = window.foundationChartData || {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        data: [0, 0, 0, 0, 0, 0]
    };

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Monthly Donations ($)',
                data: chartData.data,
                borderColor: '#1F8FFF',
                backgroundColor: 'rgba(31, 143, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#1F8FFF',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 143, 255, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#1F8FFF',
                    borderWidth: 2,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c757d',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(108, 117, 125, 0.1)'
                    },
                    ticks: {
                        color: '#6c757d',
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        callback: function(value) {
                            return '$' + (value / 1000) + 'K';
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

// Foundation Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initDonationForm();
    initMonthlyChart();
    initCounterAnimations();
    initScrollAnimations();
    initAmountButtons();
    initHeroButtons();
    initFormEnhancements();
});

// Counter Animation for Statistics
function initCounterAnimations() {
    const counters = document.querySelectorAll('.stat-number');
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / 100;
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.ceil(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };
        
        updateCounter();
    };
    
    // Intersection Observer for counter animation
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
}

// Donation Form Functionality
function initDonationForm() {
    const form = document.getElementById('donationForm');
    
    if (form) {
        form.addEventListener('submit', handleDonationSubmit);
    }
}

function handleDonationSubmit(e) {
    e.preventDefault();
    
    const submitBtn = document.querySelector('.btn-donate-submit');
    const formData = new FormData(e.target);
    
    // Get selected amount
    const customAmount = document.getElementById('customAmount').value;
    const amount = customAmount;
    
    // Validate form
    if (!validateDonationForm(amount)) {
        return;
    }
    
    // Show loading state
    submitBtn.classList.add('loading');
    
    // Simulate payment processing
    setTimeout(() => {
        submitBtn.classList.remove('loading');
        showDonationSuccess(amount);
        resetDonationForm();
    }, 2000);
}

function validateDonationForm(amount) {
    const name = document.getElementById('donorName').value.trim();
    const email = document.getElementById('donorEmail').value.trim();
    const phone = document.getElementById('donorPhone').value.trim();
    
    if (!amount || amount < 5) {
        showAlert('Please select a minimum amount of $5', 'error');
        return false;
    }
    
    if (!name) {
        showAlert('Please enter your full name', 'error');
        return false;
    }
    
    if (!email || !isValidEmail(email)) {
        showAlert('Please enter a valid email address', 'error');
        return false;
    }
    
    if (!phone) {
        showAlert('Please enter your phone number', 'error');
        return false;
    }
    
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showDonationSuccess(amount) {
    const formattedAmount = parseInt(amount).toLocaleString();
    showAlert(`Thank you for your support! $${formattedAmount} has been successfully paid.`, 'success');
    
    // Update total amount (simulate)
    updateTotalAmount(parseInt(amount));
}

function updateTotalAmount(newAmount) {
    const totalAmountElement = document.getElementById('totalAmount');
    if (totalAmountElement) {
        const currentAmount = parseInt(totalAmountElement.textContent.replace(/,/g, ''));
        const newTotal = currentAmount + newAmount;
        
        // Animate the number change
        animateNumberChange(totalAmountElement, currentAmount, newTotal);
    }
}

function animateNumberChange(element, from, to) {
    const duration = 2000;
    const startTime = Date.now();
    
    const updateNumber = () => {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(from + (to - from) * progress);
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    };
    
    updateNumber();
}

function resetDonationForm() {
    const form = document.getElementById('donationForm');
    if (form) {
        form.reset();
        
        // Reset amount chips
        document.querySelectorAll('.amount-chip').forEach(chip => {
            chip.classList.remove('active');
        });
    }
}

// Amount Button Selection
function initAmountButtons() {
    const amountChips = document.querySelectorAll('.amount-chip');
    const customAmountInput = document.getElementById('customAmount');
    
    if (amountChips.length === 0) return;
    
    amountChips.forEach(chip => {
        chip.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Remove active class from all chips
            amountChips.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked chip
            this.classList.add('active');
            
            // If "Other" clicked, focus custom input
            if (this.classList.contains('other')) {
                if (customAmountInput) {
                    customAmountInput.value = '';
                    customAmountInput.focus();
                }
            } else {
                // Set custom amount to selected value
                if (customAmountInput) {
                    customAmountInput.value = this.dataset.amount;
                }
            }
        });
    });
    
    // Handle custom amount input
    if (customAmountInput) {
        customAmountInput.addEventListener('input', function() {
            // Remove active class from all preset chips
            amountChips.forEach(c => c.classList.remove('active'));
            // Mark "Other" as active
            const otherChip = document.querySelector('.amount-chip.other');
            if (otherChip) otherChip.classList.add('active');
        });
    }
}

// Scroll Animations
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.donor-card, .total-donations-card, .donation-form-card');
    
    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = Math.random() * 0.5 + 's';
                entry.target.classList.add('animate-in');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    animatedElements.forEach(element => {
        scrollObserver.observe(element);
    });
}

// Alert System
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlert = document.querySelector('.custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `custom-alert alert-${type}`;
    alert.innerHTML = `
        <div class="alert-content">
            <i class="fas ${getAlertIcon(type)}"></i>
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add styles
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        background: ${type === 'success' ? 'linear-gradient(45deg, #28a745, #20c997)' : 'linear-gradient(45deg, #dc3545, #fd7e14)'};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: slideInRight 0.5s ease-out;
        max-width: 400px;
        backdrop-filter: blur(10px);
    `;
    
    // Add animation keyframes
    if (!document.querySelector('#alert-animations')) {
        const style = document.createElement('style');
        style.id = 'alert-animations';
        style.textContent = `
            @keyframes slideInRight {
                0% { transform: translateX(100%); opacity: 0; }
                100% { transform: translateX(0); opacity: 1; }
            }
            .alert-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .alert-close {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                font-size: 1.2rem;
                margin-right: auto;
                padding: 5px;
                border-radius: 50%;
                transition: background 0.3s ease;
            }
            .alert-close:hover {
                background: rgba(255,255,255,0.2);
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.style.animation = 'slideInRight 0.5s ease-out reverse';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
}

function getAlertIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || icons.info;
}

// Hero Action Buttons - Scroll to donation form
function initHeroButtons() {
    const donateNowBtn = document.querySelector('.btn-donate-now');
    
    if (donateNowBtn) {
        donateNowBtn.addEventListener('click', function() {
            const form = document.querySelector('.donation-card');
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
}

// Parallax Effect for Background Elements
function initParallaxEffect() {
    const floatingElements = document.querySelectorAll('.floating-element');
    
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        floatingElements.forEach((element, index) => {
            const speed = (index + 1) * 0.1;
            element.style.transform = `translateY(${rate * speed}px) rotate(${scrolled * speed * 0.1}deg)`;
        });
    });
}

// Initialize parallax effect (only if elements exist)
function initParallaxIfExists() {
    const floatingElements = document.querySelectorAll('.floating-element');
    if (floatingElements.length > 0) {
        initParallaxEffect();
    }
}

// Form Input Enhancement
function initFormEnhancements() {
    const inputGroups = document.querySelectorAll('.input-group input, .input-group textarea');
    
    inputGroups.forEach(input => {
        const parent = input.closest('.input-group');
        
        // Focus effect
        input.addEventListener('focus', function() {
            if (parent) {
                parent.style.borderColor = '#1F8FFF';
                parent.style.boxShadow = '0 0 0 3px rgba(31, 143, 255, 0.1)';
            }
        });
        
        input.addEventListener('blur', function() {
            if (parent) {
                parent.style.borderColor = '#e9ecef';
                parent.style.boxShadow = 'none';
            }
        });
    });
}

// Donation Statistics Update (Real-time simulation)
function simulateRealTimeUpdates() {
    setInterval(() => {
        // Randomly update statistics (simulation)
        if (Math.random() < 0.1) { // 10% chance every interval
            const randomAmount = Math.floor(Math.random() * 10000) + 100;
            updateTotalAmount(randomAmount);
            
            // Show notification
            showAlert(`New donation: $${randomAmount.toLocaleString()} received!`, 'success');
        }
    }, 30000); // Check every 30 seconds
}

// Initialize real-time updates (commented out for demo)
// document.addEventListener('DOMContentLoaded', simulateRealTimeUpdates);
