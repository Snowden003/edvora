import React, { useState, useEffect, useRef, useMemo } from 'react';
import { Head, router } from '@inertiajs/react';
import {
    ShieldCheck,
    Search,
    QrCode,
    Mail,
    Send,
    Download,
    Copy,
    Check,
    X,
    Cpu,
    ArrowRight,
    Building2,
    Award,
    CheckCircle2
} from 'lucide-react';

export default function TeamIndex({ members = [], departments = [], auth = {}, unreadNotificationsCount = 0 }) {
    const user = auth?.user;

    // Search and filter state
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedDept, setSelectedDept] = useState('ALL');
    const [activeQrMember, setActiveQrMember] = useState(null);
    const [copiedUrl, setCopiedUrl] = useState(false);

    // Navbar dropdown state
    const [activeDropdown, setActiveDropdown] = useState(null);
    const [userDropdownOpen, setUserDropdownOpen] = useState(false);
    const [mobileDrawerOpen, setMobileDrawerOpen] = useState(false);
    const [mobileSubMenu, setMobileSubMenu] = useState('about');
    const [isScrolled, setIsScrolled] = useState(false);

    // Canvas ref for 3D Background
    const canvasRef = useRef(null);
    const navRef = useRef(null);

    // Close dropdowns on click outside & track scroll
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (navRef.current && !navRef.current.contains(e.target)) {
                setActiveDropdown(null);
                setUserDropdownOpen(false);
            }
        };
        const handleScroll = () => {
            setIsScrolled(window.scrollY > 20);
        };
        document.addEventListener('click', handleClickOutside);
        window.addEventListener('scroll', handleScroll);
        return () => {
            document.removeEventListener('click', handleClickOutside);
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    // Filter members dynamically
    const filteredMembers = useMemo(() => {
        return members.filter((member) => {
            const matchesDept =
                selectedDept === 'ALL' ||
                (member.department && member.department.toLowerCase() === selectedDept.toLowerCase());

            const query = searchQuery.trim().toLowerCase();
            if (!query) return matchesDept;

            const name = (member.name || '').toLowerCase();
            const role = (member.role_title || '').toLowerCase();
            const dept = (member.department || '').toLowerCase();
            const bio = (member.bio || '').toLowerCase();
            const empId = (member.employee_id || '').toLowerCase();

            const matchesQuery =
                name.includes(query) ||
                role.includes(query) ||
                dept.includes(query) ||
                bio.includes(query) ||
                empId.includes(query);

            return matchesDept && matchesQuery;
        });
    }, [members, selectedDept, searchQuery]);

    // Handle copying member URL
    const handleCopy = (url) => {
        navigator.clipboard.writeText(url).then(() => {
            setCopiedUrl(true);
            setTimeout(() => setCopiedUrl(false), 2200);
        });
    };

    // Logout handler
    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    // Dashboard URL helper based on role
    const getDashboardUrl = () => {
        if (!user) return '/login';
        if (user.role === 'admin') return '/admin/dashboard';
        if (user.role === 'teacher') return '/teacher/dashboard';
        return '/student/dashboard';
    };

    const getNotificationsUrl = () => {
        if (!user) return '/login';
        if (user.role === 'teacher') return '/teacher/notifications';
        if (user.role === 'student') return '/student/notifications';
        return '/admin/dashboard';
    };

    // ─── 3D INTERACTIVE CANVAS BACKGROUND (Subtle, Atmospheric) ───
    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let animationFrameId;
        let width = (canvas.width = window.innerWidth);
        let height = (canvas.height = window.innerHeight);

        const handleResize = () => {
            if (!canvas) return;
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        };
        window.addEventListener('resize', handleResize);

        // 3D Nodes Simulation
        const NODE_COUNT = window.innerWidth < 768 ? 50 : 100;
        const nodes = [];
        const colors = [
            'rgba(13, 148, 136, ', // Teal
            'rgba(14, 165, 233, ', // Cyan
            'rgba(139, 92, 246, ', // Purple
            'rgba(16, 185, 129, ', // Emerald
        ];

        for (let i = 0; i < NODE_COUNT; i++) {
            nodes.push({
                x: (Math.random() - 0.5) * width * 1.5,
                y: (Math.random() - 0.5) * height * 1.5,
                z: Math.random() * 800 - 400,
                radius: Math.random() * 2.2 + 1,
                colorBase: colors[Math.floor(Math.random() * colors.length)],
                vx: (Math.random() - 0.5) * 0.35,
                vy: (Math.random() - 0.5) * 0.35,
                vz: (Math.random() - 0.5) * 0.4,
            });
        }

        // Floating 3D Geometric Polyhedron Nodes
        const polyNodes = [
            { x: -180, y: -120, z: -100 },
            { x: 180, y: -120, z: -100 },
            { x: 180, y: 120, z: -100 },
            { x: -180, y: 120, z: -100 },
            { x: 0, y: 0, z: 120 },
            { x: 0, y: 0, z: -200 },
        ];
        let polyAngleX = 0;
        let polyAngleY = 0;

        const fov = 450;

        const render = () => {
            ctx.clearRect(0, 0, width, height);

            polyAngleX += 0.0025;
            polyAngleY += 0.004;

            const cx = width / 2;
            const cy = height / 2;

            // Render projected 3D nodes
            const projected = [];
            for (let i = 0; i < nodes.length; i++) {
                const node = nodes[i];

                node.x += node.vx;
                node.y += node.vy;
                node.z += node.vz;

                if (node.x < -width) node.x = width;
                if (node.x > width) node.x = -width;
                if (node.y < -height) node.y = height;
                if (node.y > height) node.y = -height;
                if (node.z < -400) node.z = 400;
                if (node.z > 400) node.z = -400;

                const scale = fov / (fov + node.z + 500);
                if (scale <= 0) continue;

                const px = cx + node.x * scale;
                const py = cy + node.y * scale;
                const alpha = Math.min(Math.max((scale - 0.2) * 1.5, 0.15), 0.85);

                projected.push({ px, py, scale, alpha, colorBase: node.colorBase });

                ctx.beginPath();
                ctx.arc(px, py, Math.max(node.radius * scale, 0.8), 0, Math.PI * 2);
                ctx.fillStyle = `${node.colorBase}${alpha})`;
                ctx.shadowColor = 'rgba(14, 165, 233, 0.3)';
                ctx.shadowBlur = 6;
                ctx.fill();
            }

            // Draw Constellation Lines between nearby nodes
            ctx.shadowBlur = 0;
            const maxDist = width < 768 ? 85 : 120;
            for (let i = 0; i < projected.length; i++) {
                for (let j = i + 1; j < projected.length; j++) {
                    const p1 = projected[i];
                    const p2 = projected[j];
                    const dx = p1.px - p2.px;
                    const dy = p1.py - p2.py;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < maxDist) {
                        const lineAlpha = (1 - dist / maxDist) * 0.16 * ((p1.alpha + p2.alpha) / 2);
                        ctx.beginPath();
                        ctx.moveTo(p1.px, p1.py);
                        ctx.lineTo(p2.px, p2.py);
                        ctx.strokeStyle = `rgba(56, 189, 248, ${lineAlpha})`;
                        ctx.lineWidth = 0.7;
                        ctx.stroke();
                    }
                }
            }

            // Draw 3D Floating Polyhedron (Diamond Wireframe)
            const polyProj = polyNodes.map((pn) => {
                const cx1 = pn.x * Math.cos(polyAngleY) - pn.z * Math.sin(polyAngleY);
                const cz1 = pn.x * Math.sin(polyAngleY) + pn.z * Math.cos(polyAngleY);
                const cy1 = pn.y * Math.cos(polyAngleX) - cz1 * Math.sin(polyAngleX);
                const cz2 = pn.y * Math.sin(polyAngleX) + cz1 * Math.cos(polyAngleX);

                const polyScale = fov / (fov + cz2 + 400);
                return {
                    x: cx + (cx1 + 350) * polyScale,
                    y: cy + (cy1 - 150) * polyScale,
                };
            });

            const polyEdges = [
                [0, 1], [1, 2], [2, 3], [3, 0],
                [4, 0], [4, 1], [4, 2], [4, 3],
                [5, 0], [5, 1], [5, 2], [5, 3]
            ];

            ctx.strokeStyle = 'rgba(13, 148, 136, 0.1)';
            ctx.lineWidth = 1;
            polyEdges.forEach(([from, to]) => {
                ctx.beginPath();
                ctx.moveTo(polyProj[from].x, polyProj[from].y);
                ctx.lineTo(polyProj[to].x, polyProj[to].y);
                ctx.stroke();
            });

            animationFrameId = requestAnimationFrame(render);
        };

        render();

        return () => {
            cancelAnimationFrame(animationFrameId);
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    return (
        <div dir="ltr" className="min-h-screen bg-[#030712] text-slate-100 font-sans selection:bg-cyan-500 selection:text-white relative overflow-x-hidden antialiased">
            <Head>
                <title>Our Team - Edvora Tech</title>
                <meta
                    name="description"
                    content="Meet the passionate educators, software engineers, and visionaries behind Edvora Tech. Verified cryptographic faculty roster and direct channels."
                />

            </Head>

            {/* ─── 3D CANVAS BACKGROUND ─── */}
            <canvas
                ref={canvasRef}
                className="fixed inset-0 pointer-events-none z-0 opacity-80"
                style={{ width: '100vw', height: '100vh' }}
            />

            {/* Deep Cyber Ambient Glows */}
            <div className="fixed -top-40 -left-40 w-96 h-96 bg-teal-500/10 rounded-full blur-[140px] pointer-events-none" />
            <div className="fixed top-1/3 -right-40 w-[30rem] h-[30rem] bg-cyan-500/10 rounded-full blur-[150px] pointer-events-none" />
            <div className="fixed -bottom-40 left-1/3 w-[32rem] h-[32rem] bg-indigo-500/10 rounded-full blur-[160px] pointer-events-none" />

            {/* Subtle Grid Pattern Overlay */}
            <div className="fixed inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:32px_32px] opacity-35 pointer-events-none z-0" />

            {/* ===== MOBILE DRAWER ===== */}
            {mobileDrawerOpen && (
                <div
                    className="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 animate-in fade-in duration-200"
                    onClick={() => setMobileDrawerOpen(false)}
                />
            )}
            <div
                className={`fixed top-0 bottom-0 left-0 w-80 max-w-[85vw] bg-[#071328]/98 backdrop-blur-2xl border-r border-cyan-500/20 z-50 p-6 flex flex-col justify-between overflow-y-auto transition-transform duration-300 ease-out shadow-2xl ${
                    mobileDrawerOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div>
                    {/* Drawer Header */}
                    <div className="flex items-center justify-between pb-6 border-b border-white/10">
                        <a href="/" className="flex items-center gap-2 text-white font-bold text-lg">
                            <span className="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-500 to-cyan-500 flex items-center justify-center text-slate-950 shadow-md">
                                <i className="bi bi-mortarboard-fill text-lg"></i>
                            </span>
                            <span>Edvora Tech</span>
                        </a>
                        <button
                            type="button"
                            onClick={() => setMobileDrawerOpen(false)}
                            className="w-8 h-8 rounded-lg bg-slate-800/60 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition"
                            aria-label="Close"
                        >
                            <i className="bi bi-x-lg"></i>
                        </button>
                    </div>

                    {/* Drawer Nav Items */}
                    <div className="py-4 space-y-1">
                        <a href="/" className="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition">
                            <i className="bi bi-house text-slate-400"></i>
                            <span>Home</span>
                        </a>
                        <a href="/courses" className="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition">
                            <i className="bi bi-journal-bookmark text-slate-400"></i>
                            <span>Courses</span>
                        </a>
                        <a href="/books" className="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition">
                            <i className="bi bi-book text-slate-400"></i>
                            <span>Books</span>
                        </a>
                        <a href="/teachers" className="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition">
                            <i className="bi bi-person-workspace text-slate-400"></i>
                            <span>Teachers</span>
                        </a>

                        {/* Community Accordion */}
                        <div>
                            <button
                                type="button"
                                onClick={() => setMobileSubMenu(mobileSubMenu === 'community' ? null : 'community')}
                                className="w-full flex items-center justify-between px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition"
                            >
                                <div className="flex items-center gap-3">
                                    <i className="bi bi-people text-slate-400"></i>
                                    <span>Community</span>
                                </div>
                                <i className={`bi bi-chevron-down text-xs transition-transform ${mobileSubMenu === 'community' ? 'rotate-180 text-cyan-400' : 'text-slate-500'}`}></i>
                            </button>
                            {mobileSubMenu === 'community' && (
                                <div className="pl-9 pr-3 py-1 space-y-1">
                                    <a href="/events" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-calendar-event text-cyan-400"></i>
                                        <span>Events</span>
                                    </a>
                                    <a href="/leaderboard" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-bar-chart-steps text-cyan-400"></i>
                                        <span>Leaderboard</span>
                                    </a>
                                </div>
                            )}
                        </div>

                        {/* Resources Accordion */}
                        <div>
                            <button
                                type="button"
                                onClick={() => setMobileSubMenu(mobileSubMenu === 'resources' ? null : 'resources')}
                                className="w-full flex items-center justify-between px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition"
                            >
                                <div className="flex items-center gap-3">
                                    <i className="bi bi-layers text-slate-400"></i>
                                    <span>Resources</span>
                                </div>
                                <i className={`bi bi-chevron-down text-xs transition-transform ${mobileSubMenu === 'resources' ? 'rotate-180 text-cyan-400' : 'text-slate-500'}`}></i>
                            </button>
                            {mobileSubMenu === 'resources' && (
                                <div className="pl-9 pr-3 py-1 space-y-1">
                                    <a href="/roadmap" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-signpost-split text-cyan-400"></i>
                                        <span>Roadmap</span>
                                    </a>
                                    <a href="/foundation" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-building text-cyan-400"></i>
                                        <span>Foundation</span>
                                    </a>
                                </div>
                            )}
                        </div>

                        {/* About Accordion */}
                        <div>
                            <button
                                type="button"
                                onClick={() => setMobileSubMenu(mobileSubMenu === 'about' ? null : 'about')}
                                className="w-full flex items-center justify-between px-3.5 py-2.5 text-sm font-bold text-cyan-300 bg-cyan-500/10 rounded-xl transition"
                            >
                                <div className="flex items-center gap-3">
                                    <i className="bi bi-info-circle text-cyan-400"></i>
                                    <span>About</span>
                                </div>
                                <i className={`bi bi-chevron-down text-xs transition-transform ${mobileSubMenu === 'about' ? 'rotate-180 text-cyan-400' : 'text-slate-500'}`}></i>
                            </button>
                            {mobileSubMenu === 'about' && (
                                <div className="pl-9 pr-3 py-1 space-y-1">
                                    <a href="/about" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-info-circle text-cyan-400"></i>
                                        <span>About Us</span>
                                    </a>
                                    <a href="/story" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-book text-cyan-400"></i>
                                        <span>Our Story</span>
                                    </a>
                                    <a href="/how-we-work" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-gear text-cyan-400"></i>
                                        <span>How We Work</span>
                                    </a>
                                    <a href="/team" className="flex items-center gap-2 py-2 text-xs font-bold text-cyan-300 transition">
                                        <i className="bi bi-people text-cyan-300"></i>
                                        <span>Our Team</span>
                                    </a>
                                    <div className="my-1 border-t border-white/5" />
                                    <a href="/terms" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-file-text text-cyan-400"></i>
                                        <span>Terms</span>
                                    </a>
                                    <a href="/privacy" className="flex items-center gap-2 py-2 text-xs font-semibold text-slate-400 hover:text-cyan-300 transition">
                                        <i className="bi bi-shield-check text-cyan-400"></i>
                                        <span>Privacy</span>
                                    </a>
                                </div>
                            )}
                        </div>

                        <a href="/contact" className="flex items-center gap-3 px-3.5 py-2.5 text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 rounded-xl transition">
                            <i className="bi bi-envelope text-slate-400"></i>
                            <span>Contact</span>
                        </a>
                    </div>
                </div>

                {/* Drawer Bottom Auth */}
                <div className="pt-4 border-t border-white/10">
                    {!user ? (
                        <div className="grid grid-cols-2 gap-2">
                            <a href="/login" className="py-2.5 text-center text-xs font-bold text-white rounded-xl border border-white/20 hover:bg-white/10 transition">
                                Login
                            </a>
                            <a href="/register" className="py-2.5 text-center text-xs font-black text-slate-950 bg-gradient-to-r from-teal-400 to-cyan-400 rounded-xl shadow-md shadow-cyan-500/20 transition">
                                Sign Up
                            </a>
                        </div>
                    ) : (
                        <div className="space-y-3">
                            <div className="flex items-center justify-between p-2.5 rounded-xl bg-slate-900/60 border border-white/10">
                                <div className="flex items-center gap-2.5 min-w-0">
                                    <i className="bi bi-person-circle text-xl text-cyan-400 shrink-0"></i>
                                    <div className="truncate">
                                        <p className="text-xs font-bold text-white truncate">{user.name}</p>
                                        <p className="text-[10px] text-slate-400 capitalize">{user.role || 'Member'}</p>
                                    </div>
                                </div>
                                <a href={getNotificationsUrl()} className="relative p-2 text-slate-300 hover:text-white" title="Notifications">
                                    <i className="bi bi-bell-fill"></i>
                                    {unreadNotificationsCount > 0 && (
                                        <span className="absolute top-1 right-1 flex items-center justify-center min-w-[14px] h-3.5 px-0.5 text-[9px] font-black text-white bg-red-500 rounded-full">
                                            {unreadNotificationsCount}
                                        </span>
                                    )}
                                </a>
                            </div>
                            <div className="grid grid-cols-2 gap-2">
                                <a href="/profile" className="py-2 text-center text-xs font-semibold text-slate-300 hover:text-white bg-slate-900/40 rounded-xl border border-white/5 transition">
                                    Profile
                                </a>
                                <a href={getDashboardUrl()} className="py-2 text-center text-xs font-semibold text-cyan-300 hover:text-cyan-200 bg-cyan-500/10 rounded-xl border border-cyan-500/20 transition">
                                    Dashboard
                                </a>
                            </div>
                            <button
                                type="button"
                                onClick={handleLogout}
                                className="w-full py-2 text-center text-xs font-semibold text-red-400 hover:text-red-300 bg-red-500/10 rounded-xl border border-red-500/20 transition"
                            >
                                <i className="bi bi-box-arrow-right me-1.5"></i>Logout
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* ===== DESKTOP FLOATING NAVBAR ===== */}
            <nav
                ref={navRef}
                className={`fixed top-3 sm:top-4 left-3 sm:left-6 right-3 sm:right-6 max-w-7xl mx-auto z-50 rounded-2xl transition-all duration-300 px-4 sm:px-6 py-2.5 sm:py-3 border backdrop-blur-xl ${
                    isScrolled
                        ? 'bg-[#051024]/90 border-cyan-500/30 shadow-[0_20px_48px_rgba(0,0,0,0.5),inset_0_1px_0_rgba(255,255,255,0.2)]'
                        : 'bg-[#06142c]/75 border-white/15 shadow-[0_16px_36px_rgba(0,0,0,0.35),inset_0_1px_0_rgba(255,255,255,0.15)]'
                }`}
            >
                <div className="flex items-center justify-between w-full">
                    {/* Brand / Logo */}
                    <a href="/" className="flex items-center gap-2 text-white font-bold text-base sm:text-lg tracking-tight hover:text-cyan-300 transition-colors group">
                        <span className="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-500 to-cyan-500 flex items-center justify-center text-slate-950 shadow-md shadow-cyan-500/25 group-hover:scale-105 transition-transform">
                            <i className="bi bi-mortarboard-fill text-lg"></i>
                        </span>
                        <span className="bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">Edvora Tech</span>
                    </a>

                    {/* Desktop Navigation Links */}
                    <div className="hidden lg:flex items-center gap-1 xl:gap-2">
                        <a href="/" className="px-3 py-1.5 rounded-xl text-xs xl:text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                            Home
                        </a>
                        <a href="/courses" className="px-3 py-1.5 rounded-xl text-xs xl:text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                            Courses
                        </a>
                        <a href="/books" className="px-3 py-1.5 rounded-xl text-xs xl:text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                            Books
                        </a>
                        <a href="/teachers" className="px-3 py-1.5 rounded-xl text-xs xl:text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                            Teachers
                        </a>

                        {/* Community Dropdown */}
                        <div
                            className="relative"
                            onMouseEnter={() => setActiveDropdown('community')}
                            onMouseLeave={() => setActiveDropdown(null)}
                        >
                            <button
                                type="button"
                                onClick={() => setActiveDropdown(activeDropdown === 'community' ? null : 'community')}
                                className={`flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs xl:text-sm font-semibold transition ${
                                    activeDropdown === 'community'
                                        ? 'text-cyan-300 bg-cyan-500/10'
                                        : 'text-slate-300 hover:text-white hover:bg-white/5'
                                }`}
                            >
                                <span>Community</span>
                                <i className={`bi bi-chevron-down text-[10px] transition-transform ${activeDropdown === 'community' ? 'rotate-180 text-cyan-400' : 'text-slate-400'}`}></i>
                            </button>

                            {activeDropdown === 'community' && (
                                <div className="absolute top-full left-0 mt-2 w-52 rounded-2xl bg-[#071328]/95 backdrop-blur-2xl border border-cyan-500/20 shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150">
                                    <span className="px-3 py-1 text-[10px] font-mono font-bold uppercase tracking-wider text-cyan-400 block">Engage</span>
                                    <a href="/events" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-calendar-event text-cyan-400"></i>
                                        <span>Events</span>
                                    </a>
                                    <a href="/leaderboard" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-bar-chart-steps text-cyan-400"></i>
                                        <span>Leaderboard</span>
                                    </a>
                                </div>
                            )}
                        </div>

                        {/* Resources Dropdown */}
                        <div
                            className="relative"
                            onMouseEnter={() => setActiveDropdown('resources')}
                            onMouseLeave={() => setActiveDropdown(null)}
                        >
                            <button
                                type="button"
                                onClick={() => setActiveDropdown(activeDropdown === 'resources' ? null : 'resources')}
                                className={`flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs xl:text-sm font-semibold transition ${
                                    activeDropdown === 'resources'
                                        ? 'text-cyan-300 bg-cyan-500/10'
                                        : 'text-slate-300 hover:text-white hover:bg-white/5'
                                }`}
                            >
                                <span>Resources</span>
                                <i className={`bi bi-chevron-down text-[10px] transition-transform ${activeDropdown === 'resources' ? 'rotate-180 text-cyan-400' : 'text-slate-400'}`}></i>
                            </button>

                            {activeDropdown === 'resources' && (
                                <div className="absolute top-full left-0 mt-2 w-52 rounded-2xl bg-[#071328]/95 backdrop-blur-2xl border border-cyan-500/20 shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150">
                                    <span className="px-3 py-1 text-[10px] font-mono font-bold uppercase tracking-wider text-cyan-400 block">Learn</span>
                                    <a href="/roadmap" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-signpost-split text-cyan-400"></i>
                                        <span>Roadmap</span>
                                    </a>
                                    <a href="/foundation" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-building text-cyan-400"></i>
                                        <span>Foundation</span>
                                    </a>
                                </div>
                            )}
                        </div>

                        {/* About Dropdown (ACTIVE on /team) */}
                        <div
                            className="relative"
                            onMouseEnter={() => setActiveDropdown('about')}
                            onMouseLeave={() => setActiveDropdown(null)}
                        >
                            <button
                                type="button"
                                onClick={() => setActiveDropdown(activeDropdown === 'about' ? null : 'about')}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs xl:text-sm font-bold text-cyan-300 bg-cyan-500/15 border border-cyan-500/30 transition shadow-[0_0_15px_rgba(14,165,233,0.15)]"
                            >
                                <span>About</span>
                                <i className={`bi bi-chevron-down text-[10px] transition-transform ${activeDropdown === 'about' ? 'rotate-180' : ''}`}></i>
                            </button>

                            {activeDropdown === 'about' && (
                                <div className="absolute top-full left-0 mt-2 w-56 rounded-2xl bg-[#071328]/95 backdrop-blur-2xl border border-cyan-500/20 shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150">
                                    <span className="px-3 py-1 text-[10px] font-mono font-bold uppercase tracking-wider text-cyan-400 block">Company</span>
                                    <a href="/about" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-info-circle text-cyan-400"></i>
                                        <span>About Us</span>
                                    </a>
                                    <a href="/story" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-book text-cyan-400"></i>
                                        <span>Our Story</span>
                                    </a>
                                    <a href="/how-we-work" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-gear text-cyan-400"></i>
                                        <span>How We Work</span>
                                    </a>
                                    <a href="/team" className="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-cyan-300 bg-cyan-500/20 border border-cyan-500/30 rounded-xl transition shadow-sm">
                                        <i className="bi bi-people text-cyan-300"></i>
                                        <span>Our Team</span>
                                    </a>
                                    <div className="my-1.5 border-t border-white/10" />
                                    <a href="/terms" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-file-text text-cyan-400"></i>
                                        <span>Terms</span>
                                    </a>
                                    <a href="/privacy" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                        <i className="bi bi-shield-check text-cyan-400"></i>
                                        <span>Privacy</span>
                                    </a>
                                </div>
                            )}
                        </div>

                        <a href="/contact" className="px-3 py-1.5 rounded-xl text-xs xl:text-sm font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition">
                            Contact
                        </a>
                    </div>

                    {/* Right Action: Auth Buttons or User Menu */}
                    <div className="flex items-center gap-3">
                        {!user ? (
                            <div className="hidden sm:flex items-center gap-2">
                                <a
                                    href="/login"
                                    className="px-4 py-1.5 text-xs font-semibold text-white rounded-full border border-white/20 hover:border-white/40 hover:bg-white/10 transition active:scale-95"
                                >
                                    Login
                                </a>
                                <a
                                    href="/register"
                                    className="px-4 py-1.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-teal-400 to-cyan-400 hover:from-teal-300 hover:to-cyan-300 rounded-full shadow-[0_0_15px_rgba(14,165,233,0.35)] transition active:scale-95"
                                >
                                    Sign Up
                                </a>
                            </div>
                        ) : (
                            <div className="flex items-center gap-2">
                                {/* Notification Link */}
                                <a
                                    href={getNotificationsUrl()}
                                    className="relative p-2 text-slate-300 hover:text-white rounded-xl hover:bg-white/5 transition"
                                    title="Notifications"
                                >
                                    <i className="bi bi-bell-fill text-base"></i>
                                    {unreadNotificationsCount > 0 && (
                                        <span className="absolute top-1 right-1 flex items-center justify-center min-w-[16px] h-4 px-1 text-[10px] font-black text-white bg-red-500 rounded-full shadow-md animate-pulse">
                                            {unreadNotificationsCount}
                                        </span>
                                    )}
                                </a>

                                {/* User Profile Dropdown */}
                                <div className="relative">
                                    <button
                                        type="button"
                                        onClick={() => setUserDropdownOpen(!userDropdownOpen)}
                                        className="flex items-center gap-2 px-3.5 py-1.5 text-xs font-semibold text-white rounded-full border border-white/20 hover:border-white/40 bg-white/5 transition active:scale-95"
                                    >
                                        <i className="bi bi-person-circle text-cyan-400 text-sm"></i>
                                        <span className="max-w-[100px] truncate">{user.name}</span>
                                        <i className={`bi bi-chevron-down text-[10px] text-slate-400 transition-transform ${userDropdownOpen ? 'rotate-180' : ''}`}></i>
                                    </button>

                                    {userDropdownOpen && (
                                        <div className="absolute top-full right-0 mt-2 w-52 rounded-2xl bg-[#071328]/95 backdrop-blur-2xl border border-cyan-500/20 shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150">
                                            <a href="/profile" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                                <i className="bi bi-person-circle text-cyan-400"></i>
                                                <span>Profile</span>
                                            </a>
                                            <a href={getDashboardUrl()} className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                                <i className="bi bi-grid-1x2 text-cyan-400"></i>
                                                <span>Dashboard</span>
                                            </a>
                                            {user.role === 'admin' && (
                                                <>
                                                    <a href="/admin-panel/company-pages" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                                        <i className="bi bi-file-earmark-text text-cyan-400"></i>
                                                        <span>Company Pages</span>
                                                    </a>
                                                    <a href="/admin-panel/site-settings" className="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white hover:bg-cyan-500/10 rounded-xl transition">
                                                        <i className="bi bi-gear text-cyan-400"></i>
                                                        <span>Site Settings</span>
                                                    </a>
                                                </>
                                            )}
                                            <div className="my-1.5 border-t border-white/10" />
                                            <button
                                                type="button"
                                                onClick={handleLogout}
                                                className="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl transition text-left"
                                            >
                                                <i className="bi bi-box-arrow-right text-red-400"></i>
                                                <span>Logout</span>
                                            </button>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Mobile Menu Toggler Button */}
                        <button
                            type="button"
                            onClick={() => setMobileDrawerOpen(true)}
                            className="lg:hidden p-2 text-slate-300 hover:text-white rounded-xl hover:bg-white/5 transition"
                            aria-label="Open Navigation Menu"
                        >
                            <i className="bi bi-list text-2xl"></i>
                        </button>
                    </div>
                </div>
            </nav>

            {/* ─── MAIN CONTENT CONTAINER (pt-36 ensures ample breathing room under the header) ─── */}
            <main className="relative z-10 pt-36 sm:pt-40 pb-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                {/* ─── HERO SECTION ─── */}
                <section className="text-center max-w-4xl mx-auto mb-16 sm:mb-20 space-y-6">
                    {/* Floating Verification Tag */}
                    <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900/80 border border-cyan-500/30 shadow-[0_0_20px_rgba(14,165,233,0.18)] backdrop-blur-md">
                        <span className="relative flex h-2 w-2">
                            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75" />
                            <span className="relative inline-flex rounded-full h-2 w-2 bg-cyan-500" />
                        </span>
                        <span className="text-[11px] sm:text-xs font-mono font-bold tracking-widest uppercase bg-gradient-to-r from-teal-300 to-cyan-300 bg-clip-text text-transparent">
                            EDVORA TECH FACULTY & ARCHITECTS // VERIFIED ROSTER
                        </span>
                    </div>

                    {/* Main Title */}
                    <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-[1.12]">
                        Architecting the Future of <br className="hidden sm:inline" />
                        <span className="bg-gradient-to-r from-teal-300 via-cyan-400 to-indigo-400 bg-clip-text text-transparent drop-shadow-[0_0_35px_rgba(14,165,233,0.35)]">
                            Modern Tech Education
                        </span>
                    </h1>

                    {/* Subtitle */}
                    <p className="text-base sm:text-lg text-slate-300/90 font-normal max-w-2xl mx-auto leading-relaxed">
                        A multidisciplinary collective of distinguished educators, systems architects, and research pioneers
                        dedicated to empowering next-generation engineers worldwide.
                    </p>

                    {/* Key Stats Pills */}
                    <div className="pt-2 flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-xs font-semibold">
                        <div className="px-4 py-2 rounded-2xl bg-slate-900/60 border border-white/10 backdrop-blur-md flex items-center gap-2 text-slate-200 shadow-sm">
                            <ShieldCheck className="w-4 h-4 text-emerald-400" />
                            <span>100% Cryptographically Verified</span>
                        </div>
                        <div className="px-4 py-2 rounded-2xl bg-slate-900/60 border border-white/10 backdrop-blur-md flex items-center gap-2 text-slate-200 shadow-sm">
                            <Cpu className="w-4 h-4 text-cyan-400" />
                            <span>Distributed Engineering & AI</span>
                        </div>
                        <div className="px-4 py-2 rounded-2xl bg-slate-900/60 border border-white/10 backdrop-blur-md flex items-center gap-2 text-slate-200 shadow-sm">
                            <Award className="w-4 h-4 text-indigo-400" />
                            <span>Industry Accredited Mentors</span>
                        </div>
                    </div>
                </section>

                {/* ─── SEARCH & FILTER CONTROLS ─── */}
                <section className="mb-12 space-y-6 max-w-5xl mx-auto">
                    <div className="backdrop-blur-2xl bg-slate-900/60 border border-white/10 rounded-3xl p-4 sm:p-5 shadow-[0_12px_40px_rgba(0,0,0,0.4)] flex flex-col md:flex-row items-center gap-4">
                        {/* Search Input */}
                        <div className="relative w-full md:flex-1">
                            <Search className="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" />
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Search faculty by name, specialization, department or ID..."
                                className="w-full pl-12 pr-10 py-3 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-500/20 text-white placeholder-slate-400 text-sm transition outline-none"
                            />
                            {searchQuery && (
                                <button
                                    type="button"
                                    onClick={() => setSearchQuery('')}
                                    className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            )}
                        </div>

                        {/* Quick Count Badge */}
                        <div className="shrink-0 flex items-center gap-2 text-xs font-mono text-slate-400 px-3 py-2 rounded-xl bg-slate-950/50 border border-slate-800">
                            <span className="w-2 h-2 rounded-full bg-cyan-400 animate-pulse" />
                            <span>Showing {filteredMembers.length} of {members.length} Members</span>
                        </div>
                    </div>

                    {/* Department Tabs */}
                    <div className="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none no-scrollbar justify-start sm:justify-center">
                        <button
                            type="button"
                            onClick={() => setSelectedDept('ALL')}
                            className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 ${selectedDept === 'ALL'
                                    ? 'bg-gradient-to-r from-teal-500 to-cyan-500 text-slate-950 shadow-lg shadow-cyan-500/25 scale-105'
                                    : 'bg-slate-900/60 hover:bg-slate-800/80 text-slate-300 border border-white/5 hover:border-white/10'
                                }`}
                        >
                            <span>All Faculty</span>
                            <span className={`text-[10px] px-1.5 py-0.5 rounded-full ${selectedDept === 'ALL' ? 'bg-slate-950/20 text-slate-950 font-black' : 'bg-slate-800 text-slate-300'
                                }`}>
                                {members.length}
                            </span>
                        </button>

                        {departments.map((dept) => {
                            const count = members.filter((m) => m.department === dept).length;
                            const isActive = selectedDept.toLowerCase() === dept.toLowerCase();
                            return (
                                <button
                                    key={dept}
                                    type="button"
                                    onClick={() => setSelectedDept(dept)}
                                    className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 ${isActive
                                            ? 'bg-gradient-to-r from-teal-500 to-cyan-500 text-slate-950 shadow-lg shadow-cyan-500/25 scale-105'
                                            : 'bg-slate-900/60 hover:bg-slate-800/80 text-slate-300 border border-white/5 hover:border-white/10'
                                        }`}
                                >
                                    <span>{dept}</span>
                                    <span className={`text-[10px] px-1.5 py-0.5 rounded-full ${isActive ? 'bg-slate-950/20 text-slate-950 font-black' : 'bg-slate-800 text-slate-300'
                                        }`}>
                                        {count}
                                    </span>
                                </button>
                            );
                        })}
                    </div>
                </section>

                {/* ─── FACULTY & TEAM CARDS GRID (Stable & Solid - No Mouse Wobble) ─── */}
                {filteredMembers.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        {filteredMembers.map((member) => (
                            <FacultyCard
                                key={member.id || member.uuid}
                                member={member}
                                onOpenQr={() => setActiveQrMember(member)}
                            />
                        ))}
                    </div>
                ) : (
                    /* Empty Search State */
                    <div className="text-center py-20 backdrop-blur-xl bg-slate-900/40 border border-white/10 rounded-3xl p-8 max-w-md mx-auto space-y-4">
                        <div className="w-14 h-14 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center mx-auto text-slate-400">
                            <Search className="w-7 h-7" />
                        </div>
                        <h3 className="text-lg font-bold text-white">No Faculty Found</h3>
                        <p className="text-xs text-slate-400">
                            No team members match your current filters. Try changing your search query or department selection.
                        </p>
                        <button
                            type="button"
                            onClick={() => {
                                setSearchQuery('');
                                setSelectedDept('ALL');
                            }}
                            className="px-4 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 text-xs font-bold transition"
                        >
                            Reset All Filters
                        </button>
                    </div>
                )}
            </main>

            {/* ─── QR CODE MODAL ─── */}
            {activeQrMember && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xl animate-in fade-in duration-200"
                    onClick={() => setActiveQrMember(null)}
                >
                    <div
                        className="relative w-full max-w-sm rounded-[2.5rem] bg-gradient-to-b from-slate-900 to-slate-950 border border-cyan-500/30 shadow-[0_0_60px_rgba(14,165,233,0.3)] p-6 sm:p-7 text-center space-y-5"
                        onClick={(e) => e.stopPropagation()}
                    >
                        {/* Close button */}
                        <button
                            type="button"
                            onClick={() => setActiveQrMember(null)}
                            className="absolute right-5 top-5 p-2 rounded-full bg-slate-800/70 hover:bg-slate-700 text-slate-400 hover:text-white transition"
                        >
                            <X className="w-4 h-4" />
                        </button>

                        {/* Modal Header */}
                        <div className="space-y-1 pt-2">
                            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[11px] font-mono font-bold">
                                <ShieldCheck className="w-3.5 h-3.5" />
                                <span>VERIFIED FACULTY ID</span>
                            </div>
                            <h3 className="text-xl font-black text-white">{activeQrMember.name}</h3>
                            <p className="text-xs text-cyan-400 font-semibold">{activeQrMember.role_title}</p>
                        </div>

                        {/* High-Res QR Card Frame */}
                        <div className="p-4 sm:p-5 rounded-3xl bg-white shadow-2xl border border-slate-200 inline-block mx-auto relative group">
                            {activeQrMember.qr_data_uri ? (
                                <img
                                    src={activeQrMember.qr_data_uri}
                                    alt={`${activeQrMember.name} QR Code`}
                                    className="w-48 h-48 sm:w-52 sm:h-52 object-contain mx-auto"
                                />
                            ) : (
                                <div className="w-48 h-48 flex items-center justify-center text-slate-400">
                                    <QrCode className="w-16 h-16 animate-pulse" />
                                </div>
                            )}
                            <div className="mt-2 text-center">
                                <span className="font-mono text-[11px] font-black text-slate-800 tracking-wider">
                                    {activeQrMember.employee_id || 'EDV-VERIFIED'}
                                </span>
                            </div>
                        </div>

                        {/* Instructions */}
                        <p className="text-xs text-slate-300">
                            Scan with any smartphone camera to inspect verified credentials or save direct contact information.
                        </p>

                        {/* Action Buttons */}
                        <div className="grid grid-cols-2 gap-2.5 pt-1">
                            <button
                                type="button"
                                onClick={() => handleCopy(activeQrMember.public_url || window.location.origin + `/team/member/${activeQrMember.uuid}`)}
                                className="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-2xl bg-slate-800/80 hover:bg-slate-700/80 text-white text-xs font-bold border border-slate-700 transition active:scale-95"
                            >
                                {copiedUrl ? <Check className="w-4 h-4 text-emerald-400" /> : <Copy className="w-4 h-4 text-cyan-400" />}
                                <span>{copiedUrl ? 'Copied!' : 'Copy Link'}</span>
                            </button>

                            <a
                                href={activeQrMember.vcard_url || `/team/member/${activeQrMember.uuid}/vcard`}
                                download
                                className="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-2xl bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-400 hover:to-cyan-400 text-slate-950 text-xs font-black shadow-md shadow-cyan-500/20 transition active:scale-95"
                            >
                                <Download className="w-4 h-4" />
                                <span>Save vCard</span>
                            </a>
                        </div>

                        {/* Full Profile Link */}
                        <div className="pt-2">
                            <a
                                href={`/team/member/${activeQrMember.uuid}`}
                                className="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-2xl bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-200 hover:text-white text-xs font-bold transition group"
                            >
                                <span>Open Full Digital ID Profile</span>
                                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform text-cyan-400" />
                            </a>
                        </div>
                    </div>
                </div>
            )}

            {/* ─── FOOTER ─── */}
            <footer className="border-t border-white/10 bg-slate-950/80 backdrop-blur-2xl relative z-10 py-12 px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
                <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                    <div className="flex items-center gap-2.5">
                        <div className="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center">
                            <i className="bi bi-mortarboard-fill text-sm"></i>
                        </div>
                        <span className="font-bold text-white tracking-wide">Edvora Tech Learning Ecosystem</span>
                    </div>

                    <div className="flex flex-wrap items-center justify-center gap-6 font-medium">
                        <a href="/courses" className="hover:text-white transition">Courses</a>
                        <a href="/books" className="hover:text-white transition">Books</a>
                        <a href="/team" className="hover:text-white text-cyan-400 transition">Faculty</a>
                        <a href="/roadmap" className="hover:text-white transition">Roadmap</a>
                        <a href="/about" className="hover:text-white transition">About</a>
                        <a href="/contact" className="hover:text-white transition">Contact</a>
                    </div>

                    <p className="text-[11px] text-slate-400 font-mono">
                        © 2026 Edvora Tech Inc. All verified rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    );
}

// ─── STABLE GLASS FACULTY CARD COMPONENT (No Mouse Wobble Tilt) ───
function FacultyCard({ member, onOpenQr }) {
    return (
        <div className="h-full">
            <div className="group relative h-full flex flex-col rounded-[2.2rem] backdrop-blur-2xl bg-gradient-to-b from-slate-900/85 via-slate-950/80 to-slate-950/95 border border-white/10 hover:border-cyan-400/40 shadow-[0_15px_35px_rgba(0,0,0,0.5)] hover:shadow-[0_20px_45px_rgba(14,165,233,0.18)] hover:-translate-y-1.5 transition-all duration-300 overflow-hidden">
                {/* Top Cyber Department Banner */}
                <div className="relative h-28 bg-gradient-to-r from-teal-800/80 via-cyan-900/80 to-slate-900/90 p-4 flex items-start justify-between overflow-hidden border-b border-white/5">
                    {/* Subtle grid lines in banner */}
                    <div className="absolute inset-0 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:12px_12px] opacity-15 pointer-events-none" />
                    <div className="absolute -top-12 -right-12 w-28 h-28 bg-cyan-400/20 rounded-full blur-2xl pointer-events-none" />

                    {/* Employee ID */}
                    <div className="relative z-10 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-950/70 backdrop-blur-md border border-white/10 text-[11px] font-mono font-bold text-slate-200 shadow-sm">
                        <span className="text-cyan-400">ID:</span>
                        <span>{member.employee_id || 'EDV-1001'}</span>
                    </div>

                    {/* Verified Status Pill */}
                    <div className="relative z-10 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-950/70 backdrop-blur-md border border-emerald-500/30 text-[11px] font-bold text-emerald-400 shadow-sm">
                        <span className="relative flex h-2 w-2">
                            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" />
                            <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500" />
                        </span>
                        <span>Verified</span>
                    </div>
                </div>

                {/* Card Body */}
                <div className="px-6 pb-6 pt-0 flex-1 flex flex-col justify-between relative z-20">
                    <div>
                        {/* Avatar Bar */}
                        <div className="relative -mt-12 mb-4 flex items-end justify-between">
                            <div className="relative group/avatar">
                                <div className="absolute -inset-1 rounded-2xl bg-gradient-to-r from-teal-400 via-cyan-400 to-indigo-500 opacity-60 blur-md group-hover/avatar:opacity-100 transition duration-300" />
                                <img
                                    src={member.avatar_url}
                                    alt={member.name}
                                    className="relative w-20 h-20 rounded-2xl object-cover border-4 border-slate-950 shadow-xl bg-slate-900"
                                    onError={(e) => {
                                        e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                            member.name
                                        )}&background=0d9488&color=ffffff&size=256&bold=true`;
                                    }}
                                />
                                {member.status === 'active' && (
                                    <div
                                        className="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-slate-950 flex items-center justify-center text-white text-[10px] shadow"
                                        title="Active Personnel"
                                    >
                                        <Check className="w-3 h-3 stroke-[3]" />
                                    </div>
                                )}
                            </div>

                            {/* Scan QR Trigger */}
                            <button
                                type="button"
                                onClick={onOpenQr}
                                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/90 text-slate-200 hover:text-white text-xs font-bold border border-slate-700 shadow-sm active:scale-95 transition group/btn"
                            >
                                <QrCode className="w-3.5 h-3.5 text-cyan-400 group-hover/btn:rotate-12 transition-transform" />
                                <span>Scan ID</span>
                            </button>
                        </div>

                        {/* Name and Designation */}
                        <div className="space-y-1 mb-3">
                            <h2 className="text-xl font-extrabold text-white group-hover:text-cyan-300 transition-colors tracking-tight">
                                {member.name}
                            </h2>
                            <p className="text-xs sm:text-sm font-bold bg-gradient-to-r from-teal-300 to-cyan-300 bg-clip-text text-transparent leading-snug">
                                {member.role_title}
                            </p>
                        </div>

                        {/* Department Badge */}
                        {member.department && (
                            <div className="mb-4">
                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-900/90 border border-slate-800 text-[11px] font-semibold text-slate-300">
                                    <Building2 className="w-3 h-3 text-cyan-400" />
                                    <span>{member.department}</span>
                                </span>
                            </div>
                        )}

                        {/* Bio Description */}
                        {member.bio && (
                            <p className="text-xs text-slate-300/85 leading-relaxed line-clamp-3 mb-5 font-normal">
                                "{member.bio}"
                            </p>
                        )}
                    </div>

                    {/* Bottom Actions Area */}
                    <div className="pt-4 border-t border-slate-800/80 space-y-3">
                        {/* Social / Channel Icons */}
                        <div className="flex items-center gap-2">
                            {member.linkedin && (
                                <a
                                    href={member.linkedin.startsWith('http') ? member.linkedin : `https://linkedin.com/in/${member.linkedin}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="w-8 h-8 rounded-xl bg-slate-900/80 hover:bg-indigo-600/20 text-slate-400 hover:text-indigo-400 border border-slate-800 transition flex items-center justify-center text-sm"
                                    title="LinkedIn Profile"
                                >
                                    <i className="bi bi-linkedin"></i>
                                </a>
                            )}
                            {member.github && (
                                <a
                                    href={member.github.startsWith('http') ? member.github : `https://github.com/${member.github}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="w-8 h-8 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition flex items-center justify-center text-sm"
                                    title="GitHub Repository"
                                >
                                    <i className="bi bi-github"></i>
                                </a>
                            )}
                            {member.telegram && (
                                <a
                                    href={`https://t.me/${member.telegram.replace('@', '')}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="w-8 h-8 rounded-xl bg-slate-900/80 hover:bg-cyan-600/20 text-slate-400 hover:text-cyan-400 border border-slate-800 transition flex items-center justify-center text-sm"
                                    title="Telegram Messenger"
                                >
                                    <i className="bi bi-telegram"></i>
                                </a>
                            )}
                            {member.email && (
                                <a
                                    href={`mailto:${member.email}`}
                                    className="w-8 h-8 rounded-xl bg-slate-900/80 hover:bg-teal-600/20 text-slate-400 hover:text-teal-400 border border-slate-800 transition flex items-center justify-center text-sm"
                                    title="Send Email"
                                >
                                    <i className="bi bi-envelope-fill"></i>
                                </a>
                            )}

                            {/* Direct Profile Link Button */}
                            <a
                                href={`/team/member/${member.uuid}`}
                                className="ml-auto inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-teal-500/20 to-cyan-500/20 hover:from-teal-500/30 hover:to-cyan-500/30 text-cyan-300 border border-cyan-500/30 text-xs font-bold transition group/link shadow-sm"
                            >
                                <span>Digital ID</span>
                                <ArrowRight className="w-3.5 h-3.5 group-hover/link:translate-x-1 transition-transform" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
