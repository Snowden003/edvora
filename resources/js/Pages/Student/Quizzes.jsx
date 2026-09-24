import React, { useState, useEffect, useRef, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import {
    ClipboardCheck,
    Trophy,
    Sparkles,
    Zap,
    Clock,
    CheckCircle2,
    PlayCircle,
    RotateCcw,
    Search,
    BookOpen,
    HelpCircle,
    ArrowRight,
    ArrowLeft,
    Filter,
    Award,
    Star,
    Layers,
    X,
    ExternalLink,
    AlertCircle,
    Check,
    GraduationCap,
    SlidersHorizontal,
    TrendingUp,
    ShieldCheck
} from 'lucide-react';

/* =========================================================================
   1. Interactive 3D Canvas Background (Cosmic Constellation)
   ========================================================================= */
const Hero3DCanvas = () => {
    const canvasRef = useRef(null);

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let animId;
        let width = (canvas.width = canvas.parentElement?.offsetWidth || window.innerWidth);
        let height = (canvas.height = canvas.parentElement?.offsetHeight || 360);

        let targetRotX = 0;
        let targetRotY = 0;
        let curRotX = 0;
        let curRotY = 0;

        const handleResize = () => {
            if (!canvas || !canvas.parentElement) return;
            width = canvas.width = canvas.parentElement.offsetWidth;
            height = canvas.height = canvas.parentElement.offsetHeight;
        };

        const handleMouseMove = (e) => {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            targetRotY = ((x - width / 2) / width) * 0.35;
            targetRotX = -((y - height / 2) / height) * 0.35;
        };

        window.addEventListener('resize', handleResize);
        const parent = canvas.parentElement;
        if (parent) {
            parent.addEventListener('mousemove', handleMouseMove);
        }

        const numParticles = 45;
        const particles = [];
        const radius = Math.min(width, height) * 0.55;

        for (let i = 0; i < numParticles; i++) {
            const theta = Math.random() * 2 * Math.PI;
            const phi = Math.acos(Math.random() * 2 - 1);
            const r = radius * (0.3 + Math.random() * 0.7);

            particles.push({
                x: r * Math.sin(phi) * Math.cos(theta),
                y: r * Math.sin(phi) * Math.sin(theta),
                z: r * Math.cos(phi),
                vx: (Math.random() - 0.5) * 0.25,
                vy: (Math.random() - 0.5) * 0.25,
                vz: (Math.random() - 0.5) * 0.25,
                size: Math.random() * 2.2 + 1,
                color: i % 3 === 0 ? '#38bdf8' : i % 3 === 1 ? '#818cf8' : '#34d399',
            });
        }

        let baseAngle = 0;

        const render = () => {
            baseAngle += 0.0025;
            curRotX += (targetRotX - curRotX) * 0.05;
            curRotY += (targetRotY - curRotY) * 0.05;

            ctx.clearRect(0, 0, width, height);

            const cosY = Math.cos(curRotY + baseAngle);
            const sinY = Math.sin(curRotY + baseAngle);
            const cosX = Math.cos(curRotX);
            const sinX = Math.sin(curRotX);
            const focalLength = Math.max(width, height) * 0.9;

            const projected = [];
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.x += p.vx;
                p.y += p.vy;
                p.z += p.vz;

                const d = Math.hypot(p.x, p.y, p.z);
                if (d > radius) {
                    p.vx *= -1;
                    p.vy *= -1;
                    p.vz *= -1;
                }

                const x1 = p.x * cosY - p.z * sinY;
                const z1 = p.z * cosY + p.x * sinY;
                const y1 = p.y * cosX - z1 * sinX;
                const z2 = z1 * cosX + p.y * sinX;

                const dist = focalLength + z2;
                if (dist > 50) {
                    const scale = focalLength / dist;
                    projected.push({
                        x: width / 2 + x1 * scale,
                        y: height / 2 + y1 * scale,
                        scale,
                        size: p.size * scale,
                        color: p.color,
                    });
                }
            }

            // Depth connection lines
            ctx.lineWidth = 0.5;
            for (let i = 0; i < projected.length; i++) {
                for (let j = i + 1; j < projected.length; j++) {
                    const dx = projected[i].x - projected[j].x;
                    const dy = projected[i].y - projected[j].y;
                    const dist = Math.hypot(dx, dy);

                    if (dist < 90) {
                        const alpha = (1 - dist / 90) * 0.22;
                        ctx.strokeStyle = `rgba(56, 189, 248, ${alpha})`;
                        ctx.beginPath();
                        ctx.moveTo(projected[i].x, projected[i].y);
                        ctx.lineTo(projected[j].x, projected[j].y);
                        ctx.stroke();
                    }
                }
            }

            // Draw particle glow points
            for (let i = 0; i < projected.length; i++) {
                const pt = projected[i];
                ctx.beginPath();
                ctx.arc(pt.x, pt.y, Math.max(0.8, pt.size), 0, Math.PI * 2);
                ctx.fillStyle = pt.color;
                ctx.globalAlpha = Math.min(1, pt.scale * 0.75);
                ctx.fill();
            }
            ctx.globalAlpha = 1.0;

            animId = requestAnimationFrame(render);
        };

        render();

        return () => {
            window.removeEventListener('resize', handleResize);
            if (parent) parent.removeEventListener('mousemove', handleMouseMove);
            cancelAnimationFrame(animId);
        };
    }, []);

    return (
        <canvas
            ref={canvasRef}
            className="absolute inset-0 pointer-events-none z-0 opacity-80"
        />
    );
};

