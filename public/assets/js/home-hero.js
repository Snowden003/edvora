/**
 * Edvora Tech - High-Performance 3D Hero Engine
 * Features:
 * 1. 3D WebGL/Canvas Cosmic Constellation & Vortex Engine (60fps, Lerp, Depth Projection)
 * 2. Multi-Layer 3D Gyroscopic Card Tilt with Specular Glare & Parallax
 * 3. Animated Cyber Code Terminal (Realistic Cadence & Syntax)
 * 4. Smooth Rolling Number Counters
 * 5. Viewport Intersection Observer for Zero CPU/GPU Waste
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        initHero3DCanvas();
        initHero3DTilt();
        initHeroTerminal();
        initHeroCounters();
    });

    /* =========================================================================
       1. 3D COSMIC CONSTELLATION & VORTEX ENGINE
       ========================================================================= */
    function initHero3DCanvas() {
        const canvas = document.getElementById('hero3DCanvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const heroSection = document.getElementById('heroSection') || canvas.parentElement;

        let width = (canvas.width = heroSection.clientWidth);
        let height = (canvas.height = heroSection.clientHeight);
        let isVisible = true;
        let animFrameId = null;

        // Mouse coordinates and rotation state
        let targetRotX = 0;
        let targetRotY = 0;
        let currentRotX = 0;
        let currentRotY = 0;
        let mouseX = 0;
        let mouseY = 0;
        let isHovered = false;

        // Resize handling with debounce
        function handleResize() {
            width = canvas.width = heroSection.clientWidth;
            height = canvas.height = heroSection.clientHeight;
        }
        window.addEventListener('resize', handleResize, { passive: true });

        // Performance: Pause rendering when hero is scrolled out of view
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    isVisible = entry.isIntersecting;
                    if (isVisible && !animFrameId) {
                        loop();
                    }
                });
            },
            { threshold: 0.05 }
        );
        observer.observe(heroSection);

        // 3D Particles Definition
        const PARTICLE_COUNT = 90;
        const FOV = 420;
        const RADIUS = Math.min(width, height) * 0.75;
        const particles = [];

        // Color palettes: Electric Cyan, Cosmic Violet, Sky Blue, Magenta
        const colors = [
            '#00F0FF',
            '#38BDF8',
            '#818CF8',
            '#A855F7',
            '#00E5FF'
        ];

        // Seed 3D coordinates in a toroidal / spherical volume
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            const theta = Math.random() * Math.PI * 2;
            const phi = Math.acos(Math.random() * 2 - 1);
            const r = (0.35 + Math.random() * 0.65) * RADIUS;

            particles.push({
                x: r * Math.sin(phi) * Math.cos(theta),
                y: r * Math.sin(phi) * Math.sin(theta) * 0.6,
                z: r * Math.cos(phi),
                baseSize: 1.2 + Math.random() * 2.2,
                color: colors[Math.floor(Math.random() * colors.length)],
                orbitSpeed: (Math.random() - 0.5) * 0.003,
                pulseOffset: Math.random() * Math.PI * 2
            });
        }

        // Mouse move tracking over the hero
        heroSection.addEventListener('mousemove', (e) => {
            const rect = heroSection.getBoundingClientRect();
            mouseX = (e.clientX - rect.left) / width - 0.5;
            mouseY = (e.clientY - rect.top) / height - 0.5;
            targetRotY = mouseX * 0.65;
            targetRotX = -mouseY * 0.65;
            isHovered = true;
        }, { passive: true });

        heroSection.addEventListener('mouseleave', () => {
            isHovered = false;
        }, { passive: true });

        let time = 0;

        function render() {
            ctx.clearRect(0, 0, width, height);

            // Subtle continuous idle rotation + smooth lerp to target mouse tilt
            time += 0.007;
            const idleRotY = Math.sin(time * 0.5) * 0.15;
            const idleRotX = Math.cos(time * 0.3) * 0.08;

            const finalTargetY = isHovered ? targetRotY : idleRotY;
            const finalTargetX = isHovered ? targetRotX : idleRotX;

            currentRotY += (finalTargetY - currentRotY) * 0.06;
            currentRotX += (finalTargetX - currentRotX) * 0.06;

            const cosX = Math.cos(currentRotX);
            const sinX = Math.sin(currentRotX);
            const cosY = Math.cos(currentRotY + time * 0.12);
            const sinY = Math.sin(currentRotY + time * 0.12);

            const projected = [];
            const centerX = width * 0.52;
            const centerY = height * 0.48;

            // Project each particle to 2D screen with depth
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];

                // Y-axis rotation
                let x1 = p.x * cosY - p.z * sinY;
                let z1 = p.z * cosY + p.x * sinY;

                // X-axis rotation
                let y1 = p.y * cosX - z1 * sinX;
                let z2 = z1 * cosX + p.y * sinX;

                // Subtle orbit motion
                p.x += Math.sin(time + p.pulseOffset) * 0.15;
                p.y += Math.cos(time + p.pulseOffset) * 0.15;

                const depth = z2 + FOV;
                if (depth > 20) {
                    const scale = FOV / depth;
                    const screenX = centerX + x1 * scale;
                    const screenY = centerY + y1 * scale;

                    // Alpha depends on distance and forward depth
                    const alpha = Math.max(0.12, Math.min(0.9, (z2 + RADIUS) / (2 * RADIUS)));
                    const size = Math.max(0.8, p.baseSize * scale * 1.1);

                    projected.push({
                        screenX,
                        screenY,
                        scale,
                        alpha,
                        size,
                        color: p.color,
                        z: z2,
                        pulse: Math.sin(time * 2 + p.pulseOffset) * 0.4 + 1
                    });
                }
            }

            // Draw connection lines in 3D
            const MAX_DIST = 110;
            ctx.lineWidth = 0.65;
            for (let i = 0; i < projected.length; i++) {
                const p1 = projected[i];
                for (let j = i + 1; j < projected.length; j++) {
                    const p2 = projected[j];
                    const dx = p1.screenX - p2.screenX;
                    const dy = p1.screenY - p2.screenY;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < MAX_DIST) {
                        const lineAlpha = (1 - dist / MAX_DIST) * 0.28 * Math.min(p1.alpha, p2.alpha);
                        ctx.strokeStyle = `rgba(0, 240, 255, ${lineAlpha})`;
                        ctx.beginPath();
                        ctx.moveTo(p1.screenX, p1.screenY);
                        ctx.lineTo(p2.screenX, p2.screenY);
                        ctx.stroke();
                    }
                }
            }

            // Draw particle nodes with luminous glow
            for (let i = 0; i < projected.length; i++) {
                const p = projected[i];
                const finalSize = p.size * p.pulse;

                // Soft outer aura
                const radGrad = ctx.createRadialGradient(
                    p.screenX, p.screenY, 0,
                    p.screenX, p.screenY, finalSize * 3.5
                );
                radGrad.addColorStop(0, p.color);
                radGrad.addColorStop(0.3, p.color);
                radGrad.addColorStop(1, 'rgba(0, 240, 255, 0)');

                ctx.fillStyle = radGrad;
                ctx.globalAlpha = p.alpha * 0.7;
                ctx.beginPath();
                ctx.arc(p.screenX, p.screenY, finalSize * 3.5, 0, Math.PI * 2);
                ctx.fill();

                // Core solid star dot
                ctx.globalAlpha = Math.min(1, p.alpha * 1.2);
                ctx.fillStyle = '#FFFFFF';
                ctx.beginPath();
                ctx.arc(p.screenX, p.screenY, Math.max(0.6, finalSize * 0.55), 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.globalAlpha = 1.0;
        }

        function loop() {
            if (!isVisible) {
                animFrameId = null;
                return;
            }
            render();
            animFrameId = requestAnimationFrame(loop);
        }

        loop();
    }

    /* =========================================================================
       2. 3D GYROSCOPIC CARD TILT & SPECULAR GLARE
       ========================================================================= */
    function initHero3DTilt() {
        const stage = document.getElementById('heroVisualStage');
        const card3D = document.getElementById('heroCard3D');
        if (!stage || !card3D) return;

        const glare = card3D.querySelector('.hero2__card-glare');
        let rect = stage.getBoundingClientRect();
        let mouseX = 0;
        let mouseY = 0;
        let currentTiltX = 0;
        let currentTiltY = 0;
        let targetTiltX = 0;
        let targetTiltY = 0;
        let isHovering = false;

        function updateRect() {
            rect = stage.getBoundingClientRect();
        }
        window.addEventListener('resize', updateRect, { passive: true });
        window.addEventListener('scroll', updateRect, { passive: true });

        const hero = document.getElementById('heroSection') || stage.parentElement;

        hero.addEventListener('mousemove', (e) => {
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            const deltaX = (e.clientX - centerX) / (window.innerWidth / 2);
            const deltaY = (e.clientY - centerY) / (window.innerHeight / 2);

            targetTiltY = deltaX * 18; // Max 18deg Y-rotation
            targetTiltX = -deltaY * 18; // Max 18deg X-rotation
            isHovering = true;

            // Specular Glare light positioning
            if (glare) {
                const glareX = ((e.clientX - rect.left) / rect.width) * 100;
                const glareY = ((e.clientY - rect.top) / rect.height) * 100;
                glare.style.background = `radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255, 255, 255, 0.22) 0%, rgba(0, 240, 255, 0.08) 35%, transparent 70%)`;
                glare.style.opacity = '1';
            }
        }, { passive: true });

        hero.addEventListener('mouseleave', () => {
            targetTiltX = 0;
            targetTiltY = 0;
            isHovering = false;
            if (glare) {
                glare.style.opacity = '0';
            }
        }, { passive: true });

        function animateTilt() {
            currentTiltX += (targetTiltX - currentTiltX) * 0.08;
            currentTiltY += (targetTiltY - currentTiltY) * 0.08;

            card3D.style.transform = `perspective(1200px) rotateX(${currentTiltX.toFixed(2)}deg) rotateY(${currentTiltY.toFixed(2)}deg)`;

            requestAnimationFrame(animateTilt);
        }

        animateTilt();
    }

    /* =========================================================================
       3. ANIMATED CYBER CODE TERMINAL
       ========================================================================= */
    function initHeroTerminal() {
        const codeEl = document.getElementById('heroCodeTyped');
        if (!codeEl) return;

        const snippets = [
            `// 1. Next-Gen Web Stack\nconst edvora = {\n  learners: 2500,\n  tuition: 0,\n  certificate: "Verified"\n};\nawait edvora.launchCareer();`,
            `# 2. Python AI & ML Lab\ndef train_model(data):\n    model = AI.load("edvora-vision")\n    accuracy = model.evaluate()\n    return f"Ready: {accuracy}%"`,
            `// 3. React Interactive Hub\nexport function Masterclass() {\n  const [skills, setSkills] = useState(["FullStack", "AI"]);\n  return <Empower AfghanWomen={skills} />;\n}`
        ];

        let snippetIdx = 0;
        let charIdx = 0;
        let isDeleting = false;
        let speed = 40;

        function type() {
            const current = snippets[snippetIdx];

            if (isDeleting) {
                codeEl.textContent = current.substring(0, charIdx - 1);
                charIdx--;
                speed = 20;
            } else {
                codeEl.textContent = current.substring(0, charIdx + 1);
                charIdx++;
                speed = 38 + Math.random() * 25;
            }

            if (!isDeleting && charIdx === current.length) {
                speed = 2800; // Pause at end of snippet
                isDeleting = true;
            } else if (isDeleting && charIdx === 0) {
                isDeleting = false;
                snippetIdx = (snippetIdx + 1) % snippets.length;
                speed = 450;
            }

            setTimeout(type, speed);
        }

        setTimeout(type, 900);
    }

    /* =========================================================================
       4. SMOOTH LIVE STATS COUNTER
       ========================================================================= */
    function initHeroCounters() {
        const counters = document.querySelectorAll('.hero2__stat-num');
        if (!counters.length) return;

        let hasRun = false;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !hasRun) {
                    hasRun = true;
                    counters.forEach(counter => {
                        const target = parseInt(counter.getAttribute('data-target') || counter.textContent.replace(/,/g, ''), 10);
                        if (isNaN(target) || target <= 0) return;

                        let current = 0;
                        const duration = 2000;
                        const startTime = performance.now();

                        function update(now) {
                            const elapsed = now - startTime;
                            const progress = Math.min(elapsed / duration, 1);
                            // EaseOutExpo
                            const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                            current = Math.floor(ease * target);
                            counter.textContent = current.toLocaleString();

                            if (progress < 1) {
                                requestAnimationFrame(update);
                            } else {
                                counter.textContent = target.toLocaleString() + '+';
                            }
                        }
                        requestAnimationFrame(update);
                    });
                }
            });
        }, { threshold: 0.2 });

        counters.forEach(c => observer.observe(c));
    }
})();
