// Simple scroll spy logic for side dots
        document.addEventListener('scroll', () => {
            const sections = ['hero', 'collection', 'usage', 'security', 'rights'];
            const dots = document.querySelectorAll('.nav-dot');

            sections.forEach((id, index) => {
                const el = document.getElementById(id);
                if (el) {
                    const rect = el.getBoundingClientRect();
                    if (rect.top >= 0 && rect.top <= window.innerHeight / 2) {
                        dots.forEach(d => d.classList.remove('active'));
                        dots[index].classList.add('active');
                    }
                }
            });
        });