/* =========================================================================
   2. 3D Tilt Card Component (Micro-Physics & Specular Glare)
   ========================================================================= */
const TiltCard = ({ children, className = '', glowColor = 'rgba(31, 143, 255, 0.22)' }) => {
    const cardRef = useRef(null);
    const [style, setStyle] = useState({ transform: 'perspective(1000px) rotateX(0deg) rotateY(0deg)' });
    const [glare, setGlare] = useState({ x: 50, y: 50, opacity: 0 });

    const handleMouseMove = (e) => {
        const card = cardRef.current;
        if (!card) return;
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = -((y - centerY) / centerY) * 6;
        const rotateY = ((x - centerX) / centerX) * 6;

        setStyle({
            transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.012, 1.012, 1.012)`,
            transition: 'transform 0.08s ease-out',
        });

        setGlare({
            x: (x / rect.width) * 100,
            y: (y / rect.height) * 100,
            opacity: 0.14,
        });
    };

    const handleMouseLeave = () => {
        setStyle({
            transform: 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)',
            transition: 'transform 0.4s cubic-bezier(0.23, 1, 0.32, 1)',
        });
        setGlare((prev) => ({ ...prev, opacity: 0 }));
    };

    return (
        <div
            ref={cardRef}
            onMouseMove={handleMouseMove}
            onMouseLeave={handleMouseLeave}
            style={{ ...style, transformStyle: 'preserve-3d' }}
            className={`relative transition-all duration-300 ${className}`}
        >
            <div
                className="absolute inset-0 pointer-events-none rounded-3xl z-20 transition-opacity duration-300"
                style={{
                    background: `radial-gradient(circle at ${glare.x}% ${glare.y}%, ${glowColor} 0%, transparent 60%)`,
                    opacity: glare.opacity,
                }}
            />
            {children}
        </div>
    );
};

/* =========================================================================
   3. Main Student Quizzes Page Component
   ========================================================================= */
export default function Quizzes({
    quizzes = [],
    courses = [],
    stats = {
        total: 0,
        passed: 0,
        in_progress: 0,
        available: 0,
        total_xp: 0,
        avg_score: 0
    }
}) {
    const { t, isRtl } = useLanguage();
    const isFa = isRtl;

    // Filters state
    const [selectedTab, setSelectedTab] = useState('all'); // 'all' | 'available' | 'passed' | 'in_progress'
    const [selectedCourse, setSelectedCourse] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');
    const [previewQuiz, setPreviewQuiz] = useState(null);

    // Filtered quizzes calculation
    const filteredQuizzes = useMemo(() => {
        return quizzes.filter((quiz) => {
            // Status Tab Filter
            if (selectedTab === 'available') {
                if (quiz.user_stats.status !== 'available') return false;
            } else if (selectedTab === 'passed') {
                if (quiz.user_stats.status !== 'passed') return false;
            } else if (selectedTab === 'in_progress') {
                if (quiz.user_stats.status !== 'in_progress') return false;
            }

            // Course Filter
            if (selectedCourse !== 'all') {
                if (String(quiz.course?.id) !== String(selectedCourse)) return false;
            }

            // Search Query Filter
            if (searchQuery.trim() !== '') {
                const q = searchQuery.toLowerCase().trim();
                const titleMatch = quiz.title?.toLowerCase().includes(q);
                const descMatch = quiz.description?.toLowerCase().includes(q);
                const courseMatch = quiz.course?.title?.toLowerCase().includes(q);
                if (!titleMatch && !descMatch && !courseMatch) return false;
            }

            return true;
        });
    }, [quizzes, selectedTab, selectedCourse, searchQuery]);

    const clearFilters = () => {
        setSelectedTab('all');
        setSelectedCourse('all');
        setSearchQuery('');
    };

    return (
        <StudentLayout title={`${t('quizzes')} - ${t('brand_title')}`}>
            <Head>
                <title>{`${t('quizzes')} - ${t('brand_title')}`}</title>
                <meta
                    name="description"
                    content="امتحانات آنلاین، آزمون‌های آزمایشی و کویزهای درسی در پورتال شاگردان ادوُرا تِک."
                />
            </Head>

            <div className="space-y-8 max-w-7xl mx-auto pb-16">

                {/* =========================================================
                    1. Hero Banner (3D Cosmic Ambient Matching ScoringHelp)
                   ========================================================= */}
                <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#061E3E] via-slate-900 to-[#1F8FFF] text-white p-6 sm:p-10 lg:p-12 border border-blue-900/50">
                    <Hero3DCanvas />

                    <div className="relative z-10 space-y-4 max-w-3xl">
                        <span className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-cyan-300 backdrop-blur-md shadow-sm">
                            <Sparkles size={14} className="text-cyan-400" />
                            <span>{t('quizzes_hub_badge')}</span>
                        </span>

                        <h1 className="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                            {t('quizzes_hub_title')}
                        </h1>

                        <p className="text-sm sm:text-base text-blue-100 leading-relaxed font-normal">
                            {t('quizzes_hub_desc')}
                        </p>

                        {/* Quick Navigation Chips */}
                        <div className="pt-2 flex flex-wrap items-center gap-3">
                            <Link
                                href="/scoring-help"
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/20 text-cyan-200 text-xs font-bold transition-all backdrop-blur-md"
                            >
                                <Zap size={14} className="text-cyan-400" />
                                <span>{t('quiz_scoring_rules_link')}</span>
                            </Link>

                            <Link
                                href="/leaderboard"
                                className="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold transition-all shadow-lg shadow-cyan-500/25"
                            >
                                <Trophy size={14} />
                                <span>{t('leaderboard')}</span>
                            </Link>
                        </div>
                    </div>
                </div>

                {/* =========================================================
                    2. 3D Stat Tiles Grid
                   ========================================================= */}
                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    {/* Card 1: Total Quizzes */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-blue-400/40"
                        glowColor="rgba(31, 143, 255, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-xs">
                                <ClipboardCheck size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-slate-400">Total</span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-slate-900 font-mono">
                            {stats.total}
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('stat_total_quizzes')}
                        </p>
                    </TiltCard>

                    {/* Card 2: Passed */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-emerald-400/40"
                        glowColor="rgba(16, 185, 129, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-xs">
                                <CheckCircle2 size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-emerald-600 font-mono">
                                {stats.total > 0 ? Math.round((stats.passed / stats.total) * 100) : 0}%
                            </span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">
                            {stats.passed}
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('stat_passed_quizzes')}
                        </p>
                    </TiltCard>

                    {/* Card 3: Ready / Available */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-sky-400/40"
                        glowColor="rgba(14, 165, 233, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shadow-xs">
                                <PlayCircle size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-sky-600">Ready</span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-sky-600 font-mono">
                            {stats.available}
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('stat_available_quizzes')}
                        </p>
                    </TiltCard>

                    {/* Card 4: Average Score */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-amber-400/40"
                        glowColor="rgba(245, 158, 11, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-xs">
                                <Award size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-amber-600">Avg</span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-amber-600 font-mono">
                            {stats.avg_score}%
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('stat_avg_score')}
                        </p>
                    </TiltCard>

                    {/* Card 5: Total XP Earned */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-purple-400/40 col-span-2 sm:col-span-1"
                        glowColor="rgba(139, 92, 246, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-xs">
                                <Zap size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-purple-600">XP</span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-purple-600 font-mono">
                            +{stats.total_xp}
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('stat_xp_earned')}
                        </p>
                    </TiltCard>
                </div>

                {/* =========================================================
                    3. Filters & Search Bar
                   ========================================================= */}
                <div className="bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800/80 shadow-xs space-y-4 transition-all">
                    <div className="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                        {/* Tab Pills */}
                        <div className="flex flex-wrap items-center gap-2 p-1.5 bg-slate-100/90 dark:bg-slate-900/90 border dark:border-slate-800/90 rounded-2xl">
                            <button
                                type="button"
                                onClick={() => setSelectedTab('all')}
                                className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
                                    selectedTab === 'all'
                                        ? 'bg-white dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-slate-900 dark:text-slate-950 shadow-sm dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                        : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-white/50 dark:hover:bg-slate-800/60'
                                }`}
                            >
                                <span>{t('filter_all')}</span>
                                <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                    selectedTab === 'all'
                                        ? 'bg-slate-100 dark:bg-slate-950/25 text-slate-800 dark:text-slate-950 font-extrabold'
                                        : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-cyan-300'
                                }`}>
                                    {quizzes.length}
                                </span>
                            </button>

                            <button
                                type="button"
                                onClick={() => setSelectedTab('available')}
                                className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
                                    selectedTab === 'available'
                                        ? 'bg-white dark:bg-sky-500 text-sky-600 dark:text-slate-950 shadow-sm dark:shadow-[0_0_15px_rgba(14,165,233,0.4)]'
                                        : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-white/50 dark:hover:bg-slate-800/60'
                                }`}
                            >
                                <span>{t('filter_available')}</span>
                                <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                    selectedTab === 'available'
                                        ? 'bg-sky-50 dark:bg-slate-950/25 text-sky-700 dark:text-slate-950 font-extrabold'
                                        : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-cyan-300'
                                }`}>
                                    {stats.available}
                                </span>
                            </button>

                            <button
                                type="button"
                                onClick={() => setSelectedTab('passed')}
                                className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
                                    selectedTab === 'passed'
                                        ? 'bg-white dark:bg-emerald-500 text-emerald-600 dark:text-slate-950 shadow-sm dark:shadow-[0_0_15px_rgba(16,185,129,0.4)]'
                                        : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-white/50 dark:hover:bg-slate-800/60'
                                }`}
                            >
                                <span>{t('filter_passed')}</span>
                                <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                    selectedTab === 'passed'
                                        ? 'bg-emerald-50 dark:bg-slate-950/25 text-emerald-700 dark:text-slate-950 font-extrabold'
                                        : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-cyan-300'
                                }`}>
                                    {stats.passed}
                                </span>
                            </button>

                            <button
                                type="button"
                                onClick={() => setSelectedTab('in_progress')}
                                className={`px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 ${
                                    selectedTab === 'in_progress'
                                        ? 'bg-white dark:bg-amber-500 text-amber-600 dark:text-slate-950 shadow-sm dark:shadow-[0_0_15px_rgba(245,158,11,0.4)]'
                                        : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-white/50 dark:hover:bg-slate-800/60'
                                }`}
                            >
                                <span>{t('filter_in_progress')}</span>
                                <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                    selectedTab === 'in_progress'
                                        ? 'bg-amber-50 dark:bg-slate-950/25 text-amber-700 dark:text-slate-950 font-extrabold'
                                        : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-cyan-300'
                                }`}>
                                    {stats.in_progress}
                                </span>
                            </button>
                        </div>

                        {/* Search & Course Selector */}
                        <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            {/* Course Select */}
                            {courses.length > 0 && (
                                <div className="relative min-w-[200px]">
                                    <select
                                        value={selectedCourse}
                                        onChange={(e) => setSelectedCourse(e.target.value)}
                                        className="w-full h-11 px-4 text-xs font-bold bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none cursor-pointer"
                                    >
                                        <option value="all">{t('all_courses_filter')}</option>
                                        {courses.map((c) => (
                                            <option key={c.id} value={c.id}>
                                                {c.title}
                                            </option>
                                        ))}
                                    </select>
                                    <div className={`absolute top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 ${
                                        isRtl ? 'left-3' : 'right-3'
                                    }`}>
                                        <SlidersHorizontal size={14} />
                                    </div>
                                </div>
                            )}

                            {/* Search Input */}
                            <div className="relative flex-1 sm:w-64">
                                <input
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    placeholder={t('search_quizzes_placeholder')}
                                    className={`w-full h-11 text-xs font-medium bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 ${
                                        isRtl ? 'pr-9 pl-8' : 'pl-9 pr-8'
                                    }`}
                                />
                                <div className={`absolute top-1/2 -translate-y-1/2 text-slate-400 ${
                                    isRtl ? 'right-3' : 'left-3'
                                }`}>
                                    <Search size={15} />
                                </div>
                                {searchQuery && (
                                    <button
                                        type="button"
                                        onClick={() => setSearchQuery('')}
                                        className={`absolute top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 ${
                                            isRtl ? 'left-3' : 'right-3'
                                        }`}
                                    >
                                        <X size={14} />
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                {/* =========================================================
                    4. Quizzes Cards Grid (3D Minimal Experience)
                   ========================================================= */}
                {filteredQuizzes.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {filteredQuizzes.map((quiz) => {
                            const uStats = quiz.user_stats;
                            const isPassed = uStats.status === 'passed';
                            const isInProgress = uStats.status === 'in_progress';
                            const isAvailable = uStats.status === 'available';
                            const isExhausted = uStats.status === 'exhausted';

                            // Badge styling
                            let badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                            let badgeText = t('quiz_available_badge');
                            let cardGlow = 'rgba(31, 143, 255, 0.15)';

                            if (isPassed) {
                                badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                badgeText = t('quiz_passed_badge');
                                cardGlow = 'rgba(16, 185, 129, 0.2)';
                            } else if (isInProgress) {
                                badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                                badgeText = t('quiz_in_progress_badge');
                                cardGlow = 'rgba(245, 158, 11, 0.18)';
                            } else if (isExhausted) {
                                badgeBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                badgeText = t('quiz_exhausted_badge');
                                cardGlow = 'rgba(100, 116, 139, 0.12)';
                            }

                            return (
                                <TiltCard
                                    key={quiz.id}
                                    glowColor={cardGlow}
                                    className="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-blue-400/50 flex flex-col justify-between"
                                >
                                    <div>
                                        {/* Top Meta: Course tag + Status Badge */}
                                        <div className="flex items-center justify-between gap-2 mb-4">
                                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-[11px] font-bold truncate max-w-[65%]">
                                                <BookOpen size={13} className="text-slate-500 shrink-0" />
                                                <span className="truncate">{quiz.course?.title}</span>
                                            </span>

                                            <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border ${badgeBg}`}>
                                                {isPassed && <Check size={12} />}
                                                {isInProgress && <Clock size={12} />}
                                                {isAvailable && <Sparkles size={12} />}
                                                <span>{badgeText}</span>
                                            </span>
                                        </div>

                                        {/* Title & Description */}
                                        <h3 className="text-base sm:text-lg font-black text-slate-900 leading-snug line-clamp-2 hover:text-blue-600 transition-colors">
                                            {quiz.title}
                                        </h3>

                                        <p className="text-xs sm:text-sm text-slate-500 line-clamp-2 mt-2 leading-relaxed">
                                            {quiz.description || (isFa ? 'برای سنجش یادگیری، شرکت در این آزمون توصیه می‌شود.' : 'Reinforce your knowledge and earn course XP.')}
                                        </p>

                                        {/* Linked Lesson Badge if any */}
                                        {quiz.lesson && (
                                            <div className="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50/70 border border-indigo-100 text-indigo-700 text-[11px] font-semibold">
                                                <Layers size={12} />
                                                <span>{t('quiz_lesson_badge')}: {quiz.lesson.title}</span>
                                            </div>
                                        )}

                                        {/* 3D Specs Matrix */}
                                        <div className="grid grid-cols-2 gap-2.5 my-5 pt-4 border-t border-slate-100">
                                            <div className="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 border border-slate-100">
                                                <div className="w-7 h-7 rounded-xl bg-blue-100/60 text-blue-600 flex items-center justify-center shrink-0">
                                                    <Clock size={14} />
                                                </div>
                                                <div className="text-start">
                                                    <span className="block text-[10px] text-slate-400 font-semibold">{t('quiz_duration')}</span>
                                                    <span className="text-xs font-bold text-slate-800 font-mono">
                                                        {quiz.duration_minutes} {t('quiz_minutes')}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 border border-slate-100">
                                                <div className="w-7 h-7 rounded-xl bg-emerald-100/60 text-emerald-600 flex items-center justify-center shrink-0">
                                                    <ClipboardCheck size={14} />
                                                </div>
                                                <div className="text-start">
                                                    <span className="block text-[10px] text-slate-400 font-semibold">{t('quiz_questions')}</span>
                                                    <span className="text-xs font-bold text-slate-800 font-mono">
                                                        {quiz.questions_count} {t('quiz_questions_count')}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 border border-slate-100">
                                                <div className="w-7 h-7 rounded-xl bg-amber-100/60 text-amber-600 flex items-center justify-center shrink-0">
                                                    <Zap size={14} />
                                                </div>
                                                <div className="text-start">
                                                    <span className="block text-[10px] text-slate-400 font-semibold">{t('quiz_xp_reward')}</span>
                                                    <span className="text-xs font-bold text-amber-600 font-mono">
                                                        +{quiz.xp_reward} XP
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="flex items-center gap-2 p-2.5 rounded-2xl bg-slate-50 border border-slate-100">
                                                <div className="w-7 h-7 rounded-xl bg-purple-100/60 text-purple-600 flex items-center justify-center shrink-0">
                                                    <RotateCcw size={14} />
                                                </div>
                                                <div className="text-start">
                                                    <span className="block text-[10px] text-slate-400 font-semibold">{t('quiz_attempts_left')}</span>
                                                    <span className="text-xs font-bold text-slate-800 font-mono">
                                                        {uStats.attempts_left} / {quiz.max_attempts}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Score / Performance Bar if attempted */}
                                        {uStats.best_score !== null && (
                                            <div className="mb-5 p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                                <div className="flex items-center justify-between text-xs font-bold">
                                                    <span className="text-slate-600">{t('quiz_best_score')}</span>
                                                    <span className={`font-mono ${isPassed ? 'text-emerald-600' : 'text-slate-800'}`}>
                                                        {uStats.best_score} / {uStats.total_points} ({uStats.score_percentage}%)
                                                    </span>
                                                </div>
                                                <div className="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                                                    <div
                                                        className={`h-full rounded-full transition-all duration-500 ${
                                                            isPassed
                                                                ? 'bg-gradient-to-r from-emerald-500 to-teal-400'
                                                                : 'bg-gradient-to-r from-blue-500 to-indigo-500'
                                                        }`}
                                                        style={{ width: `${Math.min(100, uStats.score_percentage || 0)}%` }}
                                                    />
                                                </div>
                                            </div>
                                        )}
                                    </div>

                                    {/* Action Buttons Row */}
                                    <div className="pt-2 flex items-center gap-2">
                                        {/* Main Action Button */}
                                        {isAvailable && (
                                            <a
                                                href={`/student/quizzes/${quiz.id}/take`}
                                                className="flex-1 h-11 px-4 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-[0.98]"
                                            >
                                                <PlayCircle size={15} />
                                                <span>{t('quiz_start_btn')}</span>
                                            </a>
                                        )}

                                        {isInProgress && (
                                            <a
                                                href={`/student/quizzes/${quiz.id}/take`}
                                                className="flex-1 h-11 px-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-indigo-500/20 hover:shadow-lg transition-all active:scale-[0.98]"
                                            >
                                                <RotateCcw size={14} />
                                                <span>{t('quiz_retake_btn')}</span>
                                            </a>
                                        )}

                                        {isPassed && (
                                            <a
                                                href={`/student/quizzes/${quiz.id}`}
                                                className="flex-1 h-11 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-emerald-500/20 hover:shadow-lg transition-all active:scale-[0.98]"
                                            >
                                                <Trophy size={14} />
                                                <span>{t('quiz_view_details')}</span>
                                            </a>
                                        )}

                                        {isExhausted && (
                                            <a
                                                href={`/student/quizzes/${quiz.id}`}
                                                className="flex-1 h-11 px-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all active:scale-[0.98]"
                                            >
                                                <ClipboardCheck size={14} />
                                                <span>{t('quiz_completed_btn')}</span>
                                            </a>
                                        )}

                                        {/* Details / Preview Button */}
                                        <button
                                            type="button"
                                            onClick={() => setPreviewQuiz(quiz)}
                                            className="h-11 w-11 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center shrink-0 transition-colors"
                                            title={t('quiz_preview_btn')}
                                        >
                                            <HelpCircle size={17} />
                                        </button>
                                    </div>
                                </TiltCard>
                            );
                        })}
                    </div>
                ) : (
                    /* 3D Empty State */
                    <div className="bg-white rounded-3xl p-10 sm:p-16 border border-slate-200/80 shadow-xs text-center max-w-xl mx-auto">
                        <div className="w-20 h-20 rounded-3xl bg-blue-50 text-blue-500 mx-auto flex items-center justify-center mb-5 shadow-inner">
                            <ClipboardCheck size={38} className="animate-pulse" />
                        </div>
                        <h3 className="text-xl font-black text-slate-900 mb-2">
                            {t('quiz_no_quizzes')}
                        </h3>
                        <p className="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-md mx-auto mb-6">
                            {t('quiz_no_quizzes_desc')}
                        </p>
                        <button
                            type="button"
                            onClick={clearFilters}
                            className="px-6 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-md active:scale-95"
                        >
                            {t('quiz_clear_filters')}
                        </button>
                    </div>
                )}

            </div>

            {/* =========================================================
                5. 3D Quick-Guide Modal
               ========================================================= */}
            {previewQuiz && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-in fade-in duration-200"
                    onClick={() => setPreviewQuiz(null)}
                >
                    <div
                        className="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 sm:p-8 space-y-6 relative overflow-hidden"
                        style={{ transform: 'perspective(1000px)' }}
                        onClick={(e) => e.stopPropagation()}
                    >
                        {/* Decorative Top Accent Bar */}
                        <div className="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-blue-600 via-cyan-400 to-emerald-400" />

                        {/* Modal Header */}
                        <div className="flex items-start justify-between gap-4">
                            <div>
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold mb-2">
                                    <ShieldCheck size={13} />
                                    <span>{t('quiz_rules_title')}</span>
                                </span>
                                <h3 className="text-lg sm:text-xl font-black text-slate-900 leading-tight">
                                    {previewQuiz.title}
                                </h3>
                                <p className="text-xs text-slate-500 mt-1 font-medium">
                                    {previewQuiz.course?.title}
                                </p>
                            </div>

                            <button
                                type="button"
                                onClick={() => setPreviewQuiz(null)}
                                className="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center shrink-0 transition-colors"
                            >
                                <X size={16} />
                            </button>
                        </div>

                        {/* Guidelines List */}
                        <div className="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs text-slate-700">
                            <div className="flex items-start gap-2.5">
                                <CheckCircle2 size={16} className="text-emerald-500 shrink-0 mt-0.5" />
                                <span>
                                    {isFa
                                        ? `مدت زمان آزمون ${previewQuiz.duration_minutes} دقیقه می‌باشد؛ پس از پایان زمان، پاسخ‌ها خودکار ثبت خواهند شد.`
                                        : `The test timer is set to ${previewQuiz.duration_minutes} minutes. Answers will submit automatically when time expires.`}
                                </span>
                            </div>

                            <div className="flex items-start gap-2.5">
                                <CheckCircle2 size={16} className="text-emerald-500 shrink-0 mt-0.5" />
                                <span>
                                    {isFa
                                        ? `شما حداکثر ${previewQuiz.max_attempts} بار فرصت شرکت در این آزمون را دارید (تلاش‌های باقیمانده: ${previewQuiz.user_stats.attempts_left}).`
                                        : `You have a maximum of ${previewQuiz.max_attempts} attempts (${previewQuiz.user_stats.attempts_left} attempts remaining).`}
                                </span>
                            </div>

                            <div className="flex items-start gap-2.5">
                                <Zap size={16} className="text-amber-500 shrink-0 mt-0.5" />
                                <span>
                                    {isFa
                                        ? `پاسخ صحیح به سوالات باعث کسب تا +${previewQuiz.xp_reward} نمره پاداش در سیستم گیمیفیکیشن خواهد شد.`
                                        : `Passing correctly grants up to +${previewQuiz.xp_reward} bonus XP in the gamification system.`}
                                </span>
                            </div>

                            <div className="flex items-start gap-2.5">
                                <AlertCircle size={16} className="text-sky-500 shrink-0 mt-0.5" />
                                <span>{t('quiz_rules_notice')}</span>
                            </div>
                        </div>

                        {/* Modal Action Buttons */}
                        <div className="flex items-center gap-3 pt-2">
                            {previewQuiz.user_stats.attempts_left > 0 ? (
                                <a
                                    href={`/student/quizzes/${previewQuiz.id}/take`}
                                    className="flex-1 h-11 px-5 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-blue-500/25 transition-all"
                                >
                                    <PlayCircle size={16} />
                                    <span>{t('quiz_start_btn')}</span>
                                </a>
                            ) : (
                                <a
                                    href={`/student/quizzes/${previewQuiz.id}`}
                                    className="flex-1 h-11 px-5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition-all"
                                >
                                    <ClipboardCheck size={16} />
                                    <span>{t('quiz_view_details')}</span>
                                </a>
                            )}

                            <a
                                href={`/student/quizzes/${previewQuiz.id}`}
                                className="h-11 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors"
                            >
                                <ExternalLink size={14} />
                                <span>{t('quiz_view_details')}</span>
                            </a>
                        </div>
                    </div>
                </div>
            )}

        </StudentLayout>
    );
}
