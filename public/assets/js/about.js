document.addEventListener('DOMContentLoaded', function() {

    const animatedSections = document.querySelectorAll('.section-animated, .timeline-item');

    const observerOptions = {
        root: null, 
        rootMargin: '0px',
        threshold: 0.15 
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    animatedSections.forEach(section => {
        observer.observe(section);
    });

    // Add hover effect to mission/vision cards for a little extra flair
    const cards = document.querySelectorAll('.card.shadow-lg');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.boxShadow = '0 1rem 3rem rgba(0,0,0,.175)!important';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
            card.style.boxShadow = '0 1rem 3rem rgba(0,0,0,0)!important'; // Reset shadow
        });
    });

});
