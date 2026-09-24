import React, { useState, useEffect, useRef, useMemo } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import {
    BookOpen,
    CheckCircle2,
    Clock,
    FileText,
    Video,
    ClipboardCheck,
    Star,
    MessageSquare,
    ChevronDown,
    ChevronUp,
    Download,
    Radio,
    Calendar,
    User,
    Tag,
    BarChart2,
    Sparkles,
    Check,
    Layers,
    Share2,
    ThumbsUp,
    Send,
    PlayCircle,
    RotateCcw,
    ExternalLink,
    ChevronRight,
    ChevronLeft,
    AlertCircle,
    Info,
    FileCode,
    FileSpreadsheet,
    FileImage,
    File
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
        let height = (canvas.height = canvas.parentElement?.offsetHeight || 340);

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

        const numParticles = 40;
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
                size: Math.random() * 2 + 1,
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

            // Lines
            ctx.lineWidth = 0.5;
            for (let i = 0; i < projected.length; i++) {
                for (let j = i + 1; j < projected.length; j++) {
                    const dx = projected[i].x - projected[j].x;
                    const dy = projected[i].y - projected[j].y;
                    const dist = Math.hypot(dx, dy);

                    if (dist < 85) {
                        const alpha = (1 - dist / 85) * 0.2;
                        ctx.strokeStyle = `rgba(56, 189, 248, ${alpha})`;
                        ctx.beginPath();
                        ctx.moveTo(projected[i].x, projected[i].y);
                        ctx.lineTo(projected[j].x, projected[j].y);
                        ctx.stroke();
                    }
                }
            }

            // Glow points
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
            className="absolute inset-0 pointer-events-none z-0 opacity-75"
        />
    );
};

/* =========================================================================
   2. 3D Tilt Card Component (Micro-Physics & Specular Glare)
   ========================================================================= */
