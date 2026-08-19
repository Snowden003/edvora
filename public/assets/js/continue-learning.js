document.addEventListener('DOMContentLoaded', function() {
    var progressBars = document.querySelectorAll('.premium-progress-bar');
    
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var bar = entry.target;
                var progress = bar.getAttribute('data-progress') || 0;
                bar.style.width = progress + '%';
                observer.unobserve(bar);
            }
        });
    }, { threshold: 0.3 });

    progressBars.forEach(function(bar) {
        bar.style.width = '0%';
        bar.style.transition = 'width 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        observer.observe(bar);
    });
});
