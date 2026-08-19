// Modern Footer Interactive Features
document.addEventListener("DOMContentLoaded", () => {
    initNewsletterForm();
    initSocialLinks();
    initFooterObservers();
});

function initFooterObservers() {
    const footer = document.querySelector(".modern-footer");
    if (!footer) {
        return;
    }

    const animatedSections = [
        footer.querySelector(".footer-brand"),
        ...footer.querySelectorAll(".footer-section"),
        footer.querySelector(".footer-bottom"),
    ].filter(Boolean);

    const statNumbers = footer.querySelectorAll(".stat-number");
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (!("IntersectionObserver" in window)) {
        animatedSections.forEach((element) => element.classList.add("footer-animate-in"));
        statNumbers.forEach((element) => animateNumber(element, reduceMotion));
        return;
    }

    const revealObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add("footer-animate-in");
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.15,
            rootMargin: "0px 0px -8% 0px",
        },
    );

    animatedSections.forEach((element, index) => {
        element.style.setProperty("--footer-delay", `${index * 70}ms`);
        revealObserver.observe(element);
    });

    if (!statNumbers.length) {
        return;
    }

    const statsObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting || entry.target.dataset.animated === "true") {
                    return;
                }

                entry.target.dataset.animated = "true";
                animateNumber(entry.target, reduceMotion);
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.35,
        },
    );

    statNumbers.forEach((element) => statsObserver.observe(element));
}

function animateNumber(element, reduceMotion = false) {
    const targetText = element.textContent.trim();
    const numericValue = parseInt(targetText.replace(/[^\d]/g, ""), 10);

    if (!numericValue || reduceMotion) {
        element.textContent = targetText;
        return;
    }

    const suffix = targetText.replace(/[\d]/g, "");
    const duration = 700;
    const startTime = performance.now();

    const tick = (timestamp) => {
        const progress = Math.min((timestamp - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        element.textContent = `${Math.floor(numericValue * eased)}${suffix}`;

        if (progress < 1) {
            window.requestAnimationFrame(tick);
        } else {
            element.textContent = `${numericValue}${suffix}`;
        }
    };

    window.requestAnimationFrame(tick);
}

function initNewsletterForm() {
    const form = document.querySelector(".newsletter-form");
    const input = document.querySelector(".newsletter-input");
    const button = document.querySelector(".newsletter-btn");

    if (!form || !input || !button) {
        return;
    }

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        const email = input.value.trim();
        if (validateEmail(email)) {
            button.innerHTML = '<i class="bi bi-check-circle"></i>';
            button.style.background = "linear-gradient(135deg, #28a745, #20c997)";
            showNotification("Thank you for subscribing!", "success");

            window.setTimeout(() => {
                button.innerHTML = '<i class="bi bi-send"></i>';
                button.style.background = "";
                input.value = "";
            }, 2000);
            return;
        }

        input.style.borderColor = "#dc3545";
        input.style.animation = "shake 0.5s ease-in-out";
        showNotification("Please enter a valid email address", "error");

        window.setTimeout(() => {
            input.style.borderColor = "";
            input.style.animation = "";
        }, 500);
    });

    input.addEventListener("focus", () => {
        form.classList.add("newsletter-form--focused");
    });

    input.addEventListener("blur", () => {
        form.classList.remove("newsletter-form--focused");
    });
}

function initSocialLinks() {
    const socialLinks = document.querySelectorAll(".social-link");

    socialLinks.forEach((link) => {
        link.addEventListener("mouseenter", () => createRipple(link));
        link.addEventListener("click", (event) => {
            const href = link.getAttribute("href");
            if (!href || href === "#") {
                event.preventDefault();
            }

            link.classList.add("social-link--pressed");
            window.setTimeout(() => {
                link.classList.remove("social-link--pressed");
            }, 150);
        });
    });
}

function createRipple(element) {
    const ripple = document.createElement("div");
    ripple.className = "ripple-effect";
    ripple.style.cssText = `
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
        z-index: 1;
    `;

    element.style.position = "relative";
    element.appendChild(ripple);

    window.setTimeout(() => {
        ripple.remove();
    }, 600);
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `footer-notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        animation: slideInRight 0.3s ease-out;
        ${type === 'success' ? 'background: linear-gradient(135deg, #28a745, #20c997);' : 'background: linear-gradient(135deg, #dc3545, #e74c3c);'}
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add CSS animations dynamically
if (!document.getElementById('modern-footer-styles')) {
    const modernFooterStyle = document.createElement('style');
    modernFooterStyle.id = 'modern-footer-styles';
    modernFooterStyle.textContent = `
    @keyframes ripple {
        to {
            width: 100px;
            height: 100px;
            opacity: 0;
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
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
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
`;
    document.head.appendChild(modernFooterStyle);
}
