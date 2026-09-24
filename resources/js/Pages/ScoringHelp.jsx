import React, { useState, useEffect, useRef, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import {
    Trophy,
    Sparkles,
    Zap,
    Award,
    Clock,
    BookOpen,
    CheckCircle2,
    XCircle,
    AlertCircle,
    Calculator,
    Crown,
    Flame,
    ShieldCheck,
    ArrowRight,
    ArrowLeft,
    TrendingUp,
    Compass,
    Star,
    Layers,
    Sliders
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
        let height = (canvas.height = canvas.parentElement?.offsetHeight || 420);

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
            targetRotY = ((x - width / 2) / width) * 0.4;
            targetRotX = -((y - height / 2) / height) * 0.4;
        };

        window.addEventListener('resize', handleResize);
        const parent = canvas.parentElement;
        if (parent) {
            parent.addEventListener('mousemove', handleMouseMove);
        }

        const numParticles = 48;
        const particles = [];
        const radius = Math.min(width, height) * 0.6;

        for (let i = 0; i < numParticles; i++) {
            const theta = Math.random() * 2 * Math.PI;
            const phi = Math.acos(Math.random() * 2 - 1);
            const r = radius * (0.3 + Math.random() * 0.7);

            particles.push({
                x: r * Math.sin(phi) * Math.cos(theta),
                y: r * Math.sin(phi) * Math.sin(theta),
                z: r * Math.cos(phi),
                vx: (Math.random() - 0.5) * 0.3,
                vy: (Math.random() - 0.5) * 0.3,
                vz: (Math.random() - 0.5) * 0.3,
                size: Math.random() * 2.2 + 1,
                color: i % 3 === 0 ? '#38bdf8' : i % 3 === 1 ? '#818cf8' : '#34d399',
            });
        }

        // Floating 3D Octahedral Crystal
        let baseAngle = 0;

        const render = () => {
            baseAngle += 0.003;
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

            // Connection Lines
            ctx.lineWidth = 0.5;
            for (let i = 0; i < projected.length; i++) {
                for (let j = i + 1; j < projected.length; j++) {
                    const dx = projected[i].x - projected[j].x;
                    const dy = projected[i].y - projected[j].y;
                    const dist = Math.hypot(dx, dy);

                    if (dist < 95) {
                        const alpha = (1 - dist / 95) * 0.25;
                        ctx.strokeStyle = `rgba(56, 189, 248, ${alpha})`;
                        ctx.beginPath();
                        ctx.moveTo(projected[i].x, projected[i].y);
                        ctx.lineTo(projected[j].x, projected[j].y);
                        ctx.stroke();
                    }
                }
            }

            // Draw glowing points
            for (let i = 0; i < projected.length; i++) {
                const pt = projected[i];
                ctx.beginPath();
                ctx.arc(pt.x, pt.y, Math.max(0.8, pt.size), 0, Math.PI * 2);
                ctx.fillStyle = pt.color;
                ctx.globalAlpha = Math.min(1, pt.scale * 0.7);
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
const TiltCard = ({ children, className = '', glowColor = 'rgba(31, 143, 255, 0.25)' }) => {
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

        const rotateX = -((y - centerY) / centerY) * 7;
        const rotateY = ((x - centerX) / centerX) * 7;

        setStyle({
            transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.012, 1.012, 1.012)`,
            transition: 'transform 0.08s ease-out',
        });

        setGlare({
            x: (x / rect.width) * 100,
            y: (y / rect.height) * 100,
            opacity: 0.15,
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
            {/* Dynamic specular glare */}
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
   3. Main ScoringHelp Page
   ========================================================================= */
export default function ScoringHelp({ rules = [], auth = {} }) {
    const { t, isRtl } = useLanguage();
    const isFa = isRtl;

    // Interactive XP Simulator State
    const [simOnTimeDays, setSimOnTimeDays] = useState(14);
    const [simAssignments, setSimAssignments] = useState(5);
    const [simParticipation, setSimParticipation] = useState(8);
    const [simAbsences, setSimAbsences] = useState(0);
    const [simLateDays, setSimLateDays] = useState(1);
    const [milestoneUnlocked, setMilestoneUnlocked] = useState(false);

    // Calculate XP
    const calculatedXP = useMemo(() => {
        const total =
            simOnTimeDays * 10 +
            simAssignments * 20 +
            simParticipation * 5 -
            simAbsences * 15 -
            simLateDays * 5;
        return Math.max(0, total);
    }, [simOnTimeDays, simAssignments, simParticipation, simAbsences, simLateDays]);

    const simLevel = useMemo(() => {
        return Math.floor(calculatedXP / 100) + 1;
    }, [calculatedXP]);

    const simTierTitle = useMemo(() => {
        if (simLevel >= 10) return isFa ? 'استاد‌یار و اسطوره ادوُرا 👑' : 'Legendary Master 👑';
        if (simLevel >= 7) return isFa ? 'پیشتاز و نخبه اکادمیک 💎' : 'Elite Champion 💎';
        if (simLevel >= 4) return isFa ? 'شاگرد ماهر و کوشا ⚡' : 'Skilled Scholar ⚡';
        return isFa ? 'شاگرد تازه‌کار و باانگیزه 🌟' : 'Rookie Scholar 🌟';
    }, [simLevel, isFa]);

    // Split positive and negative rules
    const positiveRules = useMemo(() => {
        return rules.filter((r) => r.default_score > 0);
    }, [rules]);

    const negativeRules = useMemo(() => {
        return rules.filter((r) => r.default_score < 0);
    }, [rules]);

    // Persian translations dictionary for standard rule names
    const ruleTranslationsFa = {
        attendance_on_time: {
            title: 'حضور به‌موقع در صنف',
            desc: 'پاداش حضور دقیق و بموقع قبل از آغاز درس آنلاین استاد.',
        },
        attendance_late: {
            title: 'تاخیر در ورود به صنف',
            desc: 'کسر نمره در صورت ورود با تاخیر به صنف درسی.',
        },
        attendance_absent: {
            title: 'غیرحاضری غیرموجه',
            desc: 'کسر نمره در صورت عدم حضور در صنف بدون هماهنگی قبلی.',
        },
        assignment_completed: {
            title: 'تکمیل و ارسال کار خانگی',
            desc: 'پاداش تحویل بموقع تکالیف و پروژه‌های درسی محوله.',
        },
        assignment_not_completed: {
            title: 'عدم انجام کار خانگی',
            desc: 'کسر نمره در صورت تاخیر یا عدم ارسال کار خانگی.',
        },
        participation: {
            title: 'سهم‌گیری فعال در درس',
            desc: 'امتیاز ویژه برای پاسخ به سوالات استاد و مشارکت علمی.',
        },
        manual: {
            title: 'امتیاز تشویقی استاد',
            desc: 'تنظیم نمره دستی با صلاحدید استاد به پاس کوشش ویژه شاگرد.',
        },
    };

    const user = auth?.user;

    const triggerMilestone = () => {
        setMilestoneUnlocked(true);
        setTimeout(() => setMilestoneUnlocked(false), 1500);
    };

    return (
        <StudentLayout title={`${isFa ? 'نحوه محاسبه نمرات' : 'How Scoring Works'} - ${t('brand_title')}`}>
            <Head>
                <title>{`${isFa ? 'نحوه محاسبه نمرات' : 'How Scoring Works'} - ${t('brand_title')}`}</title>
                <meta
                    name="description"
                    content="با سیستم گیمیفیکیشن و نمره‌دهی ادوُرا آشنا شوید؛ با حضور به‌موقع و انجام تکالیف امتیاز بگیرید و به صدر جدول پیشتازان صعود کنید."
                />
            </Head>

            <div className="space-y-8 max-w-7xl mx-auto pb-12">
                {/* 1. Hero Banner Matching Student Dashboard (Deep Blue 3D Ambient) */}
                <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#061E3E] via-slate-900 to-[#1F8FFF] text-white p-6 sm:p-10 lg:p-12 border border-blue-900/50">
                    <Hero3DCanvas />

                    <div className="relative z-10 space-y-4 max-w-3xl">
                        <span className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-cyan-300 backdrop-blur-md shadow-sm">
                            <Sparkles size={14} className="text-cyan-400 animate-spin-slow" />
                            <span>{isFa ? 'سیستم گیمیفیکیشن و پیشتازی شاگردان' : 'Gamification & Scoring System'}</span>
                        </span>

                        <h1 className="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                            {isFa ? 'نحوه محاسبه نمرات و ارتقای درجات علمی' : 'How Scoring & Student Tiers Work'}
                        </h1>

                        <p className="text-sm sm:text-base text-blue-100 leading-relaxed font-normal">
                            {isFa
                                ? 'در ادوُرا تِک هر حضور، هر کار خانگی و هر پاسخ علمی شما ارزش دارد! نمره کسب کنید، درجه تحصیلی خود را ارتقا دهید و افتخار حضور در جمع برترین‌های صنف را به دست آورید.'
                                : 'Every effort matters! Earn points for punctual attendance, homework completion, and active class participation to climb the official leaderboard.'}
                        </p>

                        {/* If user logged in: quick standing chip */}
                        {user && (
                            <div className="pt-2 flex flex-wrap items-center gap-3">
                                <div className="inline-flex items-center gap-3 px-4 py-2 rounded-2xl bg-slate-950/60 border border-cyan-400/30 backdrop-blur-md">
                                    <div className="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-400 to-orange-500 flex items-center justify-center text-slate-950 font-black text-xs shadow">
                                        Lvl {user.level}
                                    </div>
                                    <div className="text-xs">
                                        <span className="text-cyan-300 font-bold block">{user.name}</span>
                                        <span className="text-slate-300">
                                            {isFa ? `${user.total_score} نمره کل · ${user.level_title}` : `${user.total_score} XP · ${user.level_title}`}
                                        </span>
                                    </div>
                                </div>
                                <Link
                                    href="/leaderboard"
                                    className="px-4 py-2 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 transition-all shadow-lg shadow-cyan-500/25"
                                >
                                    <Trophy size={14} />
                                    <span>{isFa ? 'مشاهده جایگاه در لیدربرد' : 'View Leaderboard'}</span>
                                </Link>
                            </div>
                        )}
                    </div>
                </div>

                {/* 2. Three Pillar Overview Cards (White Glassmorphic Theme) */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {/* Card 1: Attendance */}
                    <TiltCard className="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-blue-400/40" glowColor="rgba(31, 143, 255, 0.2)">
                        <div className="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <Clock size={24} />
                        </div>
                        <h3 className="text-lg font-bold text-slate-900 mb-2">
                            {isFa ? '۱. نظم و حاضری صنف' : '1. Attendance & Punctuality'}
                        </h3>
                        <p className="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {isFa
                                ? 'حاضری به‌موقع روزانه ۱۰ نمره دارد. مداومت در حضور بدون تاخیر، سریع‌ترین راه برای تثبیت رتبه اول صنف است.'
                                : 'Daily on-time attendance awards +10 XP. Consistency is your fastest route to top rank.'}
                        </p>
                        <div className="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-blue-600">
                            <span>{isFa ? 'تاثیر نمره: بسیار زیاد' : 'Impact: Very High'}</span>
                            <span className="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold font-mono">+۱۰ نمره</span>
                        </div>
                    </TiltCard>

                    {/* Card 2: Homework */}
                    <TiltCard className="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-purple-400/40" glowColor="rgba(139, 92, 246, 0.2)">
                        <div className="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                            <BookOpen size={24} />
                        </div>
                        <h3 className="text-lg font-bold text-slate-900 mb-2">
                            {isFa ? '۲. حل تکالیف و کار خانگی' : '2. Homework & Projects'}
                        </h3>
                        <p className="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {isFa
                                ? 'ارسال بموقع هر تکلیف ۲۰ نمره مستقیم می‌آورد. پروژه‌های عملی مهارت فنی شما را تقویت و رتبه‌تان را جهش می‌دهند.'
                                : 'Submitting homework assignments on time grants +20 XP. Real-world tasks give you the biggest XP leaps.'}
                        </p>
                        <div className="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-purple-600">
                            <span>{isFa ? 'تاثیر نمره: اساسی' : 'Impact: Fundamental'}</span>
                            <span className="px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-bold font-mono">+۲۰ نمره</span>
                        </div>
                    </TiltCard>

                    {/* Card 3: Participation */}
                    <TiltCard className="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm hover:shadow-xl hover:border-emerald-400/40" glowColor="rgba(16, 185, 129, 0.2)">
                        <div className="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <Zap size={24} />
                        </div>
                        <h3 className="text-lg font-bold text-slate-900 mb-2">
                            {isFa ? '۳. سهم‌گیری و امتحانات' : '3. Class Quizzes & Discussions'}
                        </h3>
                        <p className="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {isFa
                                ? 'پاسخ به پرسش‌های استاد، مشارکت در چت صنف و نمرات قبولی در کویزها، امتیازات تشویقی اختصاصی به همراه دارند.'
                                : 'Speaking up in discussions and passing weekly quizzes earns extra bonus points from instructors.'}
                        </p>
                        <div className="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-emerald-600">
                            <span>{isFa ? 'بونوس اختصاصی' : 'Bonus Rewards'}</span>
                            <span className="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold font-mono">+۵ الی +۱۵</span>
                        </div>
                    </TiltCard>
                </div>

                {/* 3. Interactive 3D XP Simulator & Calculator */}
                <TiltCard className="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200/80 shadow-md relative overflow-hidden" glowColor="rgba(31, 143, 255, 0.15)">
                    <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-100">
                        <div>
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-2">
                                <Calculator size={13} />
                                <span>{isFa ? 'ماشین‌حساب هوشمند نمرات' : 'Interactive XP Simulator'}</span>
                            </span>
                            <h2 className="text-xl sm:text-2xl font-black text-slate-900">
                                {isFa ? 'محاسبه تخمینی نمره و پیش‌بینی رتبه شما' : 'Simulate Your Score & Predict Your Tier'}
                            </h2>
                            <p className="text-xs sm:text-sm text-slate-500 mt-1">
                                {isFa
                                    ? 'اسلایدرهای زیر را تغییر دهید تا ببینید با چند جلسه حضور و کار خانگی به کدام درجه علمی و نمره می‌رسید!'
                                    : 'Adjust the sliders below to see how attendance and homework translate into your final XP.'}
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={triggerMilestone}
                            className="px-4 py-2 rounded-2xl bg-slate-900 hover:bg-slate-800 text-cyan-300 text-xs font-bold flex items-center gap-2 transition-all shadow-md active:scale-95"
                        >
                            <Sparkles size={14} className="text-cyan-400" />
                            <span>{isFa ? 'آزاد کردن مدال شبیه‌سازی' : 'Trigger Milestone'}</span>
                        </button>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        {/* Sliders Form (7 cols) */}
                        <div className="lg:col-span-7 space-y-6">
                            {/* Slider 1: On-time attendance */}
                            <div className="space-y-2">
                                <div className="flex justify-between items-center text-xs sm:text-sm font-semibold">
                                    <span className="flex items-center gap-2 text-slate-700">
                                        <Clock size={16} className="text-emerald-500" />
                                        {isFa ? 'روزهای حضور به‌موقع در صنف (+۱۰ نمره)' : 'On-time Attendance (+10 XP)'}
                                    </span>
                                    <span className="px-2.5 py-0.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold font-mono">
                                        {simOnTimeDays} {isFa ? 'روز' : 'days'} ({simOnTimeDays * 10}+)
                                    </span>
                                </div>
                                <input
                                    type="range"
                                    min="0"
                                    max="30"
                                    value={simOnTimeDays}
                                    onChange={(e) => setSimOnTimeDays(parseInt(e.target.value) || 0)}
                                    className="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-emerald-500"
                                />
                            </div>

                            {/* Slider 2: Assignments completed */}
                            <div className="space-y-2">
                                <div className="flex justify-between items-center text-xs sm:text-sm font-semibold">
                                    <span className="flex items-center gap-2 text-slate-700">
                                        <BookOpen size={16} className="text-blue-500" />
                                        {isFa ? 'تکالیف و کارهای خانگی ارسالی (+۲۰ نمره)' : 'Completed Homework (+20 XP)'}
                                    </span>
                                    <span className="px-2.5 py-0.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold font-mono">
                                        {simAssignments} {isFa ? 'تکلیف' : 'tasks'} ({simAssignments * 20}+)
                                    </span>
                                </div>
                                <input
                                    type="range"
                                    min="0"
                                    max="15"
                                    value={simAssignments}
                                    onChange={(e) => setSimAssignments(parseInt(e.target.value) || 0)}
                                    className="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-500"
                                />
                            </div>

                            {/* Slider 3: Participation */}
                            <div className="space-y-2">
                                <div className="flex justify-between items-center text-xs sm:text-sm font-semibold">
                                    <span className="flex items-center gap-2 text-slate-700">
                                        <Zap size={16} className="text-purple-500" />
                                        {isFa ? 'سهم‌گیری در مباحث و پاسخ به سوالات (+۵ نمره)' : 'Active Class Participation (+5 XP)'}
                                    </span>
                                    <span className="px-2.5 py-0.5 rounded-lg bg-purple-50 border border-purple-200 text-purple-700 text-xs font-bold font-mono">
                                        {simParticipation} {isFa ? 'بار' : 'times'} ({simParticipation * 5}+)
                                    </span>
                                </div>
                                <input
                                    type="range"
                                    min="0"
                                    max="25"
                                    value={simParticipation}
                                    onChange={(e) => setSimParticipation(parseInt(e.target.value) || 0)}
                                    className="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-purple-500"
                                />
                            </div>

                            {/* Deduction Sliders */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                <div className="space-y-2 bg-rose-50/70 p-3.5 rounded-2xl border border-rose-200/60">
                                    <div className="flex justify-between items-center text-xs font-semibold">
                                        <span className="text-rose-700">{isFa ? 'غیرحاضری (-۱۵ نمره)' : 'Absences (-15 XP)'}</span>
                                        <span className="text-rose-700 font-bold">{simAbsences}</span>
                                    </div>
                                    <input
                                        type="range"
                                        min="0"
                                        max="10"
                                        value={simAbsences}
                                        onChange={(e) => setSimAbsences(parseInt(e.target.value) || 0)}
                                        className="w-full h-1.5 bg-rose-100 rounded-lg appearance-none cursor-pointer accent-rose-500"
                                    />
                                </div>

                                <div className="space-y-2 bg-amber-50/70 p-3.5 rounded-2xl border border-amber-200/60">
                                    <div className="flex justify-between items-center text-xs font-semibold">
                                        <span className="text-amber-800">{isFa ? 'تاخیر در ورود (-۵ نمره)' : 'Late Arrivals (-5 XP)'}</span>
                                        <span className="text-amber-800 font-bold">{simLateDays}</span>
                                    </div>
                                    <input
                                        type="range"
                                        min="0"
                                        max="10"
                                        value={simLateDays}
                                        onChange={(e) => setSimLateDays(parseInt(e.target.value) || 0)}
                                        className="w-full h-1.5 bg-amber-100 rounded-lg appearance-none cursor-pointer accent-amber-500"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Holographic Output Display (5 cols) */}
                        <div className="lg:col-span-5">
                            <div className="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-slate-900 via-[#061E3E] to-slate-900 text-white text-center shadow-xl border border-blue-900/60 relative overflow-hidden">
                                <div className="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-400 to-blue-500 p-[2px] mx-auto mb-3 shadow-lg">
                                    <div className="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                                        <Trophy size={26} className="text-cyan-400" />
                                    </div>
                                </div>

                                <span className="text-xs uppercase tracking-wider text-slate-400 font-bold block mb-1">
                                    {isFa ? 'نمره کل تخمینی' : 'Projected Total Score'}
                                </span>

                                <div className="text-4xl sm:text-5xl font-black text-cyan-300 my-2 font-mono">
                                    {calculatedXP}
                                    <span className="text-base font-bold text-cyan-400 ml-1">XP</span>
                                </div>

                                <div className="inline-block mt-2 px-3.5 py-1 rounded-full bg-cyan-500/20 border border-cyan-400/40 text-cyan-300 text-xs font-bold">
                                    {simTierTitle}
                                </div>

                                <div className="mt-4 pt-4 border-t border-white/10 flex items-center justify-around text-xs text-slate-300">
                                    <div>
                                        <span className="block text-slate-400">{isFa ? 'سطح معادل' : 'Tier Level'}</span>
                                        <span className="font-bold text-white text-sm">سطح {simLevel}</span>
                                    </div>
                                    <div className="w-px h-8 bg-white/10" />
                                    <div>
                                        <span className="block text-slate-400">{isFa ? 'رتبه تخمینی' : 'Est. Standing'}</span>
                                        <span className="font-bold text-emerald-400 text-sm">{isFa ? 'جزو برترین‌ها' : 'Top 10%'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </TiltCard>

                {/* 4. Complete Scoring Rules Breakdown (Side by Side) */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Positive Rules */}
                    <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-5">
                        <div className="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                            <div className="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <Sparkles size={20} />
                            </div>
                            <div>
                                <h3 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <span>{isFa ? 'روش‌های کسب نمره مثبت' : 'Ways to Earn Points'}</span>
                                    <span className="text-[11px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">
                                        {positiveRules.length} {isFa ? 'مورد' : 'rules'}
                                    </span>
                                </h3>
                                <p className="text-xs text-slate-500">{isFa ? 'پاداش فعالیت‌های مثبت و پیوسته' : 'Rewards for consistency'}</p>
                            </div>
                        </div>

                        <div className="space-y-3">
                            {positiveRules.map((rule) => {
                                const tr = ruleTranslationsFa[rule.action_name];
                                const title = isFa ? (tr?.title || rule.label) : rule.label;
                                const desc = isFa ? (tr?.desc || rule.description) : rule.description;

                                return (
                                    <div
                                        key={rule.id || rule.action_name}
                                        className="p-4 rounded-2xl bg-slate-50 hover:bg-emerald-50/40 border border-slate-200/60 hover:border-emerald-300 transition-all flex items-start justify-between gap-4"
                                    >
                                        <div className="flex items-start gap-3">
                                            <div className="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                                <CheckCircle2 size={16} />
                                            </div>
                                            <div>
                                                <div className="text-sm font-bold text-slate-800">{title}</div>
                                                {desc && <div className="text-xs text-slate-500 mt-1 leading-relaxed">{desc}</div>}
                                            </div>
                                        </div>

                                        <span className="shrink-0 px-3 py-1 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-black text-xs font-mono dir-ltr">
                                            +{rule.default_score} XP
                                        </span>
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Negative Deductions */}
                    <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-5">
                        <div className="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                            <div className="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                                <XCircle size={20} />
                            </div>
                            <div>
                                <h3 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <span>{isFa ? 'موارد کسر امتیاز و جریمه' : 'Things That Deduct Points'}</span>
                                    <span className="text-[11px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                        {negativeRules.length} {isFa ? 'مورد' : 'rules'}
                                    </span>
                                </h3>
                                <p className="text-xs text-slate-500">{isFa ? 'اجتناب از این موارد رتبه شما را حفظ می‌کند' : 'Avoid deductions'}</p>
                            </div>
                        </div>

                        <div className="space-y-3">
                            {negativeRules.map((rule) => {
                                const tr = ruleTranslationsFa[rule.action_name];
                                const title = isFa ? (tr?.title || rule.label) : rule.label;
                                const desc = isFa ? (tr?.desc || rule.description) : rule.description;

                                return (
                                    <div
                                        key={rule.id || rule.action_name}
                                        className="p-4 rounded-2xl bg-slate-50 hover:bg-rose-50/40 border border-slate-200/60 hover:border-rose-300 transition-all flex items-start justify-between gap-4"
                                    >
                                        <div className="flex items-start gap-3">
                                            <div className="w-7 h-7 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                                                <AlertCircle size={16} />
                                            </div>
                                            <div>
                                                <div className="text-sm font-bold text-slate-800">{title}</div>
                                                {desc && <div className="text-xs text-slate-500 mt-1 leading-relaxed">{desc}</div>}
                                            </div>
                                        </div>

                                        <span className="shrink-0 px-3 py-1 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 font-black text-xs font-mono dir-ltr">
                                            {rule.default_score} XP
                                        </span>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>

                {/* 5. Student Tier Progression Ladder (4 Stages) */}
                <div className="space-y-5">
                    <div className="text-center md:text-start">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold mb-1">
                            <Crown size={13} />
                            <span>{isFa ? 'مسیر پیشرفت و ارتقا' : 'Tier Ladder'}</span>
                        </span>
                        <h2 className="text-xl sm:text-2xl font-black text-slate-900">
                            {isFa ? 'درجات علمی و افتخارات شاگردان ادوُرا' : 'Student Tiers & Achievements'}
                        </h2>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        {/* Tier 1: Rookie */}
                        <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                            <div className="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center mb-4">
                                <Award size={24} />
                            </div>
                            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                {isFa ? 'سطح ۱ تا ۳ (۰ تا ۳۹۹ XP)' : 'Level 1 - 3 (0-399 XP)'}
                            </span>
                            <h3 className="text-base font-bold text-slate-900 mb-2">
                                {isFa ? 'شاگرد تازه‌کار (Rookie)' : 'Rookie Scholar'}
                            </h3>
                            <p className="text-xs text-slate-500 leading-relaxed">
                                {isFa
                                    ? 'آغاز مسیر آموزش در ادوُرا. دسترسی کامل به دروس و چت‌های عمومی صنف.'
                                    : 'Foundation tier. Access to all enrolled courses and student chats.'}
                            </p>
                        </div>

                        {/* Tier 2: Skilled */}
                        <div className="bg-white rounded-3xl p-6 border border-blue-200/80 shadow-sm hover:shadow-md transition-shadow">
                            <div className="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                                <Zap size={24} />
                            </div>
                            <span className="text-[11px] font-bold text-blue-600 uppercase tracking-wider block mb-1">
                                {isFa ? 'سطح ۴ تا ۶ (۴۰۰ تا ۶۹۹ XP)' : 'Level 4 - 6 (400-699 XP)'}
                            </span>
                            <h3 className="text-base font-bold text-slate-900 mb-2">
                                {isFa ? 'شاگرد کوشا و ماهر' : 'Skilled Scholar'}
                            </h3>
                            <p className="text-xs text-slate-500 leading-relaxed">
                                {isFa
                                    ? 'نشان براق آبی در پروفایل، اولویت در بررسی تکالیف و امکان پاسخ به اشکالات هم‌صنفی‌ها.'
                                    : 'Blue badge on profile and priority assignment review.'}
                            </p>
                        </div>

                        {/* Tier 3: Elite */}
                        <div className="bg-white rounded-3xl p-6 border border-purple-200/80 shadow-sm hover:shadow-md transition-shadow">
                            <div className="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                                <Flame size={24} />
                            </div>
                            <span className="text-[11px] font-bold text-purple-600 uppercase tracking-wider block mb-1">
                                {isFa ? 'سطح ۷ تا ۹ (۷۰۰ تا ۹۹۹ XP)' : 'Level 7 - 9 (700-999 XP)'}
                            </span>
                            <h3 className="text-base font-bold text-slate-900 mb-2">
                                {isFa ? 'شاگرد نخبه و پیشتاز' : 'Elite Champion'}
                            </h3>
                            <p className="text-xs text-slate-500 leading-relaxed">
                                {isFa
                                    ? 'قرارگیری در صفحه اول لیدربرد ادوُرا، تصدیق‌نامه ممتاز با امضای افتخاری و دعوت به وبینارهای VIP.'
                                    : 'Featured on the leaderboard and verified honors certificate.'}
                            </p>
                        </div>

                        {/* Tier 4: Master */}
                        <div className="bg-white rounded-3xl p-6 border border-amber-200/80 shadow-sm hover:shadow-md transition-shadow">
                            <div className="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                                <Crown size={24} />
                            </div>
                            <span className="text-[11px] font-bold text-amber-600 uppercase tracking-wider block mb-1">
                                {isFa ? 'سطح ۱۰+ (۱۰۰۰+ XP)' : 'Level 10+ (1000+ XP)'}
                            </span>
                            <h3 className="text-base font-bold text-slate-900 mb-2">
                                {isFa ? 'استاد‌یار و اسطوره ادوُرا' : 'Legendary Master'}
                            </h3>
                            <p className="text-xs text-slate-500 leading-relaxed">
                                {isFa
                                    ? 'بالاترین افتخار تحصیلی! امکان تدریس‌یاری (TA)، بورسیه کامل دوره‌های بین‌المللی و جایزه ویژه ادوُرا.'
                                    : 'Highest honor! Teaching assistant eligibility and full international sponsorships.'}
                            </p>
                        </div>
                    </div>
                </div>

                {/* 6. Pro Tips (4 Golden Rules) */}
                <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
                    <div className="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div className="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <ShieldCheck size={20} />
                        </div>
                        <h3 className="text-lg font-bold text-slate-900">
                            {isFa ? '۴ قانون طلایی برای رسیدن به صدر جدول پیشتازان' : '4 Golden Rules to Reach Rank #1'}
                        </h3>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm">
                        <div className="flex items-start gap-3">
                            <div className="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                ۱
                            </div>
                            <p className="text-slate-600 leading-relaxed">
                                <strong className="text-slate-900 block mb-0.5">{isFa ? 'حضور سر وقت در صنف آنلاین:' : 'Punctual Attendance:'}</strong>
                                {isFa
                                    ? 'هر جلسه آنلاین دارای ثبت اتوماتیک حاضری است؛ تلاش کنید ۵ دقیقه قبل از شروع صنف وارد لینک شوید.'
                                    : 'Live sessions record attendance automatically. Join early to secure your full points.'}
                            </p>
                        </div>

                        <div className="flex items-start gap-3">
                            <div className="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                ۲
                            </div>
                            <p className="text-slate-600 leading-relaxed">
                                <strong className="text-slate-900 block mb-0.5">{isFa ? 'ارسال سریع تکالیف:' : 'Submit Homework Early:'}</strong>
                                {isFa
                                    ? 'تکالیفی که در ۲۴ ساعت اول پس از صنف ارسال شوند، شانس نمره تشویقی از سوی استاد را خواهند داشت.'
                                    : 'Assignments submitted early often receive faster reviews and bonus points from teachers.'}
                            </p>
                        </div>

                        <div className="flex items-start gap-3">
                            <div className="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                ۳
                            </div>
                            <p className="text-slate-600 leading-relaxed">
                                <strong className="text-slate-900 block mb-0.5">{isFa ? 'سهم‌گیری در بخش گفتگو:' : 'Voice Your Ideas in Chat:'}</strong>
                                {isFa
                                    ? 'پرسیدن سوال‌های مفید و همکاری در رفع اشکال هم‌صنفی‌ها در بخش چت کورس، امتیاز تشویقی دارد.'
                                    : 'Helping peers and engaging in course chats is regularly rewarded.'}
                            </p>
                        </div>

                        <div className="flex items-start gap-3">
                            <div className="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                ۴
                            </div>
                            <p className="text-slate-600 leading-relaxed">
                                <strong className="text-slate-900 block mb-0.5">{isFa ? 'بررسی مرتب آگاهی‌ها:' : 'Check Notifications:'}</strong>
                                {isFa
                                    ? 'هر امتیاز کسب‌شده یا کسر‌شده بلافاصله در بخش اعلانات با دلیل دقیق ثبت می‌شود تا از شفافیت کامل مطمئن شوید.'
                                    : 'Points earned or deducted trigger instant alerts for complete transparency.'}
                            </p>
                        </div>
                    </div>
                </div>

                {/* 7. Action CTAs */}
                <div className="flex flex-wrap items-center justify-center gap-4 pt-4">
                    <Link
                        href="/leaderboard"
                        className="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-105 active:scale-95 transition-all flex items-center gap-2"
                    >
                        <Trophy size={18} className="text-amber-300" />
                        <span>{isFa ? 'مشاهده زنده جدول پیشتازان' : 'View Leaderboard'}</span>
                        {isFa ? <ArrowLeft size={16} /> : <ArrowRight size={16} />}
                    </Link>

                    <Link
                        href="/student/dashboard"
                        className="px-8 py-3.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 border border-slate-200/80 font-bold text-sm shadow-sm hover:border-slate-300 hover:scale-105 active:scale-95 transition-all flex items-center gap-2"
                    >
                        <Compass size={18} className="text-blue-600" />
                        <span>{isFa ? 'بازگشت به داشبورد من' : 'My Dashboard'}</span>
                    </Link>
                </div>
            </div>
        </StudentLayout>
    );
}
