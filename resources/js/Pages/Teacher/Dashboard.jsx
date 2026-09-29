import React, { useState, useEffect } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import TeacherLayout from '@/Layouts/TeacherLayout';
import ScheduleClassModal from '@/Components/Teacher/ScheduleClassModal';
import { useTheme } from '@/Context/ThemeContext';
import {
    Users,
    UserCheck,
    Trophy,
    Star,
    Search,
    Filter,
    RotateCcw,
    BookOpen,
    Calendar,
    Award,
    CheckCircle2,
    Clock,
    Plus,
    Eye,
    ShieldAlert,
    ShieldCheck,
    UserX,
    User,
    Mail,
    Phone,
    MapPin,
    GraduationCap,
    X,
    ChevronRight,
    ChevronLeft,
    Sparkles,
    AlertCircle,
    Info,
    ArrowUpRight,
    ExternalLink,
    Video,
    SlidersHorizontal
} from 'lucide-react';

// Format date to authentic Persian (e.g. ۲۷ شهریور ۱۴۰۵)
function formatPersianDate(dateStr) {
    if (!dateStr) return 'نامشخص';
    try {
        const d = new Date(dateStr);
        return new Intl.DateTimeFormat('fa-IR', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(d);
    } catch (e) {
        return dateStr;
    }
}

// Convert numbers to Persian digits
function formatPersianNumber(val) {
    if (val === null || val === undefined || isNaN(val)) return '۰';
    return Number(val).toLocaleString('fa-IR');
}

export default function Dashboard({
    stats = {},
    enrolledStudents = { data: [], links: [] },
    filters = {},
    courses = [],
    todaySessions = [],
    isPending = false
}) {
    const { isDark } = useTheme();
    const page = usePage();
    const props = page?.props || {};
    const auth = props.auth || {};
    const user = auth?.user || {};

    const userAvatar = user?.avatar_url || (typeof user?.avatar === 'string' && user.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'Instructor')}&background=0A58CA&color=fff&size=200`);

    // Safe data arrays
    const safeCourses = Array.isArray(courses) ? courses : Object.values(courses || {});
    const rawStudents = enrolledStudents?.data || [];
    const initialStudents = Array.isArray(rawStudents) ? rawStudents : Object.values(rawStudents || {});

    // Filter states
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [selectedCourse, setSelectedCourse] = useState(filters?.course_id || '');
    const [selectedStatus, setSelectedStatus] = useState(filters?.status || '');

    // Modals & Drawers state
    const [scheduleModalOpen, setScheduleModalOpen] = useState(false);
    const [selectedStudentForProfile, setSelectedStudentForProfile] = useState(null);
    const [profileModalOpen, setProfileModalOpen] = useState(false);

    // XP Adjustment Modal state
    const [pointsModalOpen, setPointsModalOpen] = useState(false);
    const [pointsStudent, setPointsStudent] = useState(null);
    const [pointsAmount, setPointsAmount] = useState(5);
    const [pointsReason, setPointsReason] = useState('فعالیت کلاسی و مشارکت آموزشی');
    const [pointsSubmitting, setPointsSubmitting] = useState(false);

    // Ban / Unban Confirmation Modal state
    const [banModalOpen, setBanModalOpen] = useState(false);
    const [banTarget, setBanTarget] = useState(null); // { user, course_id, status }
    const [banSubmitting, setBanSubmitting] = useState(false);

    // Local students list to update immediately when XP changes
    const [studentsList, setStudentsList] = useState(initialStudents);

    useEffect(() => {
        const list = enrolledStudents?.data || [];
        setStudentsList(Array.isArray(list) ? list : Object.values(list || {}));
    }, [enrolledStudents]);

    // Handle filter submit
    const handleFilterSubmit = (e) => {
        if (e) e.preventDefault();
        router.get(
            '/teacher/dashboard',
            {
                search: searchTerm,
                course_id: selectedCourse,
                status: selectedStatus
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true
            }
        );
    };

    // Quick filter by status chip on mobile
    const handleQuickStatusChange = (status) => {
        setSelectedStatus(status);
        router.get(
            '/teacher/dashboard',
            {
                search: searchTerm,
                course_id: selectedCourse,
                status: status
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true
            }
        );
    };

    // Handle reset filters
    const handleResetFilters = () => {
        setSearchTerm('');
        setSelectedCourse('');
        setSelectedStatus('');
        router.get('/teacher/dashboard', {}, { preserveState: true, preserveScroll: true });
    };

    // Quick points adjustment (+5 or -5 XP)
    const handleQuickPoints = async (studentUser, courseId, delta) => {
        if (!studentUser || !studentUser.id) return;
        const csrfToken = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.content : '';
        const reason = delta > 0 ? 'ارزیابی مثبت مدرس' : 'کسر امتیاز به تشخیص مدرس';

        try {
            const res = await fetch(`/teacher/students/${studentUser.id}/points/adjust`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    amount: delta,
                    reason: reason,
                    course_id: courseId,
                })
            });

            if (res.ok) {
                const data = await res.json();
                const newTotal = data.new_total ?? ((studentUser.total_score || 0) + delta);
                // Update in local state
                setStudentsList((prev) =>
                    prev.map((item) => {
                        if (item.user && item.user.id === studentUser.id) {
                            return {
                                ...item,
                                user: {
                                    ...item.user,
                                    total_score: newTotal
                                }
                            };
                        }
                        return item;
                    })
                );
            }
        } catch (err) {
            console.error('Points adjustment error:', err);
        }
    };

    // Custom Points Modal Submit
    const handlePointsModalSubmit = async (e) => {
        e.preventDefault();
        if (!pointsStudent || !pointsAmount) return;

        setPointsSubmitting(true);
        const csrfToken = typeof document !== 'undefined' ? document.querySelector('meta[name="csrf-token"]')?.content : '';

        try {
            const res = await fetch(`/teacher/students/${pointsStudent.user_id}/points/adjust`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    amount: parseInt(pointsAmount, 10),
                    reason: pointsReason || 'تنظیم امتیاز توسط مدرس',
                    course_id: pointsStudent.course_id,
                })
            });

            if (res.ok) {
                const data = await res.json();
                const newTotal = data.new_total;
                setStudentsList((prev) =>
                    prev.map((item) => {
                        if (item.user && item.user.id === pointsStudent.user_id) {
                            return {
                                ...item,
                                user: {
                                    ...item.user,
                                    total_score: newTotal !== undefined ? newTotal : (item.user.total_score || 0) + parseInt(pointsAmount, 10)
                                }
                            };
                        }
                        return item;
                    })
                );
                setPointsModalOpen(false);
            }
        } catch (err) {
            console.error('Failed to submit points:', err);
        } finally {
            setPointsSubmitting(false);
        }
    };

    // Confirm Ban / Unban
    const handleConfirmBanToggle = () => {
        if (!banTarget) return;
        setBanSubmitting(true);

        const isCurrentlyBanned = banTarget.status === 'banned';
        const url = isCurrentlyBanned
            ? `/teacher/courses/${banTarget.course_id}/students/${banTarget.user_id}/unban`
            : `/teacher/courses/${banTarget.course_id}/students/${banTarget.user_id}/ban`;

        router.post(
            url,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    setBanModalOpen(false);
                    setBanTarget(null);
                    setBanSubmitting(false);
                },
                onError: () => {
                    setBanSubmitting(false);
                }
            }
        );
    };

    // Statistics counts from server
    const enrolledTotal = stats?.enrolledTotalCount ?? stats?.totalStudents ?? 0;
    const activeEnrolled = stats?.activeEnrolledCount ?? 0;
    const completedCoursesCount = stats?.completedCount ?? 0;
    const totalXP = stats?.totalPointsAwarded ?? 0;

    const paginationLinks = Array.isArray(enrolledStudents?.links) ? enrolledStudents.links : [];

    return (
        <TeacherLayout title="داشبورد اساتید و مدرسین - ادورا تک">
            <Head>
                <title>داشبورد اساتید و مدرسین - ادورا تک</title>
                <meta
                    name="description"
                    content="سامانه مدیریت دانشجویان، پیشرفت تحصیلی و امتیازات آموزشی ادورا تک ویژه اساتید و مدرسین."
                />
            </Head>

            {/* Application Pending Review Notice (if applicable) */}
            {isPending && (
                <div className="p-4 sm:p-5 rounded-[22px] bg-amber-50/90 dark:bg-amber-500/15 border border-amber-200/90 dark:border-amber-500/30 text-amber-900 dark:text-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs animate-in fade-in">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <Clock size={20} />
                        </div>
                        <div>
                            <h4 className="text-xs sm:text-sm font-extrabold text-amber-900 dark:text-amber-200">پروفایل شما در حال بررسی اداری است</h4>
                            <p className="text-[11px] sm:text-xs text-amber-700 dark:text-amber-300/80 mt-0.5 leading-relaxed">
                                درخواست تدریس شما ارسال شده و به زودی توسط مدیریت ادورا تک تایید خواهد شد.
                            </p>
                        </div>
                    </div>
                    <span className="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-200/80 dark:bg-amber-500/30 text-amber-900 dark:text-amber-200 shrink-0">
                        در انتظار تأیید
                    </span>
                </div>
            )}

            {/* =========================================================================
                1. MOBILE & DESKTOP HERO WELCOME BANNER (3D Futuristic Cyberpunk / Glass)
                ========================================================================= */}
            <div className="relative rounded-[24px] sm:rounded-[28px] overflow-hidden shadow-xl bg-gradient-to-br from-[#030712] via-[#09152e] to-[#040e24] dark:from-[#02050f] dark:via-[#071329] dark:to-[#030919] text-white p-5 sm:p-7 lg:p-8 border border-slate-800/80 dark:border-cyan-500/30 dark:shadow-[0_20px_50px_-15px_rgba(0,240,255,0.2)] transition-all">
                {/* Decorative Background Elements */}
                <div className="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
                    <div className="absolute top-4 left-6 text-cyan-400/15 animate-bounce hidden sm:block" style={{ animationDuration: '6s' }}>
                        <GraduationCap size={40} />
                    </div>
                    <div className="absolute bottom-6 left-1/3 text-indigo-400/15 animate-pulse hidden sm:block" style={{ animationDuration: '4s' }}>
                        <Sparkles size={42} />
                    </div>
                    <div className="absolute top-8 right-1/4 text-amber-400/15 animate-bounce hidden sm:block" style={{ animationDuration: '8s' }}>
                        <Award size={36} />
                    </div>
                    <div className="absolute top-0 right-0 w-64 h-64 bg-cyan-400/15 rounded-full blur-3xl" />
                    <div className="absolute bottom-0 left-0 w-72 h-72 bg-blue-600/25 rounded-full blur-3xl" />
                    <div className="absolute -top-12 -left-12 w-40 h-40 bg-white/5 rounded-full blur-2xl" />
                </div>

                <div className="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-5 sm:gap-6">
                    {/* Identity: 3D Holographic Instructor Avatar & Details */}
                    <div className="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-5 text-center sm:text-start flex-1 min-w-0 w-full">
                        {/* 3D Holographic Instructor Avatar */}
                        <div className="relative shrink-0 group">
                            <div className="relative w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-2xl sm:rounded-3xl p-[2px] bg-gradient-to-tr from-cyan-400 via-[#0A58CA] to-indigo-600 shadow-[0_0_25px_rgba(0,240,255,0.35)] group-hover:shadow-[0_0_35px_rgba(0,240,255,0.55)] transition-all">
                                <div className="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-slate-950 relative">
                                    <img
                                        src={userAvatar}
                                        alt={user?.name || 'Instructor'}
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                    {/* Glass sheen */}
                                    <div className="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent pointer-events-none" />
                                </div>
                                {/* Live Glow Indicator */}
                                <span className="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 ring-2 ring-slate-950 shadow-[0_0_10px_#10b981] flex items-center justify-center">
                                    <span className="w-1.5 h-1.5 rounded-full bg-white animate-ping" />
                                </span>
                            </div>
                        </div>

                        <div className="space-y-1.5 min-w-0">
                            <div className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-cyan-500/20 backdrop-blur-md border border-cyan-500/30 text-[11px] font-bold text-cyan-300">
                                <Sparkles size={12} className="text-cyan-300 animate-pulse" />
                                <span>سامانه هوشمند تدریس و آموزش ادورا</span>
                            </div>
                            <h1 className="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight leading-tight text-white drop-shadow-[0_2px_8px_rgba(0,240,255,0.2)]">
                                سلام، استاد {user?.name || 'گرامی'}
                            </h1>
                            <p className="text-xs sm:text-sm text-slate-300/90 leading-relaxed max-w-xl">
                                دانشجویان خود را مدیریت کنید، پیشرفت تحصیلی آن‌ها را ارزیابی نمایید و امتیازات آموزشی اعطا کنید.
                            </p>
                        </div>
                    </div>

                    {/* Quick Live Session Button */}
                    <div className="flex items-center gap-2 sm:gap-3 shrink-0 w-full sm:w-auto">
                        <button
                            onClick={() => setScheduleModalOpen(true)}
                            type="button"
                            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-600 hover:from-cyan-300 hover:to-indigo-500 text-white text-xs sm:text-sm font-extrabold shadow-lg shadow-cyan-500/25 border border-cyan-300/30 transition-all active:scale-95"
                        >
                            <Video size={16} className="text-white drop-shadow-[0_0_6px_#00f0ff]" />
                            <span>برنامه‌ریزی صنف زنده</span>
                        </button>
                    </div>
                </div>
            </div>

            {/* =========================================================================
                2. SUMMARY STATISTICS (Mobile 2x2 Grid / Desktop 4 Cards with Neon Accents)
                ========================================================================= */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
                {/* Card 1: Enrolled Students (Cyan Glow) */}
                <div className="p-3.5 sm:p-6 rounded-[22px] bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-cyan-950/30 border border-[#E5EAF2] dark:border-cyan-500/25 shadow-xs hover:shadow-md hover:dark:border-cyan-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(6,182,212,0.4)] transition-all duration-300 group flex flex-col justify-between">
                    <div className="flex items-center justify-between mb-2 sm:mb-4">
                        <div className="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-[#0A58CA]/10 dark:bg-cyan-500/15 text-[#0A58CA] dark:text-cyan-400 dark:border dark:border-cyan-500/30 dark:shadow-[0_0_12px_rgba(6,182,212,0.3)] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <Users size={18} className="sm:hidden drop-shadow-[0_0_8px_rgba(0,240,255,0.4)]" />
                            <Users size={24} className="hidden sm:block drop-shadow-[0_0_8px_rgba(0,240,255,0.4)]" />
                        </div>
                        <span className="hidden sm:inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#0A58CA]/10 dark:bg-cyan-500/20 text-[#0A58CA] dark:text-cyan-300 border border-transparent dark:border-cyan-500/30">
                            کل دوره‌ها
                        </span>
                    </div>
                    <div>
                        <p className="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-cyan-300/70">کل دانشجویان</p>
                        <h3 className="text-xl sm:text-3xl font-black text-[#111827] dark:text-white tracking-tight mt-0.5 sm:mt-1 font-vazir rtl-num">
                            {formatPersianNumber(enrolledTotal)}
                        </h3>
                    </div>
                </div>

                {/* Card 2: Active Learners (Emerald Glow) */}
                <div className="p-3.5 sm:p-6 rounded-[22px] bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-emerald-950/30 border border-[#E5EAF2] dark:border-emerald-500/25 shadow-xs hover:shadow-md hover:dark:border-emerald-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(16,185,129,0.4)] transition-all duration-300 group flex flex-col justify-between">
                    <div className="flex items-center justify-between mb-2 sm:mb-4">
                        <div className="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 dark:border dark:border-emerald-500/30 dark:shadow-[0_0_12px_rgba(16,185,129,0.3)] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <UserCheck size={18} className="sm:hidden drop-shadow-[0_0_8px_rgba(16,185,129,0.4)]" />
                            <UserCheck size={24} className="hidden sm:block drop-shadow-[0_0_8px_rgba(16,185,129,0.4)]" />
                        </div>
                        <span className="hidden sm:inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-500/30">
                            فعال در یادگیری
                        </span>
                    </div>
                    <div>
                        <p className="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-emerald-300/70">دانشجویان فعال</p>
                        <h3 className="text-xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight mt-0.5 sm:mt-1 font-vazir rtl-num">
                            {formatPersianNumber(activeEnrolled)}
                        </h3>
                    </div>
                </div>

                {/* Card 3: Completed Courses (Royal Blue / Sky Glow) */}
                <div className="p-3.5 sm:p-6 rounded-[22px] bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-blue-950/30 border border-[#E5EAF2] dark:border-blue-500/25 shadow-xs hover:shadow-md hover:dark:border-blue-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(37,99,235,0.4)] transition-all duration-300 group flex flex-col justify-between">
                    <div className="flex items-center justify-between mb-2 sm:mb-4">
                        <div className="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 dark:bg-blue-500/15 text-[#0A58CA] dark:text-blue-400 dark:border dark:border-blue-500/30 dark:shadow-[0_0_12px_rgba(37,99,235,0.3)] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <Trophy size={18} className="sm:hidden drop-shadow-[0_0_8px_rgba(37,99,235,0.4)]" />
                            <Trophy size={24} className="hidden sm:block drop-shadow-[0_0_8px_rgba(37,99,235,0.4)]" />
                        </div>
                        <span className="hidden sm:inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 dark:bg-blue-500/20 text-[#0A58CA] dark:text-blue-300 border border-blue-200/60 dark:border-blue-500/30">
                            تکمیل‌شده
                        </span>
                    </div>
                    <div>
                        <p className="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-blue-300/70">تکمیل دوره‌ها</p>
                        <h3 className="text-xl sm:text-3xl font-black text-[#0A58CA] dark:text-cyan-400 tracking-tight mt-0.5 sm:mt-1 font-vazir rtl-num">
                            {formatPersianNumber(completedCoursesCount)}
                        </h3>
                    </div>
                </div>

                {/* Card 4: Total XP Awarded (Amber Gold Glow) */}
                <div className="p-3.5 sm:p-6 rounded-[22px] bg-white dark:bg-gradient-to-br dark:from-slate-900/95 dark:to-amber-950/30 border border-[#E5EAF2] dark:border-amber-500/25 shadow-xs hover:shadow-md hover:dark:border-amber-400/60 hover:dark:shadow-[0_0_25px_-5px_rgba(245,158,11,0.4)] transition-all duration-300 group flex flex-col justify-between">
                    <div className="flex items-center justify-between mb-2 sm:mb-4">
                        <div className="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 dark:bg-amber-500/15 text-[#F59E0B] dark:text-amber-400 dark:border dark:border-amber-500/30 dark:shadow-[0_0_12px_rgba(245,158,11,0.3)] flex items-center justify-center group-hover:scale-105 transition-transform">
                            <Star size={18} className="sm:hidden fill-amber-500 text-amber-500 drop-shadow-[0_0_8px_rgba(245,158,11,0.5)]" />
                            <Star size={24} className="hidden sm:block fill-amber-500 text-amber-500 drop-shadow-[0_0_8px_rgba(245,158,11,0.5)]" />
                        </div>
                        <span className="hidden sm:inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-500/30">
                            سیستم نمرات
                        </span>
                    </div>
                    <div>
                        <p className="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-amber-300/70">مجموع نمرات XP</p>
                        <h3 className="text-xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight mt-0.5 sm:mt-1 font-vazir rtl-num flex items-baseline gap-1">
                            <span>{formatPersianNumber(totalXP)}</span>
                            <span className="text-xs sm:text-base font-bold text-amber-500">XP</span>
                        </h3>
                    </div>
                </div>
            </div>

            {/* =========================================================================
                3. SEARCH & SMART FILTER SECTION (Dark Glow & Chips)
                ========================================================================= */}
            <div className="p-4 sm:p-6 rounded-[24px] bg-white dark:bg-gradient-to-br dark:from-[#071328]/95 dark:to-[#040e24]/95 backdrop-blur-xl border border-[#E5EAF2] dark:border-cyan-500/25 shadow-xs dark:shadow-[0_15px_40px_-10px_rgba(0,0,0,0.5)] space-y-3.5">
                {/* Mobile Quick Status Filter Chips */}
                <div className="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none" dir="rtl">
                    <button
                        type="button"
                        onClick={() => handleQuickStatusChange('')}
                        className={`px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all shrink-0 ${
                            !selectedStatus
                                ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.3)]'
                                : 'bg-[#F5F8FC] dark:bg-[#030917]/80 text-slate-600 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-[#0c1a36] border border-[#E5EAF2] dark:border-white/10 dark:hover:border-cyan-500/30'
                        }`}
                    >
                        همه دانشجویان ({formatPersianNumber(enrolledTotal)})
                    </button>
                    <button
                        type="button"
                        onClick={() => handleQuickStatusChange('active')}
                        className={`px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all shrink-0 ${
                            selectedStatus === 'active'
                                ? 'bg-emerald-600 text-white shadow-xs dark:shadow-[0_0_15px_rgba(16,185,129,0.3)]'
                                : 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 border border-emerald-200/70 dark:border-emerald-500/30'
                        }`}
                    >
                        فعال ({formatPersianNumber(activeEnrolled)})
                    </button>
                    <button
                        type="button"
                        onClick={() => handleQuickStatusChange('completed')}
                        className={`px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all shrink-0 ${
                            selectedStatus === 'completed'
                                ? 'bg-[#1683F7] text-white shadow-xs dark:shadow-[0_0_15px_rgba(14,165,233,0.3)]'
                                : 'bg-blue-50 dark:bg-blue-500/10 text-[#0A58CA] dark:text-cyan-400 hover:bg-blue-100 dark:hover:bg-blue-500/20 border border-blue-200/70 dark:border-blue-500/30'
                        }`}
                    >
                        تکمیل‌شده ({formatPersianNumber(completedCoursesCount)})
                    </button>
                    <button
                        type="button"
                        onClick={() => handleQuickStatusChange('banned')}
                        className={`px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all shrink-0 ${
                            selectedStatus === 'banned'
                                ? 'bg-rose-600 text-white shadow-xs dark:shadow-[0_0_15px_rgba(244,63,94,0.3)]'
                                : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 border border-rose-200/70 dark:border-rose-500/30'
                        }`}
                    >
                        مسدودشده
                    </button>
                </div>

                <form onSubmit={handleFilterSubmit} className="space-y-3">
                    <div className="flex flex-col md:flex-row items-stretch md:items-center gap-2.5">
                        {/* Search Input */}
                        <div className="relative flex-1">
                            <div className="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400 dark:text-cyan-400">
                                <Search size={17} />
                            </div>
                            <input
                                type="text"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                placeholder="جستجوی نام یا ایمیل دانشجو..."
                                className="w-full pr-10 pl-3 py-2.5 bg-[#F5F8FC] dark:bg-[#030917]/90 border border-[#E5EAF2] dark:border-white/10 rounded-xl text-xs sm:text-sm text-[#111827] dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all"
                            />
                        </div>

                        {/* Courses Dropdown */}
                        <div className="w-full md:w-56">
                            <select
                                value={selectedCourse}
                                onChange={(e) => setSelectedCourse(e.target.value)}
                                className="w-full px-3.5 py-2.5 bg-[#F5F8FC] dark:bg-[#030917]/90 border border-[#E5EAF2] dark:border-white/10 rounded-xl text-xs sm:text-sm text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400 transition-all cursor-pointer"
                            >
                                <option value="" className="dark:bg-[#071328]">همه دوره‌های من</option>
                                {safeCourses.map((c) => (
                                    <option key={c.id} value={c.id} className="dark:bg-[#071328]">
                                        {c.title}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Action Buttons: Filter & Reset */}
                        <div className="flex items-center gap-2">
                            <button
                                type="submit"
                                className="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 hover:bg-[#1683F7] dark:hover:from-cyan-400 dark:hover:to-blue-500 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.25)] transition-all active:scale-95"
                            >
                                <Filter size={15} />
                                <span>اعمال فیلتر</span>
                            </button>

                            {(searchTerm || selectedCourse || selectedStatus) && (
                                <button
                                    type="button"
                                    onClick={handleResetFilters}
                                    title="پاک کردن فیلترها"
                                    className="p-2.5 bg-slate-100 dark:bg-[#0c1a36] hover:bg-slate-200 dark:hover:bg-[#12264f] text-slate-600 dark:text-slate-300 rounded-xl transition-colors border border-transparent dark:border-white/10 shrink-0"
                                    aria-label="پاک کردن فیلترها"
                                >
                                    <RotateCcw size={16} />
                                </button>
                            )}
                        </div>
                    </div>
                </form>
            </div>

            {/* =========================================================================
                4. STUDENT CARDS LIST (Mobile First Responsive Dark Cards)
                ========================================================================= */}
            <div className="space-y-4">
                <div className="flex items-center justify-between px-1">
                    <h2 className="text-sm sm:text-lg font-black text-[#111827] dark:text-white flex items-center gap-2">
                        <span>لیست دانشجویان</span>
                        <span className="px-2.5 py-0.5 rounded-full bg-[#0A58CA]/10 dark:bg-cyan-500/15 text-[#0A58CA] dark:text-cyan-400 text-[11px] font-extrabold font-vazir border border-transparent dark:border-cyan-500/30">
                            {formatPersianNumber(studentsList.length)}
                        </span>
                    </h2>
                </div>

                {studentsList.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5 sm:gap-6">
                        {studentsList.map((en) => {
                            const studentUser = en.user || {};
                            const course = en.course || {};
                            const isBanned = en.status === 'banned';
                            const progress = en.progress_percentage || 0;

                            const avatarUrl = studentUser.avatar_url || (studentUser.name
                                ? `https://ui-avatars.com/api/?name=${encodeURIComponent(studentUser.name)}&background=0A58CA&color=fff&size=120`
                                : 'https://ui-avatars.com/api/?name=Student&background=0A58CA&color=fff&size=120');

                            const enrolledDatePersian = formatPersianDate(en.created_at);

                            return (
                                <div
                                    key={en.id}
                                    className={`relative flex flex-col justify-between p-4 sm:p-6 rounded-[22px] bg-white dark:bg-gradient-to-br dark:from-[#071328]/95 dark:to-[#040e24]/90 dark:backdrop-blur-xl border transition-all duration-300 shadow-xs hover:shadow-md dark:shadow-[0_10px_30px_-5px_rgba(0,0,0,0.5)] ${
                                        isBanned
                                            ? 'border-rose-200/90 dark:border-rose-500/35 bg-rose-50/20 dark:from-rose-950/20 dark:to-[#071328]'
                                            : 'border-[#E5EAF2] dark:border-cyan-500/20 hover:border-[#0A58CA]/40 dark:hover:border-cyan-400/50 dark:hover:shadow-[0_12px_35px_-5px_rgba(0,240,255,0.18)]'
                                    }`}
                                >
                                    {/* Top Section: Student Identity & Status */}
                                    <div className="space-y-3.5">
                                        <div className="flex items-start justify-between gap-2.5">
                                            {/* Avatar + Info */}
                                            <div className="flex items-center gap-3 min-w-0">
                                                <div className="relative w-12 h-12 sm:w-13 sm:h-13 rounded-2xl overflow-hidden shrink-0 border border-[#E5EAF2] dark:border-cyan-500/30 bg-slate-100 dark:bg-slate-900 shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.15)]">
                                                    <img
                                                        src={avatarUrl}
                                                        alt={studentUser.name || 'دانشجو'}
                                                        className="w-full h-full object-cover"
                                                    />
                                                    {/* Status indicator dot */}
                                                    <span
                                                        className={`absolute bottom-0.5 left-0.5 w-3 h-3 rounded-full ring-2 ring-white dark:ring-slate-900 ${
                                                            isBanned
                                                                ? 'bg-rose-500 shadow-[0_0_8px_#f43f5e]'
                                                                : en.status === 'completed'
                                                                ? 'bg-[#1683F7] shadow-[0_0_8px_#00f0ff]'
                                                                : 'bg-emerald-500 shadow-[0_0_8px_#10b981]'
                                                        }`}
                                                    />
                                                </div>

                                                <div className="min-w-0">
                                                    <h3 className="font-extrabold text-[#111827] dark:text-white text-xs sm:text-base leading-snug truncate">
                                                        {studentUser.name || 'دانشجوی ادورا'}
                                                    </h3>
                                                    <p className="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5" dir="ltr">
                                                        {studentUser.email || 'ایمیل ثبت نشده'}
                                                    </p>
                                                    <div className="flex items-center gap-1 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">
                                                        <Calendar size={11} className="text-slate-400 dark:text-cyan-400/70 shrink-0" />
                                                        <span>عضویت {enrolledDatePersian}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Status Badge */}
                                            <div className="shrink-0">
                                                {isBanned ? (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-transparent dark:border-rose-500/30">
                                                        مسدودشده
                                                    </span>
                                                ) : en.status === 'completed' ? (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-500/20 text-[#0A58CA] dark:text-cyan-300 border border-blue-200/60 dark:border-cyan-500/30 dark:shadow-[0_0_10px_rgba(0,240,255,0.2)]">
                                                        تکمیل‌شده
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-500/30 dark:shadow-[0_0_10px_rgba(16,185,129,0.2)]">
                                                        فعال
                                                    </span>
                                                )}
                                            </div>
                                        </div>

                                        {/* Course Tag */}
                                        <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/[0.08] flex items-center justify-between gap-2">
                                            <div className="flex items-center gap-2 min-w-0">
                                                <BookOpen size={14} className="text-[#0A58CA] dark:text-cyan-400 shrink-0" />
                                                <span className="text-xs font-bold text-[#111827] dark:text-slate-200 truncate">
                                                    دوره: {course.title || 'دوره آموزشی'}
                                                </span>
                                            </div>
                                        </div>

                                        {/* Progress Bar */}
                                        <div className="space-y-1.5">
                                            <div className="flex items-center justify-between text-xs">
                                                <span className="font-bold text-slate-500 dark:text-slate-400 text-[11px]">پیشرفت دوره</span>
                                                <span className="font-black text-[#0A58CA] dark:text-cyan-400 font-vazir text-[11px] sm:text-xs">
                                                    {formatPersianNumber(progress)}٪
                                                </span>
                                            </div>
                                            <div className="w-full h-1.5 sm:h-2 rounded-full bg-slate-100 dark:bg-[#030917] border border-transparent dark:border-white/5 overflow-hidden">
                                                <div
                                                    className={`h-full rounded-full transition-all duration-500 ${
                                                        isBanned
                                                            ? 'bg-rose-500'
                                                            : progress >= 100
                                                            ? 'bg-emerald-500 dark:shadow-[0_0_8px_#10b981]'
                                                            : 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-400 dark:to-blue-500 dark:shadow-[0_0_10px_rgba(0,240,255,0.4)]'
                                                    }`}
                                                    style={{ width: `${Math.min(100, Math.max(0, progress))}%` }}
                                                />
                                            </div>
                                        </div>

                                        {/* Gamification / XP Area */}
                                        <div className="p-2.5 sm:p-3 rounded-2xl bg-amber-50/70 dark:bg-amber-500/10 border border-amber-200/70 dark:border-amber-500/25 flex items-center justify-between">
                                            <div className="flex items-center gap-2">
                                                <div className="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center shrink-0">
                                                    <Star size={14} className="fill-amber-500 text-amber-500 drop-shadow-[0_0_6px_rgba(245,158,11,0.6)]" />
                                                </div>
                                                <div>
                                                    <span className="text-xs font-black text-amber-900 dark:text-amber-300 font-vazir">
                                                        {formatPersianNumber(studentUser.total_score || 0)} XP
                                                    </span>
                                                </div>
                                            </div>

                                            {/* +XP and -XP Quick Controls */}
                                            {!isBanned && (
                                                <div className="flex items-center gap-1.5">
                                                    <button
                                                        type="button"
                                                        onClick={() => handleQuickPoints(studentUser, en.course_id, 5)}
                                                        title="اعطای ۵ امتیاز مثبت"
                                                        className="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border dark:border-emerald-500/40 hover:dark:bg-emerald-500/30 active:scale-95 text-white text-[10px] font-black shadow-xs transition-all"
                                                    >
                                                        +۵
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => handleQuickPoints(studentUser, en.course_id, -5)}
                                                        title="کسر ۵ امتیاز"
                                                        className="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 dark:bg-rose-500/20 dark:text-rose-300 dark:border dark:border-rose-500/40 hover:dark:bg-rose-500/30 active:scale-95 text-white text-[10px] font-black shadow-xs transition-all"
                                                    >
                                                        -۵
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            setPointsStudent({
                                                                user_id: studentUser.id,
                                                                user_name: studentUser.name,
                                                                course_id: en.course_id,
                                                                current_score: studentUser.total_score || 0
                                                            });
                                                            setPointsAmount(10);
                                                            setPointsModalOpen(true);
                                                        }}
                                                        title="تنظیم سفارشی امتیاز"
                                                        className="px-2.5 py-1 rounded-lg bg-amber-600 hover:bg-amber-700 dark:bg-amber-500/20 dark:text-amber-300 dark:border dark:border-amber-500/40 hover:dark:bg-amber-500/30 text-white text-[10px] font-bold transition-all"
                                                    >
                                                        سفارشی
                                                    </button>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* Bottom Section: Primary Actions + Separated Ban Action */}
                                    <div className="pt-3.5 mt-3.5 border-t border-[#E5EAF2] dark:border-white/[0.08] flex flex-col gap-2">
                                        <div className="grid grid-cols-3 gap-1.5 sm:gap-2">
                                            {/* Profile Action */}
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setSelectedStudentForProfile(en);
                                                    setProfileModalOpen(true);
                                                }}
                                                className="inline-flex items-center justify-center gap-1 py-2 px-1 rounded-xl bg-slate-100 dark:bg-[#0c1a36]/90 hover:bg-[#0A58CA]/10 dark:hover:bg-cyan-500/20 text-slate-700 dark:text-slate-200 hover:text-[#0A58CA] dark:hover:text-cyan-300 border border-transparent dark:border-white/10 dark:hover:border-cyan-500/30 text-[11px] sm:text-xs font-bold transition-all shadow-xs"
                                            >
                                                <User size={13} />
                                                <span>پروفایل</span>
                                            </button>

                                            {/* Points Action */}
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setPointsStudent({
                                                        user_id: studentUser.id,
                                                        user_name: studentUser.name,
                                                        course_id: en.course_id,
                                                        current_score: studentUser.total_score || 0
                                                    });
                                                    setPointsAmount(10);
                                                    setPointsModalOpen(true);
                                                }}
                                                className="inline-flex items-center justify-center gap-1 py-2 px-1 rounded-xl bg-slate-100 dark:bg-[#0c1a36]/90 hover:bg-amber-50 dark:hover:bg-amber-500/20 text-slate-700 dark:text-slate-200 hover:text-amber-700 dark:hover:text-amber-300 border border-transparent dark:border-white/10 dark:hover:border-amber-500/30 text-[11px] sm:text-xs font-bold transition-all shadow-xs"
                                            >
                                                <Star size={13} />
                                                <span>امتیازات</span>
                                            </button>

                                            {/* Course Detail Action */}
                                            <a
                                                href={`/teacher/courses/${en.course_id}`}
                                                className="inline-flex items-center justify-center gap-1 py-2 px-1 rounded-xl bg-slate-100 dark:bg-[#0c1a36]/90 hover:bg-[#0A58CA]/10 dark:hover:bg-cyan-500/20 text-slate-700 dark:text-slate-200 hover:text-[#0A58CA] dark:hover:text-cyan-300 border border-transparent dark:border-white/10 dark:hover:border-cyan-500/30 text-[11px] sm:text-xs font-bold transition-all shadow-xs"
                                            >
                                                <BookOpen size={13} />
                                                <span>صنف دوره</span>
                                            </a>
                                        </div>

                                        {/* Ban / Reinstate Action */}
                                        <div className="pt-0.5">
                                            {isBanned ? (
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        setBanTarget({
                                                            user_id: studentUser.id,
                                                            user_name: studentUser.name,
                                                            course_id: en.course_id,
                                                            status: 'banned'
                                                        });
                                                        setBanModalOpen(true);
                                                    }}
                                                    className="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50/70 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 hover:bg-emerald-100 dark:hover:bg-emerald-500/25 transition-all"
                                                >
                                                    <CheckCircle2 size={13} />
                                                    <span>رفع مسدودی دسترسی دانشجو</span>
                                                </button>
                                            ) : (
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        setBanTarget({
                                                            user_id: studentUser.id,
                                                            user_name: studentUser.name,
                                                            course_id: en.course_id,
                                                            status: 'active'
                                                        });
                                                        setBanModalOpen(true);
                                                    }}
                                                    className="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl text-[11px] font-semibold text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 transition-colors border border-transparent hover:border-rose-200 dark:hover:border-rose-500/30"
                                                >
                                                    <UserX size={12} />
                                                    <span>مسدود کردن دسترسی دانشجو</span>
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    /* Empty State */
                    <div className="py-12 sm:py-16 px-4 text-center bg-white dark:bg-gradient-to-br dark:from-[#071328] dark:to-[#040e24] rounded-[22px] border border-[#E5EAF2] dark:border-cyan-500/20 shadow-xs space-y-3">
                        <div className="w-14 h-14 rounded-full bg-[#0A58CA]/10 dark:bg-cyan-500/20 text-[#0A58CA] dark:text-cyan-400 border border-transparent dark:border-cyan-500/30 flex items-center justify-center mx-auto">
                            <Users size={28} />
                        </div>
                        <h3 className="text-sm sm:text-base font-extrabold text-[#111827] dark:text-white">
                            هیچ دانشجویی با این مشخصات یافت نشد
                        </h3>
                        <p className="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                            دانشجویی مطابق با فیلترهای جستجو یافت نشد یا هنوز دانشجویی در این دوره ثبت‌نام نکرده است.
                        </p>
                        {(searchTerm || selectedCourse || selectedStatus) && (
                            <button
                                onClick={handleResetFilters}
                                type="button"
                                className="inline-flex items-center gap-2 px-4 py-2 bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 hover:bg-[#1683F7] text-white text-xs font-bold rounded-xl transition-all shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.25)]"
                            >
                                <RotateCcw size={14} />
                                <span>پاک کردن فیلترها</span>
                            </button>
                        )}
                    </div>
                )}

                {/* 5. Pagination */}
                {paginationLinks.length > 3 && (
                    <div className="pt-3 flex flex-wrap items-center justify-center gap-1.5" dir="rtl">
                        {paginationLinks.map((link, idx) => {
                            if (!link.url && link.label === '...') {
                                return (
                                    <span key={idx} className="px-2.5 py-1 text-xs text-slate-400 dark:text-slate-500">
                                        ...
                                    </span>
                                );
                            }

                            const isPrevious = link.label.includes('Previous') || link.label.includes('&laquo;');
                            const isNext = link.label.includes('Next') || link.label.includes('&raquo;');

                            return (
                                <Link
                                    key={idx}
                                    href={link.url || '#'}
                                    preserveScroll
                                    preserveState
                                    className={`px-3 py-1.5 rounded-xl text-xs font-bold transition-all ${
                                        link.active
                                            ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.3)]'
                                            : !link.url
                                            ? 'text-slate-300 dark:text-slate-600 pointer-events-none'
                                            : 'bg-white dark:bg-[#071328] border border-[#E5EAF2] dark:border-cyan-500/25 text-slate-700 dark:text-slate-300 hover:text-[#0A58CA] dark:hover:text-cyan-400 hover:border-[#0A58CA]/40 dark:hover:border-cyan-400/50'
                                    }`}
                                >
                                    {isPrevious ? 'قبلی' : isNext ? 'بعدی' : link.label}
                                </Link>
                            );
                        })}
                    </div>
                )}
            </div>

            {/* =========================================================================
                MODAL 1: Points Adjustment Modal (Mobile Bottom Sheet with Dark Mode)
                ========================================================================= */}
            {pointsModalOpen && pointsStudent && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-md animate-in fade-in duration-150" dir="rtl">
                    <div className="bg-white dark:bg-[#071328] border border-[#E5EAF2] dark:border-cyan-500/30 rounded-t-[28px] sm:rounded-[24px] max-w-md w-full p-5 sm:p-7 shadow-2xl dark:shadow-[0_25px_60px_-15px_rgba(0,0,0,0.85),0_0_35px_rgba(0,240,255,0.15)] space-y-4 sm:space-y-6 max-h-[92vh] overflow-y-auto animate-in slide-in-from-bottom-5 sm:zoom-in-95">
                        <div className="flex items-center justify-between border-b border-[#E5EAF2] dark:border-white/10 pb-3.5">
                            <div className="flex items-center gap-2.5">
                                <div className="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 dark:border dark:border-amber-500/30 dark:shadow-[0_0_12px_rgba(245,158,11,0.3)] flex items-center justify-center shrink-0">
                                    <Star size={20} className="fill-amber-500 text-amber-500" />
                                </div>
                                <div className="min-w-0">
                                    <h3 className="font-black text-[#111827] dark:text-white text-sm sm:text-base truncate">
                                        تنظیم امتیازات XP دانشجو
                                    </h3>
                                    <p className="text-xs text-slate-500 dark:text-cyan-300/80 truncate">
                                        {pointsStudent.user_name}
                                    </p>
                                </div>
                            </div>
                            <button
                                onClick={() => setPointsModalOpen(false)}
                                className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#0c1a36] transition-colors"
                            >
                                <X size={18} />
                            </button>
                        </div>

                        <form onSubmit={handlePointsModalSubmit} className="space-y-4">
                            {/* Current Score Display */}
                            <div className="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-500/10 border border-amber-200/70 dark:border-amber-500/25 flex items-center justify-between">
                                <span className="text-xs font-bold text-amber-900 dark:text-amber-300">امتیاز فعلی دانشجو:</span>
                                <span className="text-sm font-black text-amber-800 dark:text-amber-400 font-vazir">
                                    {formatPersianNumber(pointsStudent.current_score)} XP
                                </span>
                            </div>

                            {/* Quick Amount Chips */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    انتخاب سریع مقدار امتیاز:
                                </label>
                                <div className="grid grid-cols-5 gap-1.5">
                                    {[5, 10, 25, -5, -10].map((amt) => (
                                        <button
                                            key={amt}
                                            type="button"
                                            onClick={() => setPointsAmount(amt)}
                                            className={`py-2 text-xs font-black rounded-xl border transition-all ${
                                                pointsAmount === amt
                                                    ? 'bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white border-[#0A58CA] dark:border-cyan-400 dark:shadow-[0_0_12px_rgba(0,240,255,0.3)]'
                                                    : amt > 0
                                                    ? 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 hover:bg-emerald-100 dark:hover:bg-emerald-500/25'
                                                    : 'bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30 hover:bg-rose-100 dark:hover:bg-rose-500/25'
                                            }`}
                                        >
                                            {amt > 0 ? `+${amt}` : amt}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Custom Amount Input */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    مقدار امتیاز دلخواه (مثبت یا منفی):
                                </label>
                                <input
                                    type="number"
                                    value={pointsAmount}
                                    onChange={(e) => setPointsAmount(e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-[#F5F8FC] dark:bg-[#030917]/90 border border-[#E5EAF2] dark:border-white/10 rounded-xl text-sm font-bold text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400"
                                />
                            </div>

                            {/* Reason Input */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    علت ثبت امتیاز:
                                </label>
                                <input
                                    type="text"
                                    value={pointsReason}
                                    onChange={(e) => setPointsReason(e.target.value)}
                                    placeholder="مثال: مشارکت فعال در صنف، انجام به موقع تکالیف..."
                                    required
                                    className="w-full px-3.5 py-2.5 bg-[#F5F8FC] dark:bg-[#030917]/90 border border-[#E5EAF2] dark:border-white/10 rounded-xl text-xs text-[#111827] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 dark:focus:ring-cyan-500/30 focus:border-[#0A58CA] dark:focus:border-cyan-400"
                                />
                            </div>

                            {/* Buttons */}
                            <div className="pt-2 flex items-center justify-end gap-2.5">
                                <button
                                    type="button"
                                    onClick={() => setPointsModalOpen(false)}
                                    className="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#0c1a36] border border-transparent dark:border-white/10 transition-colors"
                                >
                                    انصراف
                                </button>
                                <button
                                    type="submit"
                                    disabled={pointsSubmitting}
                                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 hover:bg-[#1683F7] dark:hover:from-cyan-400 dark:hover:to-blue-500 text-white text-xs font-bold rounded-xl shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.3)] transition-all disabled:opacity-50"
                                >
                                    <Star size={14} className="fill-white" />
                                    <span>{pointsSubmitting ? 'در حال ثبت...' : 'ثبت و اعمال امتیاز'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* =========================================================================
                MODAL 2: Student Full Profile Drawer (Dark Mode & Bottom Sheet)
                ========================================================================= */}
            {profileModalOpen && selectedStudentForProfile && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-md animate-in fade-in duration-150" dir="rtl">
                    <div className="bg-white dark:bg-[#071328] border border-[#E5EAF2] dark:border-cyan-500/30 rounded-t-[28px] sm:rounded-[24px] max-w-lg w-full p-5 sm:p-7 shadow-2xl dark:shadow-[0_25px_60px_-15px_rgba(0,0,0,0.85),0_0_35px_rgba(0,240,255,0.15)] space-y-4 sm:space-y-6 max-h-[92vh] overflow-y-auto animate-in slide-in-from-bottom-5 sm:zoom-in-95">
                        <div className="flex items-center justify-between border-b border-[#E5EAF2] dark:border-white/10 pb-3.5">
                            <h3 className="font-black text-[#111827] dark:text-white text-sm sm:text-base">
                                مشخصات و سوابق دانشجو
                            </h3>
                            <button
                                onClick={() => setProfileModalOpen(false)}
                                className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#0c1a36] transition-colors"
                            >
                                <X size={18} />
                            </button>
                        </div>

                        {(() => {
                            const u = selectedStudentForProfile.user || {};
                            const prof = u.student_profile || {};
                            const course = selectedStudentForProfile.course || {};

                            return (
                                <div className="space-y-4">
                                    {/* Header Info */}
                                    <div className="flex items-center gap-3.5">
                                        <div className="w-14 h-14 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-900 border border-[#E5EAF2] dark:border-cyan-500/30 shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.2)] shrink-0">
                                            <img
                                                src={u.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name || 'Student')}&background=0A58CA&color=fff&size=150`}
                                                alt={u.name || 'دانشجو'}
                                                className="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div className="min-w-0">
                                            <h4 className="font-black text-base text-[#111827] dark:text-white truncate">
                                                {u.name || 'دانشجو'}
                                            </h4>
                                            <p className="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5" dir="ltr">
                                                {u.email || 'ایمیل ثبت نشده'}
                                            </p>
                                            <div className="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 text-[10px] font-black">
                                                <Star size={11} className="fill-amber-500 text-amber-500" />
                                                <span>{formatPersianNumber(u.total_score || 0)} XP نمرات</span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Detail Fields Grid */}
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                                        <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/10">
                                            <span className="text-slate-400 dark:text-cyan-300/70 block mb-0.5 text-[11px]">دوره آموزشی:</span>
                                            <span className="font-extrabold text-[#111827] dark:text-white">{course.title || 'دوره اختصاصی'}</span>
                                        </div>
                                        <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/10">
                                            <span className="text-slate-400 dark:text-cyan-300/70 block mb-0.5 text-[11px]">تاریخ ثبت‌نام:</span>
                                            <span className="font-extrabold text-[#111827] dark:text-white">{formatPersianDate(selectedStudentForProfile.created_at)}</span>
                                        </div>
                                        <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/10">
                                            <span className="text-slate-400 dark:text-cyan-300/70 block mb-0.5 text-[11px]">مقطع تحصیلی:</span>
                                            <span className="font-extrabold text-[#111827] dark:text-white">{prof.education_level || 'ثبت نشده'}</span>
                                        </div>
                                        <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/10">
                                            <span className="text-slate-400 dark:text-cyan-300/70 block mb-0.5 text-[11px]">موقعیت سکونت:</span>
                                            <span className="font-extrabold text-[#111827] dark:text-white">
                                                {prof.province ? `${prof.province} - ${prof.district || ''}` : 'ثبت نشده'}
                                            </span>
                                        </div>
                                        {prof.phone && (
                                            <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/10 sm:col-span-2">
                                                <span className="text-slate-400 dark:text-cyan-300/70 block mb-0.5 text-[11px]">شماره تماس / واتساپ:</span>
                                                <span className="font-extrabold text-[#111827] dark:text-white" dir="ltr">{prof.phone}</span>
                                            </div>
                                        )}
                                        {prof.skills && (
                                            <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/10 sm:col-span-2">
                                                <span className="text-slate-400 dark:text-cyan-300/70 block mb-0.5 text-[11px]">مهارت‌ها و علاقه‌مندی‌ها:</span>
                                                <span className="font-medium text-slate-700 dark:text-slate-200 leading-relaxed">{prof.skills}</span>
                                            </div>
                                        )}
                                        {prof.bio && (
                                            <div className="p-2.5 sm:p-3 rounded-xl bg-[#F5F8FC] dark:bg-[#030917]/70 border border-[#E5EAF2] dark:border-white/10 sm:col-span-2">
                                                <span className="text-slate-400 dark:text-cyan-300/70 block mb-0.5 text-[11px]">درباره دانشجو:</span>
                                                <p className="font-normal text-slate-600 dark:text-slate-300 leading-relaxed">{prof.bio}</p>
                                            </div>
                                        )}
                                    </div>

                                    {/* Action Links */}
                                    <div className="pt-2 flex items-center justify-end gap-2.5 border-t border-[#E5EAF2] dark:border-white/10">
                                        <a
                                            href={`/teacher/courses/${selectedStudentForProfile.course_id}`}
                                            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A58CA] dark:bg-gradient-to-r dark:from-cyan-500 dark:to-blue-600 text-white text-xs font-bold hover:bg-[#1683F7] dark:hover:from-cyan-400 dark:hover:to-blue-500 transition-all shadow-xs dark:shadow-[0_0_15px_rgba(0,240,255,0.25)]"
                                        >
                                            <BookOpen size={14} />
                                            <span>مشاهده صنف دوره</span>
                                        </a>
                                        <button
                                            type="button"
                                            onClick={() => setProfileModalOpen(false)}
                                            className="px-4 py-2 rounded-xl bg-slate-100 dark:bg-[#0c1a36] text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-[#12264f] border border-transparent dark:border-white/10 transition-colors"
                                        >
                                            بستن
                                        </button>
                                    </div>
                                </div>
                            );
                        })()}
                    </div>
                </div>
            )}

            {/* =========================================================================
                MODAL 3: Ban / Unban Confirmation Modal (Dark Mode)
                ========================================================================= */}
            {banModalOpen && banTarget && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-md animate-in fade-in duration-150" dir="rtl">
                    <div className="bg-white dark:bg-[#071328] border border-[#E5EAF2] dark:border-cyan-500/30 rounded-t-[28px] sm:rounded-[24px] max-w-md w-full p-5 sm:p-6 shadow-2xl dark:shadow-[0_25px_60px_-15px_rgba(0,0,0,0.85),0_0_35px_rgba(0,240,255,0.15)] space-y-4 text-center animate-in slide-in-from-bottom-5 sm:zoom-in-95">
                        <div className={`w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center mx-auto ${
                            banTarget.status === 'banned' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 dark:border dark:border-emerald-500/30 dark:shadow-[0_0_15px_rgba(16,185,129,0.3)]' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 dark:border dark:border-rose-500/30 dark:shadow-[0_0_15px_rgba(244,63,94,0.3)]'
                        }`}>
                            {banTarget.status === 'banned' ? <CheckCircle2 size={26} /> : <ShieldAlert size={26} />}
                        </div>

                        <div className="space-y-1.5">
                            <h3 className="font-black text-[#111827] dark:text-white text-sm sm:text-lg">
                                {banTarget.status === 'banned' ? 'رفع مسدودی دسترسی دانشجو' : 'مسدودسازی دسترسی دانشجو'}
                            </h3>
                            <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                {banTarget.status === 'banned' ? (
                                    <>
                                        آیا مایل به رفع مسدودی و بازگردانی دسترسی <strong className="text-[#111827] dark:text-cyan-300">{banTarget.user_name}</strong> به این دوره هستید؟
                                    </>
                                ) : (
                                    <>
                                        آیا مطمئن هستید که می‌خواهید دسترسی <strong className="text-[#111827] dark:text-rose-300">{banTarget.user_name}</strong> به این دوره را مسدود کنید؟
                                    </>
                                )}
                            </p>
                        </div>

                        <div className="flex items-center justify-center gap-2.5 pt-2">
                            <button
                                type="button"
                                onClick={() => setBanModalOpen(false)}
                                className="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#0c1a36] border border-transparent dark:border-white/10 transition-colors"
                            >
                                انصراف
                            </button>
                            <button
                                type="button"
                                disabled={banSubmitting}
                                onClick={handleConfirmBanToggle}
                                className={`flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 text-white text-xs font-bold rounded-xl shadow-xs transition-all disabled:opacity-50 ${
                                    banTarget.status === 'banned'
                                        ? 'bg-emerald-600 hover:bg-emerald-700 dark:shadow-[0_0_15px_rgba(16,185,129,0.3)]'
                                        : 'bg-[#EF4444] hover:bg-rose-700 dark:shadow-[0_0_15px_rgba(244,63,94,0.3)]'
                                }`}
                            >
                                <span>{banSubmitting ? 'در حال اعمال...' : banTarget.status === 'banned' ? 'تأیید و رفع مسدودی' : 'تأیید و مسدود کردن'}</span>
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Schedule Class Modal Component */}
            {scheduleModalOpen && (
                <ScheduleClassModal
                    courses={safeCourses}
                    isOpen={scheduleModalOpen}
                    onClose={() => setScheduleModalOpen(false)}
                    onScheduled={() => {
                        setScheduleModalOpen(false);
                    }}
                />
            )}
        </TeacherLayout>
    );
}
