import React, { useState, useEffect, useRef } from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import {
    User,
    Mail,
    Phone,
    BookOpen,
    Camera,
    CheckCircle2,
    AlertCircle,
    Save,
    ShieldCheck,
    Trophy,
    Zap,
    GraduationCap,
    Calendar,
    Globe,
    Link2,
    Sparkles,
    SlidersHorizontal,
    Award,
    Clock,
    FileText,
    ExternalLink,
    Check
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
        let height = (canvas.height = canvas.parentElement?.offsetHeight || 280);

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

            // Connection lines
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

            // Draw glowing points
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
            className="absolute inset-0 pointer-events-none z-0 opacity-70"
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

        const rotateX = -((y - centerY) / centerY) * 6;
        const rotateY = ((x - centerX) / centerX) * 6;

        setStyle({
            transform: `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.01, 1.01, 1.01)`,
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
   3. Main Student Profile Component
   ========================================================================= */
export default function Profile({
    profileUser = {},
    stats = {
        enrolled_courses: 0,
        completed_courses: 0,
        level: 1,
        level_title: 'Scholar',
        progress: 0,
        total_xp: 0
    },
    studentProfile = null
}) {
    const { t, isRtl } = useLanguage();
    const isFa = isRtl;

    // Form setup with Inertia
    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        name: profileUser.name || '',
        email: profileUser.email || '',
        phone: profileUser.phone || '',
        department: profileUser.department || '',
        bio: profileUser.bio || '',
        linkedin: profileUser.linkedin || '',
        github: profileUser.github || '',
        website: profileUser.website || '',
        avatar: null,
        cover_image: null,
    });

    // Preview state for files
    const [avatarPreview, setAvatarPreview] = useState(profileUser.avatar);
    const [coverPreview, setCoverPreview] = useState(profileUser.cover_image);

    const avatarInputRef = useRef(null);
    const coverInputRef = useRef(null);

    const handleAvatarChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('avatar', file);
            setAvatarPreview(URL.createObjectURL(file));
        }
    };

    const handleCoverChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('cover_image', file);
            setCoverPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/student/profile', {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    // Character counter for bio
    const bioLength = data.bio ? data.bio.length : 0;
    const bioMax = 1000;
    const bioPercent = Math.min(100, Math.round((bioLength / bioMax) * 100));

    return (
        <StudentLayout title={`${t('profile')} - ${t('brand_title')}`}>
            <Head>
                <title>{`${t('profile')} - ${t('brand_title')}`}</title>
                <meta
                    name="description"
                    content="پروفایل کاربری، معلومات فردی و سوابق تحصیلی شاگرد در ادوُرا تِک."
                />
            </Head>

            <div className="space-y-8 max-w-7xl mx-auto pb-16">

                {/* =========================================================
                    1. Hero Profile Banner (3D Ambient with Live Cover & Avatar)
                   ========================================================= */}
                <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-white border border-slate-200/80">

                    {/* Cover Area with 3D Canvas */}
                    <div
                        className="relative h-48 sm:h-64 w-full bg-cover bg-center overflow-hidden transition-all duration-500"
                        style={{
                            backgroundImage: coverPreview
                                ? `url('${coverPreview}')`
                                : `linear-gradient(135deg, #061E3E 0%, #0f1c3f 50%, #1F8FFF 100%)`,
                        }}
                    >
                        <Hero3DCanvas />

                        {/* Dark Gradient Overlay for Contrast */}
                        <div className="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent" />

                        {/* Top Badge */}
                        <div className="absolute top-4 sm:top-6 start-4 sm:start-6 z-10">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-cyan-300 backdrop-blur-md shadow-sm">
                                <Sparkles size={14} className="text-cyan-400" />
                                <span>{t('profile_hub_badge')}</span>
                            </span>
                        </div>

                        {/* Cover Image Upload Button */}
                        <div className="absolute top-4 sm:top-6 end-4 sm:end-6 z-10">
                            <input
                                ref={coverInputRef}
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                className="hidden"
                                onChange={handleCoverChange}
                            />
                            <button
                                type="button"
                                onClick={() => coverInputRef.current?.click()}
                                className="px-3.5 py-1.5 rounded-xl bg-slate-950/60 hover:bg-slate-950/80 border border-white/20 text-white text-xs font-bold flex items-center gap-1.5 backdrop-blur-md transition-all shadow-md active:scale-95"
                                title={t('cover_hint')}
                            >
                                <Camera size={14} className="text-cyan-300" />
                                <span>{t('change_cover')}</span>
                            </button>
                        </div>
                    </div>

                    {/* Profile Identity Bar */}
                    <div className="px-6 sm:px-10 pb-6 pt-4 relative">
                        <div className="flex flex-col md:flex-row items-center md:items-end justify-between gap-6 -mt-16 sm:-mt-20 relative z-20">

                            {/* Avatar & Basic Info */}
                            <div className="flex flex-col sm:flex-row items-center sm:items-end gap-4 sm:gap-6 text-center sm:text-start">

                                {/* Avatar Box with Specular Ring */}
                                <div className="relative group">
                                    <input
                                        ref={avatarInputRef}
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        className="hidden"
                                        onChange={handleAvatarChange}
                                    />
                                    <div className="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl p-1 bg-white shadow-2xl ring-4 ring-blue-500/20 relative overflow-hidden">
                                        <img
                                            src={avatarPreview || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name || 'Student')}&size=160&background=1F8FFF&color=fff`}
                                            alt={data.name}
                                            className="w-full h-full object-cover rounded-[22px]"
                                            onError={(e) => {
                                                e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name || 'Student')}&size=160&background=1F8FFF&color=fff`;
                                            }}
                                        />
                                    </div>

                                    {/* Avatar Change Overlay Button */}
                                    <button
                                        type="button"
                                        onClick={() => avatarInputRef.current?.click()}
                                        className="absolute -bottom-1 -end-1 w-9 h-9 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center shadow-lg border-2 border-white transition-all active:scale-95"
                                        title={t('avatar_hint')}
                                    >
                                        <Camera size={15} />
                                    </button>
                                </div>

                                {/* Identity Text & Badges */}
                                <div className="space-y-2">
                                    <div className="flex items-center justify-center sm:justify-start gap-2">
                                        <h1 className="text-xl sm:text-2xl font-black text-slate-900">
                                            {data.name || 'Student'}
                                        </h1>
                                        <span className="text-blue-500" title={t('verified_account')}>
                                            <CheckCircle2 size={20} className="fill-blue-500 text-white" />
                                        </span>
                                    </div>

                                    <div className="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
                                            <GraduationCap size={13} />
                                            <span>{t('role_student')}</span>
                                        </span>

                                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-100 font-mono">
                                            <Award size={13} />
                                            <span>{isFa ? `سطح ${stats.level} · ${stats.level_title}` : `Level ${stats.level} · ${stats.level_title}`}</span>
                                        </span>

                                        {data.department && (
                                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold">
                                                <BookOpen size={13} />
                                                <span>{data.department}</span>
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Header Save Button */}
                            <div className="flex items-center gap-3">
                                {recentlySuccessful && (
                                    <span className="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 animate-in fade-in">
                                        <Check size={16} />
                                        <span>{t('profile_updated_success')}</span>
                                    </span>
                                )}

                                <button
                                    type="submit"
                                    form="student-profile-form"
                                    disabled={processing}
                                    className="h-11 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-blue-500/25 transition-all active:scale-95 disabled:opacity-50"
                                >
                                    <Save size={16} />
                                    <span>{processing ? t('saving_profile') : t('save_profile_btn')}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* =========================================================
                    2. 3D Stat Tiles Grid
                   ========================================================= */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    {/* Stat 1: Enrolled Courses */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-blue-400/40"
                        glowColor="rgba(31, 143, 255, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-xs">
                                <BookOpen size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-slate-400">Courses</span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-slate-900 font-mono">
                            {stats.enrolled_courses}
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('stat_enrolled_courses')}
                        </p>
                    </TiltCard>

                    {/* Stat 2: Completed Courses */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-emerald-400/40"
                        glowColor="rgba(16, 185, 129, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-xs">
                                <CheckCircle2 size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-emerald-600 font-mono">
                                {stats.enrolled_courses > 0 ? Math.round((stats.completed_courses / stats.enrolled_courses) * 100) : 0}%
                            </span>
                        </div>
                        <div className="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">
                            {stats.completed_courses}
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('stat_completed_courses')}
                        </p>
                    </TiltCard>

                    {/* Stat 3: Total XP */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-purple-400/40"
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
                            {t('stat_total_xp')}
                        </p>
                    </TiltCard>

                    {/* Stat 4: Academic Level */}
                    <TiltCard
                        className="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-amber-400/40"
                        glowColor="rgba(245, 158, 11, 0.2)"
                    >
                        <div className="flex items-center justify-between mb-3">
                            <div className="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-xs">
                                <Trophy size={20} />
                            </div>
                            <span className="text-[11px] font-bold text-amber-600 font-mono">
                                Lvl {stats.level}
                            </span>
                        </div>
                        <div className="text-base sm:text-lg font-black text-slate-900 truncate">
                            {stats.level_title}
                        </div>
                        <p className="text-xs text-slate-500 font-medium mt-1">
                            {t('level_badge_label')}
                        </p>
                    </TiltCard>
                </div>

                {/* =========================================================
                    3. Main Grid Layout (Sidebar Column + Main Form Column)
                   ========================================================= */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    {/* Left Column: Verification Card & Account Overview (4 cols) */}
                    <div className="lg:col-span-4 space-y-6">

                        {/* KYC Verification Card */}
                        <TiltCard
                            className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs"
                            glowColor={studentProfile?.is_complete ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)'}
                        >
                            <div className="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                                <ShieldCheck size={18} className="text-blue-600" />
                                <h3 className="text-sm font-bold text-slate-900">
                                    {isFa ? 'تایید هویت و اسناد شاگرد' : 'Student Verification'}
                                </h3>
                            </div>

                            {studentProfile?.is_complete ? (
                                <div className="space-y-4">
                                    <div className="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/60 space-y-2">
                                        <div className="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                                            <CheckCircle2 size={16} className="text-emerald-600" />
                                            <span>{t('kyc_verified_title')}</span>
                                        </div>
                                        <p className="text-xs text-emerald-700 leading-relaxed">
                                            {t('kyc_verified_desc')}
                                        </p>
                                    </div>

                                    <Link
                                        href="/student/profile-details"
                                        className="w-full h-11 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold flex items-center justify-center gap-2 transition-colors"
                                    >
                                        <FileText size={15} />
                                        <span>{t('btn_view_kyc')}</span>
                                    </Link>
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    <div className="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/60 space-y-2">
                                        <div className="flex items-center gap-2 text-amber-800 font-bold text-xs">
                                            <AlertCircle size={16} className="text-amber-600" />
                                            <span>{t('kyc_incomplete_title')}</span>
                                        </div>
                                        <p className="text-xs text-amber-700 leading-relaxed">
                                            {t('kyc_incomplete_desc')}
                                        </p>
                                    </div>

                                    <Link
                                        href="/student/profile-details"
                                        className="w-full h-11 px-4 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-md shadow-amber-500/20 transition-all active:scale-95"
                                    >
                                        <FileText size={15} />
                                        <span>{t('btn_complete_kyc')}</span>
                                    </Link>
                                </div>
                            )}
                        </TiltCard>

                        {/* Learning Journey Overview Card */}
                        <TiltCard
                            className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs"
                            glowColor="rgba(31, 143, 255, 0.15)"
                        >
                            <div className="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                                <SlidersHorizontal size={18} className="text-blue-600" />
                                <h3 className="text-sm font-bold text-slate-900">
                                    {t('learning_overview')}
                                </h3>
                            </div>

                            <div className="space-y-3.5 text-xs">
                                <div className="flex items-center justify-between py-1">
                                    <span className="text-slate-500 flex items-center gap-2">
                                        <Calendar size={14} className="text-slate-400" />
                                        <span>{t('stat_member_since')}</span>
                                    </span>
                                    <span className="font-bold text-slate-800 font-mono">
                                        {profileUser.member_since || 'Recent'}
                                    </span>
                                </div>

                                <div className="flex items-center justify-between py-1">
                                    <span className="text-slate-500 flex items-center gap-2">
                                        <CheckCircle2 size={14} className="text-emerald-500" />
                                        <span>{t('stat_account_status')}</span>
                                    </span>
                                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px]">
                                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                                        <span>{t('active_status')}</span>
                                    </span>
                                </div>

                                <div className="flex items-center justify-between py-1">
                                    <span className="text-slate-500 flex items-center gap-2">
                                        <Mail size={14} className="text-slate-400" />
                                        <span>Email</span>
                                    </span>
                                    <span className="font-bold text-slate-800 truncate max-w-[170px]" title={data.email}>
                                        {data.email}
                                    </span>
                                </div>
                            </div>

                            {/* Quick Links */}
                            <div className="mt-5 pt-4 border-t border-slate-100 space-y-2">
                                <Link
                                    href="/scoring-help"
                                    className="p-3 rounded-2xl bg-blue-50/60 hover:bg-blue-50 text-blue-700 text-xs font-bold flex items-center justify-between transition-colors group"
                                >
                                    <span className="flex items-center gap-2">
                                        <Zap size={14} className="text-blue-500" />
                                        <span>{t('how_scoring_works')}</span>
                                    </span>
                                    <ExternalLink size={13} className="text-blue-400 group-hover:translate-x-0.5 transition-transform" />
                                </Link>

                                <Link
                                    href="/leaderboard"
                                    className="p-3 rounded-2xl bg-amber-50/60 hover:bg-amber-50 text-amber-800 text-xs font-bold flex items-center justify-between transition-colors group"
                                >
                                    <span className="flex items-center gap-2">
                                        <Trophy size={14} className="text-amber-500" />
                                        <span>{t('leaderboard')}</span>
                                    </span>
                                    <ExternalLink size={13} className="text-amber-400 group-hover:translate-x-0.5 transition-transform" />
                                </Link>
                            </div>
                        </TiltCard>

                    </div>

                    {/* Right Main Column: Profile Form (8 cols) */}
                    <div className="lg:col-span-8 space-y-6">
                        <form id="student-profile-form" onSubmit={handleSubmit} className="space-y-6">

                            {/* Panel 1: Personal Information */}
                            <TiltCard
                                className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs"
                                glowColor="rgba(31, 143, 255, 0.15)"
                            >
                                <div className="flex items-center gap-2.5 mb-6 pb-4 border-b border-slate-100">
                                    <div className="w-9 h-9 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <User size={18} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-slate-900">
                                            {t('card_personal_title')}
                                        </h3>
                                        <p className="text-xs text-slate-400">
                                            {isFa ? 'اطلاعات هویتی و راه‌های ارتباطی با شما' : 'Basic identity and communication details'}
                                        </p>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    {/* Full Name */}
                                    <div className="space-y-2">
                                        <label className="text-xs font-bold text-slate-700 flex items-center gap-1">
                                            <span>{t('full_name')}</span>
                                            <span className="text-rose-500">*</span>
                                        </label>
                                        <div className="relative">
                                            <input
                                                type="text"
                                                value={data.name}
                                                onChange={(e) => setData('name', e.target.value)}
                                                placeholder={t('full_name_placeholder')}
                                                required
                                                className={`w-full h-11 px-4 text-xs font-medium bg-slate-50 border rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all ${
                                                    errors.name ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200'
                                                }`}
                                            />
                                            <div className={`absolute top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none ${
                                                isRtl ? 'left-3' : 'right-3'
                                            }`}>
                                                <User size={15} />
                                            </div>
                                        </div>
                                        {errors.name && (
                                            <p className="text-[11px] text-rose-500 font-semibold">{errors.name}</p>
                                        )}
                                    </div>

                                    {/* Email */}
                                    <div className="space-y-2">
                                        <label className="text-xs font-bold text-slate-700 flex items-center gap-1">
                                            <span>{t('email_address')}</span>
                                            <span className="text-rose-500">*</span>
                                        </label>
                                        <div className="relative">
                                            <input
                                                type="email"
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder="student@example.com"
                                                required
                                                className={`w-full h-11 px-4 text-xs font-medium bg-slate-50 border rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all ${
                                                    errors.email ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200'
                                                }`}
                                            />
                                            <div className={`absolute top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none ${
                                                isRtl ? 'left-3' : 'right-3'
                                            }`}>
                                                <Mail size={15} />
                                            </div>
                                        </div>
                                        {errors.email && (
                                            <p className="text-[11px] text-rose-500 font-semibold">{errors.email}</p>
                                        )}
                                    </div>

                                    {/* Phone */}
                                    <div className="space-y-2">
                                        <label className="text-xs font-bold text-slate-700">
                                            {t('phone_number_label')}
                                        </label>
                                        <div className="relative">
                                            <input
                                                type="tel"
                                                value={data.phone}
                                                onChange={(e) => setData('phone', e.target.value)}
                                                placeholder="+93 700 000 000"
                                                className={`w-full h-11 px-4 text-xs font-medium bg-slate-50 border rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all ${
                                                    errors.phone ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200'
                                                }`}
                                            />
                                            <div className={`absolute top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none ${
                                                isRtl ? 'left-3' : 'right-3'
                                            }`}>
                                                <Phone size={15} />
                                            </div>
                                        </div>
                                        {errors.phone && (
                                            <p className="text-[11px] text-rose-500 font-semibold">{errors.phone}</p>
                                        )}
                                    </div>

                                    {/* Department / Discipline */}
                                    <div className="space-y-2">
                                        <label className="text-xs font-bold text-slate-700">
                                            {t('department_label')}
                                        </label>
                                        <div className="relative">
                                            <select
                                                value={data.department}
                                                onChange={(e) => setData('department', e.target.value)}
                                                className={`w-full h-11 px-4 text-xs font-medium bg-slate-50 border rounded-2xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none cursor-pointer ${
                                                    errors.department ? 'border-rose-400 bg-rose-50/20' : 'border-slate-200'
                                                }`}
                                            >
                                                <option value="">{t('select_department')}</option>
                                                <optgroup label={isFa ? 'تکنالوژی و برنامه‌نویسی' : 'Technology & Programming'}>
                                                    <option value="Computer Science">{t('dept_cs')}</option>
                                                    <option value="Software Engineering">{t('dept_se')}</option>
                                                    <option value="Web Development">{t('dept_web')}</option>
                                                    <option value="Artificial Intelligence">{t('dept_ai')}</option>
                                                </optgroup>
                                                <optgroup label={isFa ? 'دیزاین و گرافیک' : 'Design & Creative'}>
                                                    <option value="UI/UX Design">{t('dept_design')}</option>
                                                    <option value="Graphic Design">{t('dept_graphic')}</option>
                                                </optgroup>
                                                <optgroup label={isFa ? 'مدیریت و علوم' : 'Business & Languages'}>
                                                    <option value="Business Administration">{t('dept_business')}</option>
                                                    <option value="English Language">{t('dept_english')}</option>
                                                    <option value="Medicine">{t('dept_medicine')}</option>
                                                    <option value="Other">{t('dept_other')}</option>
                                                </optgroup>
                                            </select>
                                            <div className={`absolute top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none ${
                                                isRtl ? 'left-3' : 'right-3'
                                            }`}>
                                                <BookOpen size={15} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </TiltCard>

                            {/* Panel 2: About Me (Bio) */}
                            <TiltCard
                                className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs"
                                glowColor="rgba(139, 92, 246, 0.15)"
                            >
                                <div className="flex items-center gap-2.5 mb-6 pb-4 border-b border-slate-100">
                                    <div className="w-9 h-9 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                        <FileText size={18} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-slate-900">
                                            {t('card_bio_title')}
                                        </h3>
                                        <p className="text-xs text-slate-400">
                                            {t('bio_desc')}
                                        </p>
                                    </div>
                                </div>

                                <div className="space-y-3">
                                    <textarea
                                        value={data.bio}
                                        onChange={(e) => setData('bio', e.target.value)}
                                        rows={4}
                                        maxLength={bioMax}
                                        placeholder={t('bio_placeholder')}
                                        className="w-full p-4 text-xs font-medium bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all leading-relaxed"
                                    />

                                    <div className="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                                        <span>{isFa ? 'معرفی اهداف علمی و مهارتی' : 'Highlight your academic passions'}</span>
                                        <div className="flex items-center gap-2">
                                            <div className="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div
                                                    className="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full transition-all"
                                                    style={{ width: `${bioPercent}%` }}
                                                />
                                            </div>
                                            <span className="font-mono">{bioLength} / {bioMax}</span>
                                        </div>
                                    </div>
                                </div>
                            </TiltCard>

                            {/* Panel 3: Social & Portfolio Links */}
                            <TiltCard
                                className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs"
                                glowColor="rgba(16, 185, 129, 0.15)"
                            >
                                <div className="flex items-center gap-2.5 mb-6 pb-4 border-b border-slate-100">
                                    <div className="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <Globe size={18} />
                                    </div>
                                    <div>
                                        <h3 className="text-base font-bold text-slate-900">
                                            {t('card_social_title')}
                                        </h3>
                                        <p className="text-xs text-slate-400">
                                            {t('social_desc')}
                                        </p>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    {/* LinkedIn */}
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                            <i className="bi bi-linkedin text-[#0a66c2]"></i>
                                            <span>LinkedIn</span>
                                        </label>
                                        <input
                                            type="url"
                                            value={data.linkedin}
                                            onChange={(e) => setData('linkedin', e.target.value)}
                                            placeholder="https://linkedin.com/in/..."
                                            className="w-full h-11 px-3.5 text-xs font-medium bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono"
                                        />
                                    </div>

                                    {/* GitHub */}
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                            <i className="bi bi-github text-slate-800"></i>
                                            <span>GitHub</span>
                                        </label>
                                        <input
                                            type="url"
                                            value={data.github}
                                            onChange={(e) => setData('github', e.target.value)}
                                            placeholder="https://github.com/..."
                                            className="w-full h-11 px-3.5 text-xs font-medium bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono"
                                        />
                                    </div>

                                    {/* Website / Portfolio */}
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                            <Globe size={14} className="text-emerald-600" />
                                            <span>Website / Portfolio</span>
                                        </label>
                                        <input
                                            type="url"
                                            value={data.website}
                                            onChange={(e) => setData('website', e.target.value)}
                                            placeholder="https://yourportfolio.com"
                                            className="w-full h-11 px-3.5 text-xs font-medium bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-mono"
                                        />
                                    </div>
                                </div>
                            </TiltCard>

                            {/* Bottom Submit Bar */}
                            <div className="flex items-center justify-end gap-3 pt-2">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="h-12 px-8 rounded-2xl bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-blue-500/25 transition-all active:scale-95 disabled:opacity-50"
                                >
                                    <Save size={16} />
                                    <span>{processing ? t('saving_profile') : t('save_profile_btn')}</span>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </StudentLayout>
    );
}
