/**
 * EDVORA TECH - COMPANY & LEGAL PAGES INTERACTION CONTROLLER
 * Lightweight, zero-dependency, ultra-fast preloader & interactive controls.
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. FAST, ULTRA-SMOOTH PRELOADER DISMISSAL
    const preloader = document.getElementById('cpPreloader');
    if (preloader) {
        const dismissPreloader = () => {
            if (!preloader.classList.contains('cp-loaded')) {
                preloader.classList.add('cp-loaded');
                setTimeout(() => {
                    if (preloader.parentNode) {
                        preloader.parentNode.removeChild(preloader);
                    }
                }, 550);
            }
        };

        if (document.readyState === 'complete') {
            setTimeout(dismissPreloader, 150);
        } else {
            window.addEventListener('load', () => setTimeout(dismissPreloader, 150));
            // Safety timeout: Never let the user wait longer than 850ms even on slow network assets
            setTimeout(dismissPreloader, 850);
        }
    }

    // 2. TEAM CATEGORY FILTERING (About Us Page)
    const filterBtns = document.querySelectorAll('.cp-filter-btn');
    const teamMembers = document.querySelectorAll('.cp-team-col');

    if (filterBtns.length > 0 && teamMembers.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter');
                teamMembers.forEach(col => {
                    if (filter === 'all' || col.getAttribute('data-category') === filter) {
                        col.style.display = '';
                        setTimeout(() => {
                            col.style.opacity = '1';
                            col.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        col.style.opacity = '0';
                        col.style.transform = 'translateY(15px)';
                        setTimeout(() => {
                            col.style.display = 'none';
                        }, 250);
                    }
                });
            });
        });
    }

    // 3. REAL-TIME SEARCH (Terms of Service Page)
    const legalSearch = document.getElementById('legalSearchInput');
    const legalCards = document.querySelectorAll('.cp-legal-module-card');

    if (legalSearch && legalCards.length > 0) {
        legalSearch.addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase().trim();
            legalCards.forEach(card => {
                const title = (card.querySelector('.module-title') || {}).textContent || '';
                const text = (card.querySelector('.module-text') || {}).textContent || '';
                const tldr = (card.querySelector('.tldr-text') || {}).textContent || '';
                const content = (title + ' ' + text + ' ' + tldr).toLowerCase();

                if (query === '' || content.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // 4. PRIVACY PRISM SCROLLSPY (Privacy Policy Page)
    const prismDots = document.querySelectorAll('.cp-prism-dot');
    const prismPanes = document.querySelectorAll('.cp-prism-pane');

    if (prismDots.length > 0 && prismPanes.length > 0) {
        window.addEventListener('scroll', () => {
            let currentId = '';
            const scrollPos = window.scrollY + 250;

            prismPanes.forEach(pane => {
                if (pane.offsetTop <= scrollPos) {
                    currentId = pane.getAttribute('id');
                }
            });

            prismDots.forEach(dot => {
                dot.classList.remove('active');
                if (dot.getAttribute('href') === '#' + currentId) {
                    dot.classList.add('active');
                }
            });
        }, { passive: true });
    }
});
