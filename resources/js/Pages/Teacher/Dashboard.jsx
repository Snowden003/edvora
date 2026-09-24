import React, { useState, useEffect } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import TeacherLayout from '@/Layouts/TeacherLayout';
import ScheduleClassModal from '@/Components/Teacher/ScheduleClassModal';
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
    ExternalLink
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
    const { auth } = usePage().props;
    const user = auth?.user || {};

    // Filter states
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [selectedCourse, setSelectedCourse] = useState(filters.course_id || '');
    const [selectedStatus, setSelectedStatus] = useState(filters.status || '');

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
    const [studentsList, setStudentsList] = useState(enrolledStudents?.data || []);

    useEffect(() => {
        setStudentsList(enrolledStudents?.data || []);
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

    // Handle reset filters
    const handleResetFilters = () => {
        setSearchTerm('');
        setSelectedCourse('');
        setSelectedStatus('');
        router.get('/teacher/dashboard', {}, { preserveState: true, preserveScroll: true });
    };

    // Quick points adjustment (+5 or -5 XP)
    const handleQuickPoints = async (studentUser, courseId, delta) => {
        if (!studentUser) return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
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
                const newTotal = data.new_total ?? (studentUser.total_score + delta);
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

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
                                    total_score: newTotal !== undefined ? newTotal : item.user.total_score + parseInt(pointsAmount, 10)
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
    const enrolledTotal = stats.enrolledTotalCount ?? stats.totalStudents ?? 0;
    const activeEnrolled = stats.activeEnrolledCount ?? 0;
    const completedCoursesCount = stats.completedCount ?? 0;
    const totalXP = stats.totalPointsAwarded ?? 0;

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
                <div className="p-4 sm:p-5 rounded-[20px] bg-amber-50/80 border border-amber-200/80 text-amber-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm animate-in fade-in">
                    <div className="flex items-center gap-3.5">
                        <div className="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-700 flex items-center justify-center shrink-0">
                            <Clock size={20} />
                        </div>
                        <div>
                            <h4 className="text-sm font-bold text-amber-900">پروفایل شما در حال بررسی اداری است</h4>
                            <p className="text-xs text-amber-700 mt-0.5 leading-relaxed">
                                درخواست تدریس شما ارسال شده و به زودی توسط مدیریت ادورا تک تایید خواهد شد.
                            </p>
                        </div>
                    </div>
                    <span className="px-3 py-1 rounded-full text-xs font-bold bg-amber-200/70 text-amber-800 shrink-0">
                        در انتظار تأیید
                    </span>
                </div>
            )}

            {/* 1. Page Header */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="space-y-1.5">
                    <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0A58CA]/10 text-[#0A58CA] text-xs font-bold">
                        <Sparkles size={13} />
                        <span>مدیریت کلاس‌ها و دانشجویان</span>
                    </div>
                    <h1 className="text-2xl sm:text-3xl font-extrabold text-[#111827] tracking-tight">
                        دانشجویان ثبت‌نام‌شده
                    </h1>
                    <p className="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-2xl">
                        دانشجویان خود را مدیریت کنید، وضعیت تحصیلی آن‌ها را مشاهده کنید و امتیازات آموزشی را مدیریت کنید.
                    </p>
                </div>

                {/* Header Action: Schedule Class */}
                <div className="flex items-center gap-3">
                    <button
                        onClick={() => setScheduleModalOpen(true)}
                        type="button"
                        className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0A58CA] hover:bg-[#1683F7] text-white text-xs sm:text-sm font-bold shadow-sm shadow-[#0A58CA]/20 transition-all active:scale-95"
                    >
                        <Plus size={16} />
                        <span>برنامه‌ریزی صنف زنده</span>
                    </button>
                </div>
            </div>

            {/* 2. Summary Statistics (4 Cards) */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {/* Card 1: Enrolled Students */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div className="flex items-center justify-between mb-4">
                        <div className="w-12 h-12 rounded-2xl bg-[#0A58CA]/10 text-[#0A58CA] flex items-center justify-center group-hover:scale-110 transition-transform">
                            <Users size={24} />
                        </div>
                        <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#0A58CA]/10 text-[#0A58CA]">
                            کل دوره‌ها
                        </span>
                    </div>
                    <p className="text-xs font-bold text-slate-400">دانشجویان ثبت‌نام‌شده</p>
                    <h3 className="text-3xl font-extrabold text-[#111827] tracking-tight mt-1.5 font-vazir">
                        {formatPersianNumber(enrolledTotal)}
                    </h3>
                </div>

                {/* Card 2: Active Learners */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div className="flex items-center justify-between mb-4">
                        <div className="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <UserCheck size={24} />
                        </div>
                        <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                            فعال در یادگیری
                        </span>
                    </div>
                    <p className="text-xs font-bold text-slate-400">دانشجویان فعال</p>
                    <h3 className="text-3xl font-extrabold text-[#111827] tracking-tight mt-1.5 font-vazir">
                        {formatPersianNumber(activeEnrolled)}
                    </h3>
                </div>

                {/* Card 3: Completed Courses */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div className="flex items-center justify-between mb-4">
                        <div className="w-12 h-12 rounded-2xl bg-[#1683F7]/10 text-[#1683F7] flex items-center justify-center group-hover:scale-110 transition-transform">
                            <Trophy size={24} />
                        </div>
                        <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-[#0A58CA] border border-blue-200/60">
                            فارغ‌التحصیل
                        </span>
                    </div>
                    <p className="text-xs font-bold text-slate-400">دوره‌های تکمیل‌شده</p>
                    <h3 className="text-3xl font-extrabold text-[#111827] tracking-tight mt-1.5 font-vazir">
                        {formatPersianNumber(completedCoursesCount)}
                    </h3>
                </div>

                {/* Card 4: Total XP Awarded */}
                <div className="p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm hover:shadow-md transition-all duration-300 group">
                    <div className="flex items-center justify-between mb-4">
                        <div className="w-12 h-12 rounded-2xl bg-[#F59E0B]/10 text-[#F59E0B] flex items-center justify-center group-hover:scale-110 transition-transform">
                            <Star size={24} />
                        </div>
                        <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                            سیستم نمرات
                        </span>
                    </div>
                    <p className="text-xs font-bold text-slate-400">مجموع XP اعطاشده</p>
                    <h3 className="text-3xl font-extrabold text-[#111827] tracking-tight mt-1.5 font-vazir">
                        {formatPersianNumber(totalXP)} <span className="text-base font-bold text-amber-500">XP</span>
                    </h3>
                </div>
            </div>

            {/* 3. Search & Filtering Section */}
            <div className="p-5 sm:p-6 rounded-[20px] bg-white border border-[#E5EAF2] shadow-sm">
                <form onSubmit={handleFilterSubmit} className="space-y-4">
                    <div className="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                        {/* Search Input */}
                        <div className="relative flex-1">
                            <div className="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                                <Search size={18} />
                            </div>
                            <input
                                type="text"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                placeholder="جستجو بر اساس نام یا ایمیل دانشجو..."
                                className="w-full pr-10 pl-4 py-2.5 bg-[#F5F8FC] border border-[#E5EAF2] rounded-xl text-xs sm:text-sm text-[#111827] placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all"
                            />
                        </div>

                        {/* Courses Dropdown */}
                        <div className="w-full md:w-56">
                            <select
                                value={selectedCourse}
                                onChange={(e) => setSelectedCourse(e.target.value)}
                                className="w-full px-3.5 py-2.5 bg-[#F5F8FC] border border-[#E5EAF2] rounded-xl text-xs sm:text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all appearance-none cursor-pointer"
                                style={{
                                    backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E")`,
                                    backgroundRepeat: 'no-repeat',
                                    backgroundPosition: 'left 0.75rem center',
                                    backgroundSize: '1rem'
                                }}
                            >
                                <option value="">همه دوره‌ها</option>
                                {courses.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.title}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Status Dropdown */}
                        <div className="w-full md:w-44">
                            <select
                                value={selectedStatus}
                                onChange={(e) => setSelectedStatus(e.target.value)}
                                className="w-full px-3.5 py-2.5 bg-[#F5F8FC] border border-[#E5EAF2] rounded-xl text-xs sm:text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#0A58CA]/20 focus:border-[#0A58CA] transition-all appearance-none cursor-pointer"
                                style={{
                                    backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E")`,
                                    backgroundRepeat: 'no-repeat',
                                    backgroundPosition: 'left 0.75rem center',
                                    backgroundSize: '1rem'
                                }}
                            >
                                <option value="">همه وضعیت‌ها</option>
                                <option value="active">فعال</option>
                                <option value="completed">تکمیل‌شده</option>
                                <option value="banned">مسدودشده</option>
                            </select>
                        </div>

                        {/* Action Buttons: Filter & Reset */}
                        <div className="flex items-center gap-2">
                            <button
                                type="submit"
                                className="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#0A58CA] hover:bg-[#1683F7] text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-colors"
                            >
                                <Filter size={15} />
                                <span>فیلتر</span>
                            </button>

                            {(searchTerm || selectedCourse || selectedStatus) && (
                                <button
                                    type="button"
                                    onClick={handleResetFilters}
                                    title="پاک کردن فیلترها"
                                    className="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors"
                                >
                                    <RotateCcw size={16} />
                                </button>
                            )}
                        </div>
                    </div>
                </form>
            </div>

            {/* 4. Student Cards Grid */}
            <div className="space-y-4">
                <div className="flex items-center justify-between px-1">
                    <h2 className="text-base sm:text-lg font-bold text-[#111827]">
                        لیست دانشجویان
                    </h2>
                    <span className="text-xs text-slate-400 font-medium">
                        تعداد نتایج: {formatPersianNumber(studentsList.length)} دانشجو
                    </span>
                </div>

                {studentsList.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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
                                    className={`relative flex flex-col justify-between p-6 rounded-[22px] bg-white border transition-all duration-300 shadow-sm hover:shadow-md ${
                                        isBanned
                                            ? 'border-rose-200/90 bg-rose-50/20'
                                            : 'border-[#E5EAF2] hover:border-[#0A58CA]/40'
                                    }`}
                                >
                                    {/* Top Section: Student Identity & Status */}
                                    <div className="space-y-4">
                                        <div className="flex items-start justify-between gap-3">
                                            {/* Avatar + Info */}
                                            <div className="flex items-center gap-3.5 min-w-0">
                                                <div className="relative w-13 h-13 rounded-2xl overflow-hidden shrink-0 border border-[#E5EAF2] bg-slate-100 shadow-xs">
                                                    <img
                                                        src={avatarUrl}
                                                        alt={studentUser.name || 'دانشجو'}
                                                        className="w-full h-full object-cover"
                                                    />
                                                    {/* Status indicator dot */}
                                                    <span
                                                        className={`absolute bottom-1 left-1 w-3 h-3 rounded-full ring-2 ring-white ${
                                                            isBanned
                                                                ? 'bg-rose-500'
                                                                : en.status === 'completed'
                                                                ? 'bg-[#1683F7]'
                                                                : 'bg-emerald-500'
                                                        }`}
                                                    />
                                                </div>

                                                <div className="min-w-0">
                                                    <h3 className="font-extrabold text-[#111827] text-sm sm:text-base leading-snug truncate">
                                                        {studentUser.name || 'دانشجوی ادورا'}
                                                    </h3>
                                                    <p className="text-xs text-slate-400 truncate mt-0.5" dir="ltr">
                                                        {studentUser.email || 'ایمیل ثبت نشده'}
                                                    </p>
                                                    <div className="flex items-center gap-1.5 text-[11px] text-slate-400 mt-1 font-medium">
                                                        <Calendar size={12} className="text-slate-400 shrink-0" />
                                                        <span>ثبت‌نام در {enrolledDatePersian}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Status Badge */}
                                            <div className="shrink-0">
                                                {isBanned ? (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700">
                                                        مسدودشده
                                                    </span>
                                                ) : en.status === 'completed' ? (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-[#0A58CA] border border-blue-200/60">
                                                        تکمیل‌شده
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                        فعال
                                                    </span>
                                                )}
                                            </div>
                                        </div>

                                        {/* Course Tag */}
                                        <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2] flex items-center justify-between gap-2">
                                            <div className="flex items-center gap-2 min-w-0">
                                                <BookOpen size={15} className="text-[#0A58CA] shrink-0" />
                                                <span className="text-xs font-bold text-[#111827] truncate">
                                                    دوره: {course.title || 'دوره تخصصی'}
                                                </span>
                                            </div>
                                        </div>

                                        {/* Progress Bar */}
                                        <div className="space-y-1.5">
                                            <div className="flex items-center justify-between text-xs">
                                                <span className="font-bold text-slate-500">پیشرفت دوره</span>
                                                <span className="font-extrabold text-[#0A58CA] font-vazir">
                                                    {formatPersianNumber(progress)}٪
                                                </span>
                                            </div>
                                            <div className="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                                <div
                                                    className={`h-full rounded-full transition-all duration-500 ${
                                                        isBanned
                                                            ? 'bg-rose-500'
                                                            : progress >= 100
                                                            ? 'bg-emerald-500'
                                                            : 'bg-[#0A58CA]'
                                                    }`}
                                                    style={{ width: `${Math.min(100, Math.max(0, progress))}%` }}
                                                />
                                            </div>
                                        </div>

                                        {/* Gamification / XP Area */}
                                        <div className="p-3 rounded-2xl bg-amber-50/60 border border-amber-200/60 flex items-center justify-between">
                                            <div className="flex items-center gap-2">
                                                <div className="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-700 flex items-center justify-center shrink-0">
                                                    <Star size={15} className="fill-amber-500 text-amber-500" />
                                                </div>
                                                <div>
                                                    <span className="text-xs font-bold text-amber-900">
                                                        ⭐ {formatPersianNumber(studentUser.total_score || 0)} XP
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
                                                        className="px-2 py-0.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-extrabold shadow-xs transition-colors"
                                                    >
                                                        +۵
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => handleQuickPoints(studentUser, en.course_id, -5)}
                                                        title="کسر ۵ امتیاز"
                                                        className="px-2 py-0.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-extrabold shadow-xs transition-colors"
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
                                                        className="px-2 py-0.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-[11px] font-bold transition-colors"
                                                    >
                                                        تنظیم
                                                    </button>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* Bottom Section: Primary Actions + Separated Ban Action */}
                                    <div className="pt-5 mt-5 border-t border-[#E5EAF2] flex flex-col gap-2.5">
                                        <div className="grid grid-cols-3 gap-2">
                                            {/* Profile Action */}
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setSelectedStudentForProfile(en);
                                                    setProfileModalOpen(true);
                                                }}
                                                className="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-slate-100 hover:bg-[#0A58CA]/10 text-slate-700 hover:text-[#0A58CA] text-xs font-bold transition-colors"
                                            >
                                                <User size={14} />
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
                                                className="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-slate-100 hover:bg-amber-50 text-slate-700 hover:text-amber-700 text-xs font-bold transition-colors"
                                            >
                                                <Star size={14} />
                                                <span>امتیازات</span>
                                            </button>

                                            {/* Course Detail Action */}
                                            <a
                                                href={`/teacher/courses/${en.course_id}`}
                                                className="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-slate-100 hover:bg-[#0A58CA]/10 text-slate-700 hover:text-[#0A58CA] text-xs font-bold transition-colors"
                                            >
                                                <BookOpen size={14} />
                                                <span>جزئیات دوره</span>
                                            </a>
                                        </div>

                                        {/* Clearly Separated Destructive / Reinstate Action */}
                                        <div className="pt-1 flex justify-end">
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
                                                    className="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-xs font-bold text-emerald-700 hover:bg-emerald-50 transition-colors border border-emerald-200"
                                                >
                                                    <CheckCircle2 size={13} />
                                                    <span>رفع مسدودی دانشجو</span>
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
                                                    className="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-xs font-semibold text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors border border-transparent hover:border-rose-200"
                                                >
                                                    <UserX size={13} />
                                                    <span>مسدود کردن</span>
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    /* Empty State in Persian RTL */
                    <div className="py-16 px-6 text-center bg-white rounded-[22px] border border-[#E5EAF2] shadow-sm space-y-3">
                        <div className="w-16 h-16 rounded-full bg-[#0A58CA]/10 text-[#0A58CA] flex items-center justify-center mx-auto">
                            <Users size={32} />
                        </div>
                        <h3 className="text-base font-bold text-[#111827]">
                            هیچ دانشجویی یافت نشد
                        </h3>
                        <p className="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                            دانشجویی مطابق با فیلترهای جستجوی شما یافت نشد یا هنوز ثبت‌نامی انجام نگرفته است.
                        </p>
                        {(searchTerm || selectedCourse || selectedStatus) && (
                            <button
                                onClick={handleResetFilters}
                                type="button"
                                className="inline-flex items-center gap-2 px-4 py-2 bg-[#0A58CA] hover:bg-[#1683F7] text-white text-xs font-bold rounded-xl transition-colors"
                            >
                                <RotateCcw size={14} />
                                <span>پاک کردن فیلترها</span>
                            </button>
                        )}
                    </div>
                )}

                {/* 5. Pagination */}
                {enrolledStudents?.links && enrolledStudents.links.length > 3 && (
                    <div className="pt-4 flex items-center justify-center gap-2" dir="rtl">
                        {enrolledStudents.links.map((link, idx) => {
                            if (!link.url && link.label === '...') {
                                return (
                                    <span key={idx} className="px-3 py-1.5 text-xs text-slate-400">
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
                                    className={`px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors ${
                                        link.active
                                            ? 'bg-[#0A58CA] text-white shadow-xs'
                                            : !link.url
                                            ? 'text-slate-300 pointer-events-none'
                                            : 'bg-white border border-[#E5EAF2] text-slate-700 hover:text-[#0A58CA] hover:border-[#0A58CA]/40'
                                    }`}
                                >
                                    {isPrevious ? 'قبلی' : isNext ? 'بعدی' : link.label}
                                </Link>
                            );
                        })}
                    </div>
                )}
            </div>

            {/* ========================================================
                MODAL 1: Points Adjustment Modal (امتیازات XP)
            ======================================================== */}
            {pointsModalOpen && pointsStudent && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-150" dir="rtl">
                    <div className="bg-white border border-[#E5EAF2] rounded-[24px] max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-6">
                        <div className="flex items-center justify-between border-b border-[#E5EAF2] pb-4">
                            <div className="flex items-center gap-2.5">
                                <div className="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-600 flex items-center justify-center">
                                    <Star size={20} className="fill-amber-500" />
                                </div>
                                <div>
                                    <h3 className="font-extrabold text-[#111827] text-base">
                                        تنظیم امتیازات XP دانشجو
                                    </h3>
                                    <p className="text-xs text-slate-400">
                                        {pointsStudent.user_name}
                                    </p>
                                </div>
                            </div>
                            <button
                                onClick={() => setPointsModalOpen(false)}
                                className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                            >
                                <X size={18} />
                            </button>
                        </div>

                        <form onSubmit={handlePointsModalSubmit} className="space-y-4">
                            {/* Current Score Display */}
                            <div className="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/70 flex items-center justify-between">
                                <span className="text-xs font-bold text-amber-900">امتیاز فعلی دانشجو:</span>
                                <span className="text-sm font-black text-amber-800 font-vazir">
                                    {formatPersianNumber(pointsStudent.current_score)} XP
                                </span>
                            </div>

                            {/* Quick Amount Chips */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                    انتخاب سریع مقدار امتیاز:
                                </label>
                                <div className="grid grid-cols-5 gap-2">
                                    {[5, 10, 25, -5, -10].map((amt) => (
                                        <button
                                            key={amt}
                                            type="button"
                                            onClick={() => setPointsAmount(amt)}
                                            className={`py-1.5 text-xs font-extrabold rounded-lg border transition-all ${
                                                pointsAmount === amt
                                                    ? 'bg-[#0A58CA] text-white border-[#0A58CA]'
                                                    : amt > 0
                                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                                                    : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100'
                                            }`}
                                        >
                                            {amt > 0 ? `+${amt}` : amt}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Custom Amount Input */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    مقدار امتیاز دلخواه (مثبت یا منفی):
                                </label>
                                <input
                                    type="number"
                                    value={pointsAmount}
                                    onChange={(e) => setPointsAmount(e.target.value)}
                                    required
                                    className="w-full px-3.5 py-2.5 bg-[#F5F8FC] border border-[#E5EAF2] rounded-xl text-sm font-bold text-[#111827] focus:outline-none focus:border-[#0A58CA]"
                                />
                            </div>

                            {/* Reason Input */}
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">
                                    علت ثبت امتیاز:
                                </label>
                                <input
                                    type="text"
                                    value={pointsReason}
                                    onChange={(e) => setPointsReason(e.target.value)}
                                    placeholder="مثال: مشارکت فعال در صنف، تحویل به موقع پروژه..."
                                    required
                                    className="w-full px-3.5 py-2.5 bg-[#F5F8FC] border border-[#E5EAF2] rounded-xl text-xs text-[#111827] focus:outline-none focus:border-[#0A58CA]"
                                />
                            </div>

                            {/* Buttons */}
                            <div className="pt-2 flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => setPointsModalOpen(false)}
                                    className="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors"
                                >
                                    انصراف
                                </button>
                                <button
                                    type="submit"
                                    disabled={pointsSubmitting}
                                    className="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0A58CA] hover:bg-[#1683F7] text-white text-xs font-bold rounded-xl shadow-xs transition-colors disabled:opacity-50"
                                >
                                    <Star size={14} />
                                    <span>{pointsSubmitting ? 'در حال ثبت...' : 'ثبت و اعمال امتیاز'}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* ========================================================
                MODAL 2: Student Full Profile Drawer / Modal (پروفایل دانشجو)
            ======================================================== */}
            {profileModalOpen && selectedStudentForProfile && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-150" dir="rtl">
                    <div className="bg-white border border-[#E5EAF2] rounded-[24px] max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between border-b border-[#E5EAF2] pb-4">
                            <h3 className="font-extrabold text-[#111827] text-base">
                                مشخصات و سوابق دانشجو
                            </h3>
                            <button
                                onClick={() => setProfileModalOpen(false)}
                                className="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                            >
                                <X size={18} />
                            </button>
                        </div>

                        {/* Student Profile Content */}
                        {(() => {
                            const u = selectedStudentForProfile.user || {};
                            const prof = u.student_profile || {};
                            const course = selectedStudentForProfile.course || {};

                            return (
                                <div className="space-y-5">
                                    {/* Header Info */}
                                    <div className="flex items-center gap-4">
                                        <div className="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 border border-[#E5EAF2] shrink-0">
                                            <img
                                                src={u.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name || 'Student')}&background=0A58CA&color=fff&size=150`}
                                                alt={u.name}
                                                className="w-full h-full object-cover"
                                            />
                                        </div>
                                        <div className="min-w-0">
                                            <h4 className="font-black text-lg text-[#111827] truncate">
                                                {u.name}
                                            </h4>
                                            <p className="text-xs text-slate-400 truncate" dir="ltr">
                                                {u.email}
                                            </p>
                                            <div className="inline-flex items-center gap-1.5 mt-1 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-extrabold">
                                                <Star size={12} className="fill-amber-500 text-amber-500" />
                                                <span>{formatPersianNumber(u.total_score || 0)} XP مجموع امتیازات</span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Detail Fields Grid */}
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                        <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2]">
                                            <span className="text-slate-400 block mb-0.5">دوره در حال یادگیری:</span>
                                            <span className="font-bold text-[#111827]">{course.title || 'دوره اختصاصی'}</span>
                                        </div>
                                        <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2]">
                                            <span className="text-slate-400 block mb-0.5">تاریخ ثبت‌نام:</span>
                                            <span className="font-bold text-[#111827]">{formatPersianDate(selectedStudentForProfile.created_at)}</span>
                                        </div>
                                        <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2]">
                                            <span className="text-slate-400 block mb-0.5">سویه تحصیلی:</span>
                                            <span className="font-bold text-[#111827]">{prof.education_level || 'ثبت نشده'}</span>
                                        </div>
                                        <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2]">
                                            <span className="text-slate-400 block mb-0.5">موقعیت سکونت:</span>
                                            <span className="font-bold text-[#111827]">
                                                {prof.province ? `${prof.province} - ${prof.district || ''}` : 'ثبت نشده'}
                                            </span>
                                        </div>
                                        {prof.phone && (
                                            <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2] sm:col-span-2">
                                                <span className="text-slate-400 block mb-0.5">شماره تماس / واتساپ:</span>
                                                <span className="font-bold text-[#111827]" dir="ltr">{prof.phone}</span>
                                            </div>
                                        )}
                                        {prof.skills && (
                                            <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2] sm:col-span-2">
                                                <span className="text-slate-400 block mb-0.5">مهارت‌ها و علاقه‌مندی‌ها:</span>
                                                <span className="font-medium text-slate-700 leading-relaxed">{prof.skills}</span>
                                            </div>
                                        )}
                                        {prof.bio && (
                                            <div className="p-3 rounded-xl bg-[#F5F8FC] border border-[#E5EAF2] sm:col-span-2">
                                                <span className="text-slate-400 block mb-0.5">درباره دانشجو:</span>
                                                <p className="font-normal text-slate-600 leading-relaxed">{prof.bio}</p>
                                            </div>
                                        )}
                                    </div>

                                    {/* Action Links */}
                                    <div className="pt-2 flex items-center justify-end gap-3 border-t border-[#E5EAF2]">
                                        <a
                                            href={`/teacher/courses/${selectedStudentForProfile.course_id}`}
                                            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0A58CA] text-white text-xs font-bold hover:bg-[#1683F7] transition-colors"
                                        >
                                            <BookOpen size={14} />
                                            <span>مشاهده صنف در دوره</span>
                                        </a>
                                        <button
                                            type="button"
                                            onClick={() => setProfileModalOpen(false)}
                                            className="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-colors"
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

            {/* ========================================================
                MODAL 3: Ban / Unban Confirmation Modal
            ======================================================== */}
            {banModalOpen && banTarget && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-150" dir="rtl">
                    <div className="bg-white border border-[#E5EAF2] rounded-[24px] max-w-md w-full p-6 shadow-2xl space-y-5 text-center">
                        <div className={`w-14 h-14 rounded-full flex items-center justify-center mx-auto ${
                            banTarget.status === 'banned' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'
                        }`}>
                            {banTarget.status === 'banned' ? <CheckCircle2 size={28} /> : <ShieldAlert size={28} />}
                        </div>

                        <div className="space-y-2">
                            <h3 className="font-extrabold text-[#111827] text-base sm:text-lg">
                                {banTarget.status === 'banned' ? 'رفع مسدودی دسترسی دانشجو' : 'مسدودسازی دسترسی دانشجو'}
                            </h3>
                            <p className="text-xs text-slate-500 leading-relaxed">
                                {banTarget.status === 'banned' ? (
                                    <>
                                        آیا مایل به رفع مسدودی و بازگردانی دسترسی <strong className="text-[#111827]">{banTarget.user_name}</strong> به این دوره هستید؟
                                    </>
                                ) : (
                                    <>
                                        آیا مطمئن هستید که می‌خواهید دسترسی <strong className="text-[#111827]">{banTarget.user_name}</strong> به این دوره را مسدود کنید؟ دانشجو دیگر به جلسات و محتوای دوره دسترسی نخواهد داشت.
                                    </>
                                )}
                            </p>
                        </div>

                        <div className="flex items-center justify-center gap-3 pt-2">
                            <button
                                type="button"
                                onClick={() => setBanModalOpen(false)}
                                className="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors"
                            >
                                انصراف
                            </button>
                            <button
                                type="button"
                                disabled={banSubmitting}
                                onClick={handleConfirmBanToggle}
                                className={`inline-flex items-center gap-2 px-5 py-2.5 text-white text-xs font-bold rounded-xl shadow-xs transition-colors disabled:opacity-50 ${
                                    banTarget.status === 'banned'
                                        ? 'bg-emerald-600 hover:bg-emerald-700'
                                        : 'bg-[#EF4444] hover:bg-rose-700'
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
                    courses={courses}
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
