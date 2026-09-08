/* ===== Home Hero Interactive Scripts ===== */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Typewriter Animation for Code Preview Card
    const codeEl = document.getElementById('heroCodeTyped');
    if (codeEl) {
        const codeSnippets = [
            `const edvora = {\n  mission: "Free Education",\n  skills: ["Web", "AI", "Python"],\n  cost: 0 // 100% Free\n};`,
            `def start_learning():\n    topics = ["Frontend", "Backend", "Data"]\n    for item in topics:\n        master(item)\n    return "Certificate Ready!"`,
            `function empower() {\n  const future = "Unlimited";\n  const access = "Open to All";\n  return future + " " + access;\n}`
        ];

        let snippetIdx = 0;
        let charIdx = 0;
        let isDeleting = false;
        let typingSpeed = 45;

        function type() {
            const currentSnippet = codeSnippets[snippetIdx];

            if (isDeleting) {
                codeEl.textContent = currentSnippet.substring(0, charIdx - 1);
                charIdx--;
                typingSpeed = 25;
            } else {
                codeEl.textContent = currentSnippet.substring(0, charIdx + 1);
                charIdx++;
                typingSpeed = 45;
            }

            if (!isDeleting && charIdx === currentSnippet.length) {
                typingSpeed = 2500; // Pause at end
                isDeleting = true;
            } else if (isDeleting && charIdx === 0) {
                isDeleting = false;
                snippetIdx = (snippetIdx + 1) % codeSnippets.length;
                typingSpeed = 400; // Pause before new snippet
            }

            setTimeout(type, typingSpeed);
        }

        setTimeout(type, 800);
    }

    // 2. 3D Parallax Tilt Effect on Hero Visual
    const heroVisuals = document.querySelectorAll('.hero2__visual');
    const heroSection = document.querySelector('.hero2');

    if (heroSection && heroVisuals.length > 0) {
        heroSection.addEventListener('mousemove', (e) => {
            const { clientX, clientY } = e;
            const { innerWidth, innerHeight } = window;

            const rotateX = ((clientY / innerHeight) - 0.5) * -12;
            const rotateY = ((clientX / innerWidth) - 0.5) * 12;

            heroVisuals.forEach(visual => {
                visual.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });
        });

        heroSection.addEventListener('mouseleave', () => {
            heroVisuals.forEach(visual => {
                visual.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg)`;
            });
        });
    }

    // 3. Counter Animation for Stats
    const statNums = document.querySelectorAll('.hero2__stat-num');
    statNums.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-target') || stat.textContent, 10);
        if (isNaN(target) || target <= 0) return;

        let count = 0;
        const duration = 1800; // 1.8s
        const stepTime = Math.max(16, Math.floor(duration / target));

        const counter = setInterval(() => {
            count += Math.ceil(target / 40);
            if (count >= target) {
                stat.textContent = target.toLocaleString();
                clearInterval(counter);
            } else {
                stat.textContent = count.toLocaleString();
            }
        }, stepTime);
    });
});
