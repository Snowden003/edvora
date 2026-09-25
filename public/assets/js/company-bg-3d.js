/**
 * EDVORA TECH – 3D Crystal Constellation Background
 * Lightweight, GPU-friendly, pure-canvas 3D animation.
 * Renders floating geometric nodes with connecting mesh on white background.
 * Includes wireframe diamonds, triangles, and glowing circle nodes.
 */
(function () {
    'use strict';

    const canvas = document.getElementById('cp3dCanvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let W, H, raf;
    let nodes = [];
    let scrollOffset = 0;
    let visible = true;

    // Performance: fewer nodes on mobile
    const isMobile = window.innerWidth < 768;
    const TOTAL = isMobile ? 28 : 55;
    const LINK_DIST = isMobile ? 130 : 170;
    const FOCAL = 700;
    const ROT_SPEED = 0.00018;

    const PALETTE = [
        { r: 31, g: 143, b: 255 },   // brand blue
        { r: 0, g: 200, b: 245 },    // cyan
        { r: 139, g: 92, b: 246 },   // purple
        { r: 99, g: 102, b: 241 },   // indigo
        { r: 14, g: 165, b: 233 },   // sky blue
    ];

    function resize() {
        W = canvas.width = canvas.offsetWidth || window.innerWidth;
        H = canvas.height = canvas.offsetHeight || window.innerHeight;
    }

    function createNodes() {
        nodes = [];
        var rangeX = W * 0.65;
        var rangeY = H * 0.55;
        var rangeZ = Math.min(W, H) * 0.5;
        for (var i = 0; i < TOTAL; i++) {
            var shapeRoll = Math.random();
            nodes.push({
                ox: (Math.random() - 0.5) * rangeX,
                oy: (Math.random() - 0.5) * rangeY,
                oz: (Math.random() - 0.5) * rangeZ,
                speed: 0.15 + Math.random() * 0.35,
                phase: Math.random() * Math.PI * 2,
                size: 1.5 + Math.random() * 2.5,
                color: PALETTE[Math.floor(Math.random() * PALETTE.length)],
                shape: shapeRoll > 0.82 ? 'diamond' : (shapeRoll > 0.65 ? 'triangle' : 'circle'),
                rotSpeed: (Math.random() - 0.5) * 0.002,
                localAngle: Math.random() * Math.PI * 2,
            });
        }
    }

    function project(x, y, z) {
        var s = FOCAL / (FOCAL + z);
        return { px: W * 0.5 + x * s, py: H * 0.5 + y * s, s: s };
    }

    function rotY(x, z, a) {
        var c = Math.cos(a), s = Math.sin(a);
        return { x: x * c - z * s, z: x * s + z * c };
    }

    function drawShape(x, y, sz, shape, color, alpha, localAngle) {
        ctx.save();
        ctx.translate(x, y);

        if (shape === 'diamond') {
            ctx.rotate(localAngle);
            var dh = sz * 2.8, dw = sz * 1.8;
            ctx.beginPath();
            ctx.moveTo(0, -dh);
            ctx.lineTo(dw, 0);
            ctx.lineTo(0, dh);
            ctx.lineTo(-dw, 0);
            ctx.closePath();
            ctx.strokeStyle = 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + alpha * 0.65 + ')';
            ctx.lineWidth = 1;
            ctx.stroke();
            ctx.fillStyle = 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + alpha * 0.08 + ')';
            ctx.fill();
        } else if (shape === 'triangle') {
            ctx.rotate(localAngle);
            var ts = sz * 2.5;
            ctx.beginPath();
            ctx.moveTo(0, -ts);
            ctx.lineTo(ts * 0.866, ts * 0.5);
            ctx.lineTo(-ts * 0.866, ts * 0.5);
            ctx.closePath();
            ctx.strokeStyle = 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + alpha * 0.6 + ')';
            ctx.lineWidth = 0.8;
            ctx.stroke();
            ctx.fillStyle = 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + alpha * 0.06 + ')';
            ctx.fill();
        } else {
            // Subtle glow halo
            var g = ctx.createRadialGradient(0, 0, 0, 0, 0, sz * 5);
            g.addColorStop(0, 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + alpha * 0.35 + ')');
            g.addColorStop(1, 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',0)');
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.arc(0, 0, sz * 5, 0, Math.PI * 2);
            ctx.fill();

            // Core dot
            ctx.fillStyle = 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + alpha + ')';
            ctx.beginPath();
            ctx.arc(0, 0, sz, 0, Math.PI * 2);
            ctx.fill();
        }
        ctx.restore();
    }

    function frame(ts) {
        if (!visible) { raf = requestAnimationFrame(frame); return; }
        ts = ts || 0;

        ctx.clearRect(0, 0, W, H);
        var globalAngle = ts * ROT_SPEED;

        // Project all nodes
        var pts = [];
        for (var i = 0; i < nodes.length; i++) {
            var n = nodes[i];
            n.localAngle += n.rotSpeed;
            var fx = n.ox + Math.sin(ts * 0.0003 * n.speed + n.phase) * 30;
            var fy = n.oy + Math.cos(ts * 0.00035 * n.speed + n.phase) * 22 - scrollOffset * 0.06;
            var fz = n.oz + Math.sin(ts * 0.00018 * n.speed + n.phase * 2) * 18;
            var r = rotY(fx, fz, globalAngle);
            var p = project(r.x, fy, r.z);
            pts.push({ px: p.px, py: p.py, s: p.s, node: n });
        }

        // Sort back → front
        pts.sort(function (a, b) { return a.s - b.s; });

        // Draw connecting lines
        for (var i = 0; i < pts.length; i++) {
            for (var j = i + 1; j < pts.length; j++) {
                var dx = pts[i].px - pts[j].px;
                var dy = pts[i].py - pts[j].py;
                var d = Math.sqrt(dx * dx + dy * dy);
                if (d < LINK_DIST) {
                    var lineAlpha = (1 - d / LINK_DIST) * 0.07 * Math.min(pts[i].s, pts[j].s);
                    var c = pts[i].node.color;
                    ctx.beginPath();
                    ctx.strokeStyle = 'rgba(' + c.r + ',' + c.g + ',' + c.b + ',' + lineAlpha + ')';
                    ctx.lineWidth = 0.6;
                    ctx.moveTo(pts[i].px, pts[i].py);
                    ctx.lineTo(pts[j].px, pts[j].py);
                    ctx.stroke();
                }
            }
        }

        // Draw nodes
        for (var k = 0; k < pts.length; k++) {
            var pt = pts[k];
            var alpha = 0.12 + pt.s * 0.15;
            if (alpha > 0.4) alpha = 0.4;
            drawShape(pt.px, pt.py, pt.node.size * pt.s, pt.node.shape, pt.node.color, alpha, pt.node.localAngle);
        }

        raf = requestAnimationFrame(frame);
    }

    // Visibility & scroll
    document.addEventListener('visibilitychange', function () { visible = !document.hidden; });
    window.addEventListener('scroll', function () { scrollOffset = window.scrollY; }, { passive: true });
    window.addEventListener('resize', function () { resize(); createNodes(); });

    // Boot
    resize();
    createNodes();
    raf = requestAnimationFrame(frame);
})();