const TiltCard = ({ children, className = '', glowColor = 'rgba(31, 143, 255, 0.2)' }) => {
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

        const rotateX = -((y - centerY) / centerY) * 5;
        const rotateY = ((x - centerX) / centerX) * 5;

        setStyle({
            transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.01, 1.01, 1.01)`,
            transition: 'transform 0.08s ease-out',
        });

        setGlare({
            x: (x / rect.width) * 100,
            y: (y / rect.height) * 100,
            opacity: 0.12,
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

/* Helper for File Icon */
const getFileIcon = (fileName = '') => {
    const ext = fileName.split('.').pop()?.toLowerCase();
    if (ext === 'pdf') return <FileText size={20} className="text-rose-500" />;
    if (['doc', 'docx'].includes(ext)) return <FileText size={20} className="text-blue-500" />;
    if (['xls', 'xlsx', 'csv'].includes(ext)) return <FileSpreadsheet size={20} className="text-emerald-500" />;
    if (['png', 'jpg', 'jpeg', 'webp', 'svg'].includes(ext)) return <FileImage size={20} className="text-purple-500" />;
    if (['zip', 'rar', 'tar', 'gz'].includes(ext)) return <FileCode size={20} className="text-amber-500" />;
    return <File size={20} className="text-slate-500" />;
};

/* =========================================================================
   3. Main CourseLearning Page Component
   ========================================================================= */
export default function CourseLearning({
    course = {},
    lessons = [],
    documents = [],
    classNotes = [],
    sessions = [],
    quizzes = [],
    activeSession = null,
    stats = {
        total_lessons: 0,
        completed_lessons: 0,
        total_documents: 0,
        total_notes: 0,
        total_sessions: 0,
        total_quizzes: 0,
        progress: 0,
        total_learning_hours: 0,
        total_learning_minutes: 0
    },
    reviews = [],
    userReview = null
}) {
    const { t, isRtl } = useLanguage();
    const isFa = isRtl;

    // Tabs state: 'curriculum' | 'documents' | 'notes' | 'sessions' | 'quizzes' | 'reviews'
    const [activeTab, setActiveTab] = useState('curriculum');

    // Expanded accordion lessons
    const [expandedLessons, setExpandedLessons] = useState({});

    const toggleLesson = (lessonId) => {
        setExpandedLessons((prev) => ({
            ...prev,
            [lessonId]: !prev[lessonId],
        }));
    };

    // Review form state
    const { data: reviewData, setData: setReviewData, post: postReview, processing: reviewProcessing, reset: resetReview } = useForm({
        rating: userReview?.rating || 5,
        comment: userReview?.comment || '',
    });

    const handleReviewSubmit = (e) => {
        e.preventDefault();
        postReview(`/student/courses/${course.slug}/reviews`, {
            preserveScroll: true,
            onSuccess: () => resetReview(),
        });
    };

    // Format schedule days translation
    const dayNamesMap = {
        saturday: isFa ? 'شنبه' : 'Sat',
        sunday: isFa ? 'یکشنبه' : 'Sun',
        monday: isFa ? 'دوشنبه' : 'Mon',
        tuesday: isFa ? 'سه‌شنبه' : 'Tue',
        wednesday: isFa ? 'چهارشنبه' : 'Wed',
        thursday: isFa ? 'پنج‌شنبه' : 'Thu',
        friday: isFa ? 'جمعه' : 'Fri',
    };

    const formattedScheduleDays = useMemo(() => {
        const days = course.schedule?.days || [];
        return days.map((d) => dayNamesMap[d.toLowerCase()] || d).join('، ');
    }, [course.schedule?.days, isFa]);

    return (
        <StudentLayout title={`${course.title || 'Course'} - ${t('brand_title')}`}>
            <Head>
                <title>{`${course.title || 'Course'} - ${t('brand_title')}`}</title>
                <meta
                    name="description"
                    content={`مرکز یادگیری و دروس کورس ${course.title} در پورتال شاگردان ادوُرا تِک.`}
                />
            </Head>

            <div className="space-y-8 max-w-7xl mx-auto pb-16">

                {/* =========================================================
                    1. Hero Banner (3D Ambient with Course Info & Progress)
                   ========================================================= */}
                <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#061E3E] via-slate-900 to-[#1F8FFF] text-white p-6 sm:p-10 lg:p-12 border border-blue-900/50">
                    <Hero3DCanvas />

                    <div className="relative z-10 space-y-5 max-w-4xl">
                        {/* Direction-Aware Breadcrumbs */}
                        <div className="flex items-center gap-2 text-xs font-semibold text-blue-200/90">
                            <Link href="/student/dashboard" className="hover:text-white transition-colors">
                                {t('dashboard')}
                            </Link>
                            {isRtl ? <ChevronLeft size={13} /> : <ChevronRight size={13} />}
                            <Link href="/student/courses" className="hover:text-white transition-colors">
                                {t('my_courses')}
                            </Link>
                            {isRtl ? <ChevronLeft size={13} /> : <ChevronRight size={13} />}
                            <span className="text-cyan-300 truncate max-w-[200px] sm:max-w-xs">{course.title}</span>
                        </div>

                        {/* Title */}
                        <h1 className="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                            {course.title}
                        </h1>

                        {/* Course Metadata Badges */}
                        <div className="flex flex-wrap items-center gap-2.5 pt-1">
                            {course.teacher && (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/20 text-xs font-bold text-slate-100 backdrop-blur-md">
                                    <User size={13} className="text-cyan-400" />
                                    <span>{t('instructor_label')} {course.teacher.name}</span>
                                </span>
                            )}

                            {course.category && (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/20 text-xs font-bold text-slate-100 backdrop-blur-md">
                                    <Tag size={13} className="text-cyan-400" />
                                    <span>{course.category.name}</span>
                                </span>
                            )}

                            {course.level && (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/20 text-xs font-bold text-slate-100 backdrop-blur-md">
                                    <BarChart2 size={13} className="text-cyan-400" />
                                    <span>{t('course_level_label')} {course.level}</span>
                                </span>
                            )}

                            {course.schedule?.start_time && (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/20 text-xs font-bold text-slate-100 backdrop-blur-md">
                                    <Clock size={13} className="text-cyan-400" />
                                    <span>{course.schedule.start_time} - {course.schedule.end_time}</span>
                                    {formattedScheduleDays && <span className="text-cyan-300">({formattedScheduleDays})</span>}
                                </span>
                            )}

                            {course.schedule?.start_date && (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 border border-white/20 text-xs font-bold text-slate-100 backdrop-blur-md">
                                    <Calendar size={13} className="text-cyan-400" />
                                    <span>{course.schedule.start_date} {course.schedule.end_date && `~ ${course.schedule.end_date}`}</span>
                                </span>
                            )}
                        </div>

                        {/* Progress Strip Inside Hero */}
                        <div className="pt-2 space-y-2 max-w-2xl">
                            <div className="flex items-center justify-between text-xs font-bold text-blue-100">
                                <span className="flex items-center gap-1.5">
                                    <CheckCircle2 size={15} className="text-emerald-400" />
                                    <span>{stats.progress}% {t('lessons_completed')}</span>
                                    <span className="text-blue-300 font-mono">({stats.completed_lessons} / {stats.total_lessons})</span>
                                </span>
                                <span className="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono text-[11px] border border-emerald-400/30">
                                    {stats.total_learning_hours > 0
                                        ? `${stats.total_learning_hours} ${t('hours_unit')} ${stats.total_learning_minutes} ${t('minutes_unit')}`
                                        : `${stats.total_learning_minutes} ${t('minutes_unit')}`}
                                </span>
                            </div>

                            <div className="w-full h-2.5 bg-slate-950/40 rounded-full overflow-hidden border border-white/10">
                                <div
                                    className="h-full bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 rounded-full transition-all duration-700 shadow-sm"
                                    style={{ width: `${Math.min(100, stats.progress || 0)}%` }}
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {/* =========================================================
                    2. Live Class Beacon Alert (If Session is Active)
                   ========================================================= */}
                {activeSession && (
                    <div className="p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 text-white shadow-xl shadow-blue-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 animate-in fade-in duration-300">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 rounded-2xl bg-white text-blue-600 flex items-center justify-center shrink-0 shadow-lg relative">
                                <Radio size={24} className="animate-pulse" />
                                <span className="absolute -top-1 -end-1 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-white animate-ping" />
                            </div>
                            <div>
                                <h3 className="text-base sm:text-lg font-black text-white flex items-center gap-2">
                                    <span>{t('live_class_in_progress')}</span>
                                </h3>
                                <p className="text-xs text-blue-100 mt-0.5">
                                    {activeSession.lesson ? (
                                        <span>درس {activeSession.lesson.order}: {activeSession.lesson.title}</span>
                                    ) : (
                                        <span>Google Meet</span>
                                    )}
                                    {activeSession.room_name && <span> · {activeSession.room_name}</span>}
                                </p>
                            </div>
                        </div>

                        <a
                            href={`/student/courses/${course.slug}/sessions/join`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="h-11 px-6 rounded-2xl bg-white hover:bg-slate-50 text-blue-600 font-black text-xs flex items-center gap-2 shadow-lg transition-all active:scale-95 shrink-0"
                        >
                            <ExternalLink size={15} />
                            <span>{t('join_live_class')}</span>
                        </a>
                    </div>
                )}

                {/* =========================================================
                    3. 3D Stat Tiles Grid
                   ========================================================= */}
                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    {/* Stat 1: Lessons */}
                    <TiltCard className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-lg" glowColor="rgba(31, 143, 255, 0.2)">
                        <div className="w-9 h-9 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2.5">
                            <BookOpen size={18} />
                        </div>
                        <div className="text-xl sm:text-2xl font-black text-slate-900 font-mono">
                            {stats.total_lessons}
                        </div>
                        <p className="text-[11px] text-slate-500 font-semibold truncate mt-0.5">
                            {t('curriculum')}
                        </p>
                    </TiltCard>

                    {/* Stat 2: Completed */}
                    <TiltCard className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-lg" glowColor="rgba(16, 185, 129, 0.2)">
                        <div className="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2.5">
                            <CheckCircle2 size={18} />
                        </div>
                        <div className="text-xl sm:text-2xl font-black text-emerald-600 font-mono">
                            {stats.completed_lessons}
                        </div>
                        <p className="text-[11px] text-slate-500 font-semibold truncate mt-0.5">
                            {t('lessons_completed')}
                        </p>
                    </TiltCard>

                    {/* Stat 3: Files & Documents */}
                    <TiltCard className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-lg" glowColor="rgba(245, 158, 11, 0.2)">
                        <div className="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-2.5">
                            <FileText size={18} />
                        </div>
                        <div className="text-xl sm:text-2xl font-black text-amber-600 font-mono">
                            {stats.total_documents}
                        </div>
                        <p className="text-[11px] text-slate-500 font-semibold truncate mt-0.5">
                            {t('course_documents')}
                        </p>
                    </TiltCard>

                    {/* Stat 4: Quizzes */}
                    <TiltCard className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-lg" glowColor="rgba(139, 92, 246, 0.2)">
                        <div className="w-9 h-9 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-2.5">
                            <ClipboardCheck size={18} />
                        </div>
                        <div className="text-xl sm:text-2xl font-black text-purple-600 font-mono">
                            {stats.total_quizzes}
                        </div>
                        <p className="text-[11px] text-slate-500 font-semibold truncate mt-0.5">
                            {t('quizzes')}
                        </p>
                    </TiltCard>

                    {/* Stat 5: Sessions */}
                    <TiltCard className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-lg" glowColor="rgba(14, 165, 233, 0.2)">
                        <div className="w-9 h-9 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center mb-2.5">
                            <Video size={18} />
                        </div>
                        <div className="text-xl sm:text-2xl font-black text-sky-600 font-mono">
                            {stats.total_sessions}
                        </div>
                        <p className="text-[11px] text-slate-500 font-semibold truncate mt-0.5">
                            {t('course_sessions')}
                        </p>
                    </TiltCard>

                    {/* Stat 6: Learning Duration */}
                    <TiltCard className="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-lg" glowColor="rgba(16, 185, 129, 0.2)">
                        <div className="w-9 h-9 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mb-2.5">
                            <Clock size={18} />
                        </div>
                        <div className="text-base sm:text-lg font-black text-teal-700 font-mono truncate">
                            {stats.total_learning_hours > 0 ? `${stats.total_learning_hours}h ${stats.total_learning_minutes}m` : `${stats.total_learning_minutes}m`}
                        </div>
                        <p className="text-[11px] text-slate-500 font-semibold truncate mt-0.5">
                            {t('learning_time')}
                        </p>
                    </TiltCard>
                </div>

                {/* =========================================================
                    4. Navigation Tabs Strip (Curriculum, Files, Notes, etc.)
                   ========================================================= */}
                <div className="bg-white dark:bg-gradient-to-br dark:from-[#0b1329] dark:to-[#050916] rounded-3xl p-2 sm:p-2.5 border border-slate-200/80 dark:border-slate-800/80 shadow-xs transition-all">
                    <div className="flex items-center gap-1.5 overflow-x-auto scrollbar-none py-1">
                        <button
                            type="button"
                            onClick={() => setActiveTab('curriculum')}
                            className={`px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 ${
                                activeTab === 'curriculum'
                                    ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/25 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                    : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            }`}
                        >
                            <BookOpen size={16} className="shrink-0 text-current" />
                            <span>{t('curriculum')}</span>
                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                activeTab === 'curriculum'
                                    ? 'bg-white/20 dark:bg-slate-950/20 text-white dark:text-slate-950 font-extrabold'
                                    : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-cyan-300'
                            }`}>
                                {lessons.length}
                            </span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('documents')}
                            className={`px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 ${
                                activeTab === 'documents'
                                    ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/25 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                    : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            }`}
                        >
                            <FileText size={16} className="shrink-0 text-current" />
                            <span>{t('course_documents')}</span>
                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                activeTab === 'documents'
                                    ? 'bg-white/20 dark:bg-slate-950/20 text-white dark:text-slate-950 font-extrabold'
                                    : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-cyan-300'
                            }`}>
                                {documents.length}
                            </span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('notes')}
                            className={`px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 ${
                                activeTab === 'notes'
                                    ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/25 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                    : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            }`}
                        >
                            <FileText size={16} className="shrink-0 text-current" />
                            <span>{t('class_notes')}</span>
                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                activeTab === 'notes'
                                    ? 'bg-white/20 dark:bg-slate-950/20 text-white dark:text-slate-950 font-extrabold'
                                    : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-cyan-300'
                            }`}>
                                {classNotes.length}
                            </span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('sessions')}
                            className={`px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 ${
                                activeTab === 'sessions'
                                    ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/25 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                    : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            }`}
                        >
                            <Video size={16} className="shrink-0 text-current" />
                            <span>{t('course_sessions')}</span>
                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                activeTab === 'sessions'
                                    ? 'bg-white/20 dark:bg-slate-950/20 text-white dark:text-slate-950 font-extrabold'
                                    : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-cyan-300'
                            }`}>
                                {sessions.length}
                            </span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('quizzes')}
                            className={`px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 ${
                                activeTab === 'quizzes'
                                    ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/25 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                    : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            }`}
                        >
                            <ClipboardCheck size={16} className="shrink-0 text-current" />
                            <span>{t('quizzes')}</span>
                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                activeTab === 'quizzes'
                                    ? 'bg-white/20 dark:bg-slate-950/20 text-white dark:text-slate-950 font-extrabold'
                                    : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-cyan-300'
                            }`}>
                                {quizzes.length}
                            </span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('reviews')}
                            className={`px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 ${
                                activeTab === 'reviews'
                                    ? 'bg-brand-600 dark:bg-gradient-to-r dark:from-cyan-500 dark:to-brand-600 text-white dark:text-slate-950 shadow-md shadow-brand-500/25 dark:shadow-[0_0_15px_rgba(0,240,255,0.4)]'
                                    : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-cyan-300 hover:bg-slate-100 dark:hover:bg-slate-800/80'
                            }`}
                        >
                            <Star size={16} className="shrink-0 text-current" />
                            <span>{t('course_reviews')}</span>
                            <span className={`px-2 py-0.5 rounded-full text-[10px] font-mono ${
                                activeTab === 'reviews'
                                    ? 'bg-white/20 dark:bg-slate-950/20 text-white dark:text-slate-950 font-extrabold'
                                    : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-cyan-300'
                            }`}>
                                {reviews.length}
                            </span>
                        </button>

                        {/* Direct Course Chat Link */}
                        <a
                            href={`/courses/${course.id}/chat`}
                            className="ms-auto px-4 py-2.5 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-brand-600 dark:hover:text-cyan-400 hover:bg-blue-50 dark:hover:bg-slate-800/80 flex items-center gap-1.5 transition-colors shrink-0 border border-transparent dark:border-slate-800"
                        >
                            <MessageSquare size={16} className="shrink-0 text-brand-500 dark:text-cyan-400" />
                            <span>{t('course_chat')}</span>
                            <ExternalLink size={13} className="shrink-0 opacity-70" />
                        </a>
                    </div>
                </div>

                {/* =========================================================
                    5. TAB PANELS CONTENT
                   ========================================================= */}

                {/* --- 5.1 CURRICULUM TAB --- */}
                {activeTab === 'curriculum' && (
                    <div className="space-y-4">
                        {lessons.length > 0 ? (
                            <div className="space-y-3">
                                {lessons.map((lesson, idx) => {
                                    const isExpanded = !!expandedLessons[lesson.id];
                                    const isCompleted = lesson.is_completed;

                                    return (
                                        <div
                                            key={lesson.id}
                                            className={`rounded-3xl border transition-all duration-300 overflow-hidden ${
                                                isCompleted
                                                    ? 'bg-emerald-50/20 border-emerald-200/80 shadow-xs'
                                                    : 'bg-white border-slate-200/80 shadow-xs hover:border-blue-400/40'
                                            }`}
                                        >
                                            {/* Lesson Header Row */}
                                            <div
                                                onClick={() => toggleLesson(lesson.id)}
                                                className="p-4 sm:p-5 flex items-center justify-between gap-4 cursor-pointer select-none"
                                            >
                                                <div className="flex items-center gap-3.5 sm:gap-4 flex-1 min-w-0">
                                                    {/* Number/Check Badge */}
                                                    <div className={`w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xs shrink-0 font-mono transition-colors ${
                                                        isCompleted
                                                            ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20'
                                                            : 'bg-slate-100 text-slate-700'
                                                    }`}>
                                                        {isCompleted ? <Check size={18} /> : idx + 1}
                                                    </div>

                                                    <div className="min-w-0">
                                                        <div className="flex items-center gap-2">
                                                            <h4 className="text-sm sm:text-base font-bold text-slate-900 truncate">
                                                                {lesson.title}
                                                            </h4>
                                                            {isCompleted && (
                                                                <span className="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">
                                                                    {t('lessons_completed')}
                                                                </span>
                                                            )}
                                                            {lesson.is_free && (
                                                                <span className="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold">
                                                                    {t('free_badge')}
                                                                </span>
                                                            )}
                                                        </div>

                                                        <div className="flex items-center gap-3 text-xs text-slate-400 mt-1">
                                                            {lesson.duration_minutes && (
                                                                <span className="flex items-center gap-1 font-mono">
                                                                    <Clock size={12} />
                                                                    <span>{lesson.duration_minutes} {t('minutes_unit')}</span>
                                                                </span>
                                                            )}
                                                            {lesson.documents?.length > 0 && (
                                                                <span className="flex items-center gap-1">
                                                                    <Download size={12} />
                                                                    <span>{lesson.documents.length} {t('attached_files')}</span>
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="flex items-center gap-3 shrink-0">
                                                    <span className={`text-xs font-bold hidden sm:inline-block ${
                                                        isCompleted ? 'text-emerald-600' : 'text-slate-400'
                                                    }`}>
                                                        {isCompleted ? t('lessons_completed') : t('pending_completion')}
                                                    </span>

                                                    <div className="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform">
                                                        {isExpanded ? <ChevronUp size={16} /> : <ChevronDown size={16} />}
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Expandable Lesson Details */}
                                            {isExpanded && (
                                                <div className="px-5 pb-5 pt-2 border-t border-slate-100 space-y-4 text-xs text-slate-700 animate-in fade-in duration-200">
                                                    {lesson.description && (
                                                        <p className="leading-relaxed text-slate-600 font-normal">
                                                            {lesson.description}
                                                        </p>
                                                    )}

                                                    {lesson.content && (
                                                        <div className="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-slate-800 leading-relaxed font-normal whitespace-pre-line">
                                                            {lesson.content}
                                                        </div>
                                                    )}

                                                    {/* Lesson Attached Files */}
                                                    {lesson.documents?.length > 0 && (
                                                        <div className="space-y-2 pt-2">
                                                            <span className="font-bold text-slate-800 flex items-center gap-1.5">
                                                                <Download size={14} className="text-blue-500" />
                                                                <span>{t('attached_files')} ({lesson.documents.length}):</span>
                                                            </span>
                                                            <div className="flex flex-wrap gap-2">
                                                                {lesson.documents.map((doc) => (
                                                                    <a
                                                                        key={doc.id}
                                                                        href={doc.file_path}
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        className="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-blue-400 hover:text-blue-600 text-slate-700 text-xs font-semibold flex items-center gap-2 shadow-2xs transition-colors"
                                                                    >
                                                                        {getFileIcon(doc.file_name)}
                                                                        <span className="truncate max-w-xs">{doc.title}</span>
                                                                    </a>
                                                                ))}
                                                            </div>
                                                        </div>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    );
                                })}
                            </div>
                        ) : (
                            <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs max-w-md mx-auto">
                                <BookOpen size={40} className="text-slate-300 mx-auto mb-3" />
                                <h4 className="text-base font-bold text-slate-800">{t('no_lessons_yet')}</h4>
                                <p className="text-xs text-slate-500 mt-1">{t('no_lessons_desc')}</p>
                            </div>
                        )}
                    </div>
                )}

                {/* --- 5.2 DOCUMENTS TAB --- */}
                {activeTab === 'documents' && (
                    <div>
                        {documents.length > 0 ? (
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                {documents.map((doc) => (
                                    <TiltCard
                                        key={doc.id}
                                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg flex flex-col justify-between"
                                        glowColor="rgba(245, 158, 11, 0.15)"
                                    >
                                        <div>
                                            <div className="flex items-center justify-between mb-3">
                                                <div className="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                                    {getFileIcon(doc.file_name)}
                                                </div>
                                                {doc.lesson_title && (
                                                    <span className="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold truncate max-w-[130px]">
                                                        {doc.lesson_title}
                                                    </span>
                                                )}
                                            </div>

                                            <h4 className="text-sm font-bold text-slate-900 line-clamp-2">
                                                {doc.title}
                                            </h4>
                                            <p className="text-[11px] text-slate-400 mt-1 font-mono">
                                                {doc.file_name} {doc.file_size ? `· ${doc.file_size}` : ''}
                                            </p>
                                        </div>

                                        <div className="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                                            <span className="text-[10px] text-slate-400">{doc.created_at}</span>
                                            <a
                                                href={doc.file_path}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold flex items-center gap-1.5 transition-colors"
                                            >
                                                <Download size={13} />
                                                <span>{t('download_file')}</span>
                                            </a>
                                        </div>
                                    </TiltCard>
                                ))}
                            </div>
                        ) : (
                            <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs max-w-md mx-auto">
                                <FileText size={40} className="text-slate-300 mx-auto mb-3" />
                                <h4 className="text-base font-bold text-slate-800">{t('no_documents_yet')}</h4>
                                <p className="text-xs text-slate-500 mt-1">{t('no_documents_desc')}</p>
                            </div>
                        )}
                    </div>
                )}

                {/* --- 5.3 CLASS NOTES TAB --- */}
                {activeTab === 'notes' && (
                    <div>
                        {classNotes.length > 0 ? (
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                {classNotes.map((note) => (
                                    <TiltCard
                                        key={note.id}
                                        className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-lg flex flex-col justify-between"
                                        glowColor="rgba(14, 165, 233, 0.15)"
                                    >
                                        <div>
                                            <div className="flex items-center justify-between mb-3 text-xs text-slate-400">
                                                <span className="font-bold text-blue-600">{note.teacher_name}</span>
                                                <span className="font-mono text-[11px]">{note.created_at}</span>
                                            </div>

                                            <h4 className="text-base font-bold text-slate-900 mb-2">
                                                {note.title}
                                            </h4>

                                            <p className="text-xs text-slate-600 leading-relaxed line-clamp-4 whitespace-pre-line">
                                                {note.content}
                                            </p>
                                        </div>

                                        <div className="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-[11px] text-slate-400">
                                            <Clock size={12} />
                                            <span>{isFa ? 'یادداشت تدریس استاد' : 'Teacher class note'}</span>
                                        </div>
                                    </TiltCard>
                                ))}
                            </div>
                        ) : (
                            <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs max-w-md mx-auto">
                                <FileText size={40} className="text-slate-300 mx-auto mb-3" />
                                <h4 className="text-base font-bold text-slate-800">{t('no_notes_yet')}</h4>
                                <p className="text-xs text-slate-500 mt-1">{t('no_notes_desc')}</p>
                            </div>
                        )}
                    </div>
                )}

                {/* --- 5.4 SESSIONS TAB --- */}
                {activeTab === 'sessions' && (
                    <div>
                        {sessions.length > 0 ? (
                            <div className="space-y-3">
                                {sessions.map((sess) => (
                                    <TiltCard
                                        key={sess.id}
                                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                                        glowColor="rgba(31, 143, 255, 0.15)"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                                <Video size={20} />
                                            </div>
                                            <div>
                                                <h4 className="text-sm sm:text-base font-bold text-slate-900">
                                                    {sess.title}
                                                </h4>
                                                <div className="flex flex-wrap items-center gap-3 text-xs text-slate-400 mt-1">
                                                    {sess.started_at && (
                                                        <span className="font-mono">{sess.started_at}</span>
                                                    )}
                                                    {sess.duration && (
                                                        <span className="font-mono">({sess.duration} {t('minutes_unit')})</span>
                                                    )}
                                                    {sess.room_name && (
                                                        <span>· {sess.room_name}</span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-3 w-full sm:w-auto justify-end">
                                            <span className={`px-3 py-1 rounded-full text-xs font-bold ${
                                                sess.status === 'active'
                                                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse'
                                                    : 'bg-slate-100 text-slate-600'
                                            }`}>
                                                {sess.status === 'active' ? t('live_class_in_progress') : t('lessons_completed')}
                                            </span>

                                            {sess.meet_link && (
                                                <a
                                                    href={sess.meet_link}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="h-10 px-4 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold flex items-center gap-1.5 transition-colors"
                                                >
                                                    <ExternalLink size={13} />
                                                    <span>Meet</span>
                                                </a>
                                            )}
                                        </div>
                                    </TiltCard>
                                ))}
                            </div>
                        ) : (
                            <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs max-w-md mx-auto">
                                <Video size={40} className="text-slate-300 mx-auto mb-3" />
                                <h4 className="text-base font-bold text-slate-800">{t('no_sessions_yet')}</h4>
                                <p className="text-xs text-slate-500 mt-1">{t('no_sessions_desc')}</p>
                            </div>
                        )}
                    </div>
                )}

                {/* --- 5.5 QUIZZES TAB --- */}
                {activeTab === 'quizzes' && (
                    <div>
                        {quizzes.length > 0 ? (
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                {quizzes.map((quiz) => (
                                    <TiltCard
                                        key={quiz.id}
                                        className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-lg flex flex-col justify-between"
                                        glowColor="rgba(139, 92, 246, 0.15)"
                                    >
                                        <div>
                                            <div className="flex items-center justify-between mb-3">
                                                <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-bold text-xs">
                                                    <ClipboardCheck size={13} />
                                                    <span>+{quiz.xp_reward} XP</span>
                                                </span>
                                                <span className="text-xs text-slate-400 font-mono">
                                                    {quiz.duration_minutes} {t('minutes_unit')}
                                                </span>
                                            </div>

                                            <h4 className="text-base font-bold text-slate-900 line-clamp-2">
                                                {quiz.title}
                                            </h4>

                                            <p className="text-xs text-slate-500 mt-1.5 line-clamp-2">
                                                {quiz.description || (isFa ? 'برای سنجش یادگیری، در این آزمون شرکت کنید.' : 'Test your knowledge on this topic.')}
                                            </p>

                                            <div className="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                                                <span>{quiz.questions_count} {t('quiz_questions_count')}</span>
                                                {quiz.attempt ? (
                                                    <span className="font-bold text-emerald-600 font-mono">
                                                        {quiz.attempt.score} / {quiz.attempt.total_points}
                                                    </span>
                                                ) : (
                                                    <span className="text-slate-400">{t('filter_available')}</span>
                                                )}
                                            </div>
                                        </div>

                                        <div className="pt-4 mt-4 border-t border-slate-100">
                                            <a
                                                href={`/student/quizzes/${quiz.id}/take`}
                                                className="w-full h-10 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all"
                                            >
                                                <PlayCircle size={14} />
                                                <span>{quiz.attempt ? t('quiz_retake_btn') : t('quiz_start_btn')}</span>
                                            </a>
                                        </div>
                                    </TiltCard>
                                ))}
                            </div>
                        ) : (
                            <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs max-w-md mx-auto">
                                <ClipboardCheck size={40} className="text-slate-300 mx-auto mb-3" />
                                <h4 className="text-base font-bold text-slate-800">{t('quiz_no_quizzes')}</h4>
                                <p className="text-xs text-slate-500 mt-1">{t('quiz_no_quizzes_desc')}</p>
                            </div>
                        )}
                    </div>
                )}

                {/* --- 5.6 REVIEWS TAB --- */}
                {activeTab === 'reviews' && (
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        {/* Left: Review Submission Form (5 cols) */}
                        <div className="lg:col-span-5 space-y-5">
                            <TiltCard className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs" glowColor="rgba(245, 158, 11, 0.15)">
                                <h3 className="text-base font-bold text-slate-900 mb-1">
                                    {t('leave_review')}
                                </h3>
                                <p className="text-xs text-slate-400 mb-5">
                                    {isFa ? 'نظر شما به سایر محصلین و بهبود تدریس کمک می‌کند.' : 'Share your feedback to help improve the learning experience.'}
                                </p>

                                <form onSubmit={handleReviewSubmit} className="space-y-4">
                                    {/* Star Rating */}
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-bold text-slate-700">
                                            {t('your_rating')}
                                        </label>
                                        <div className="flex items-center gap-2">
                                            {[1, 2, 3, 4, 5].map((star) => (
                                                <button
                                                    key={star}
                                                    type="button"
                                                    onClick={() => setReviewData('rating', star)}
                                                    className="p-1 hover:scale-110 transition-transform"
                                                >
                                                    <Star
                                                        size={24}
                                                        className={
                                                            star <= reviewData.rating
                                                                ? 'fill-amber-400 text-amber-400'
                                                                : 'text-slate-200'
                                                        }
                                                    />
                                                </button>
                                            ))}
                                            <span className="text-xs font-bold text-amber-600 font-mono ms-2">
                                                {reviewData.rating} / 5
                                            </span>
                                        </div>
                                    </div>

                                    {/* Comment */}
                                    <div className="space-y-1.5">
                                        <textarea
                                            value={reviewData.comment}
                                            onChange={(e) => setReviewData('comment', e.target.value)}
                                            rows={4}
                                            required
                                            placeholder={t('review_placeholder')}
                                            className="w-full p-4 text-xs font-medium bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all leading-relaxed"
                                        />
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={reviewProcessing}
                                        className="w-full h-11 px-5 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-amber-500/20 transition-all active:scale-95 disabled:opacity-50"
                                    >
                                        <Send size={14} />
                                        <span>{reviewProcessing ? t('saving') : t('submit_review')}</span>
                                    </button>
                                </form>
                            </TiltCard>
                        </div>

                        {/* Right: Reviews List (7 cols) */}
                        <div className="lg:col-span-7 space-y-4">
                            {reviews.length > 0 ? (
                                reviews.map((rev) => (
                                    <div
                                        key={rev.id}
                                        className="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-3"
                                    >
                                        <div className="flex items-center justify-between">
                                            <div className="flex items-center gap-3">
                                                <img
                                                    src={rev.user_avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(rev.user_name)}&size=80&background=1F8FFF&color=fff`}
                                                    alt={rev.user_name}
                                                    className="w-10 h-10 rounded-2xl object-cover"
                                                />
                                                <div>
                                                    <h5 className="text-xs sm:text-sm font-bold text-slate-900">
                                                        {rev.user_name}
                                                    </h5>
                                                    <span className="text-[11px] text-slate-400">{rev.created_at}</span>
                                                </div>
                                            </div>

                                            <div className="flex items-center gap-1">
                                                {[1, 2, 3, 4, 5].map((star) => (
                                                    <Star
                                                        key={star}
                                                        size={14}
                                                        className={
                                                            star <= rev.rating
                                                                ? 'fill-amber-400 text-amber-400'
                                                                : 'text-slate-200'
                                                        }
                                                    />
                                                ))}
                                            </div>
                                        </div>

                                        <p className="text-xs text-slate-700 leading-relaxed font-normal">
                                            {rev.comment}
                                        </p>
                                    </div>
                                ))
                            ) : (
                                <div className="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs">
                                    <Star size={40} className="text-slate-300 mx-auto mb-3" />
                                    <h4 className="text-base font-bold text-slate-800">{t('no_reviews_yet')}</h4>
                                    <p className="text-xs text-slate-500 mt-1">{t('no_reviews_desc')}</p>
                                </div>
                            )}
                        </div>
                    </div>
                )}

            </div>
        </StudentLayout>
    );
}
