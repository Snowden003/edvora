import React from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import StatCard from '@/Components/Admin/StatCard';
import RegistrationsChart from '@/Components/Admin/Charts/RegistrationsChart';
import EnrollmentsChart from '@/Components/Admin/Charts/EnrollmentsChart';
import TeacherStatusChart from '@/Components/Admin/Charts/TeacherStatusChart';
import CategoryPieChart from '@/Components/Admin/Charts/CategoryPieChart';
import SessionsLineChart from '@/Components/Admin/Charts/SessionsLineChart';
import QuickActions from '@/Components/Admin/QuickActions';
import RecentLists from '@/Components/Admin/RecentLists';
import TeamMembersSection from '@/Components/Admin/TeamMembersSection';
import {
    Users,
    GraduationCap,
    UserCheck,
    Calendar,
    Clock,
    Sparkles,
    BookOpen,
    MessageSquare,
    RefreshCw
} from 'lucide-react';
import { router } from '@inertiajs/react';

export default function Dashboard({
    stats = {},
    registrationsChart = {},
    enrollmentsChart = {},
    categoriesChart = {},
    teacherStatusChart = {},
    sessionsChart = {},
    pendingTeachers = [],
    recentCourses = [],
    recentMessages = [],
    teamMembers = [],
}) {
    // Current date formatted in Persian
    const todayPersian = new Intl.DateTimeFormat('fa-IR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date());

    const handleRefresh = () => {
        router.reload({ preserveScroll: true });
    };

    return (
        <AdminLayout title="پیشخوان مدیریت">
            {/* Header Greeting & Controls */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900/90 via-slate-900/60 to-slate-900/30 p-6 rounded-3xl border border-slate-800/80 backdrop-blur-xl relative overflow-hidden shadow-2xl">
                {/* Decorative background glow */}
                <div className="absolute top-0 right-0 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl pointer-events-none -mr-20 -mt-20" />

                <div className="relative z-10 space-y-1.5">
                    <div className="flex items-center gap-2">
                        <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" />
                        <span className="text-xs font-bold text-brand-400 uppercase tracking-wider">
                            سامانه آنلاین ادورا تک
                        </span>
                        <span className="text-xs text-slate-400">•</span>
                        <span className="text-xs text-slate-400">{todayPersian}</span>
                    </div>
                    <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight font-display">
                        پیشخوان مدیریت و کنترل پنل
                    </h1>
                    <p className="text-xs sm:text-sm text-slate-400">
                        خلاصه جامع وضعیت دانشجویان، کلاس‌ها، اساتید، و رویدادهای سامانه آموزشی
                    </p>
                </div>

                <div className="relative z-10 flex items-center gap-3">
                    <button
                        onClick={handleRefresh}
                        className="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60 transition-all hover:border-slate-600 text-xs font-bold shadow-md active:scale-95"
                    >
                        <RefreshCw size={15} className="text-brand-400" />
                        <span>بروزرسانی داده‌ها</span>
                    </button>
                    <a
                        href="/admin-panel"
                        target="_blank"
                        rel="noreferrer"
                        className="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-gradient-to-tr from-brand-600 to-accent-500 hover:from-brand-500 hover:to-accent-400 text-white text-xs font-bold shadow-glow transition-all active:scale-95"
                    >
                        <Sparkles size={15} />
                        <span>پنل فیلامنت</span>
                    </a>
                </div>
            </div>

            {/* KPI Stats Overview Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                {/* 1. Active Students */}
                <StatCard
                    title="دانشجویان فعال"
                    value={stats.active_students || 0}
                    subtitle={`از مجموع ${(stats.total_students || 0).toLocaleString('fa-IR')} کاربر`}
                    icon={Users}
                    color="brand"
                    trend="+12%"
                    href="/admin-panel/students"
                />

                {/* 2. Active Courses */}
                <StatCard
                    title="دوره‌های فعال"
                    value={stats.active_courses || 0}
                    subtitle={`از مجموع ${(stats.total_courses || 0).toLocaleString('fa-IR')} دوره`}
                    icon={GraduationCap}
                    color="emerald"
                    trend="+8%"
                    href="/admin-panel/courses"
                />

                {/* 3. Verified Teachers */}
                <StatCard
                    title="اساتید تایید شده"
                    value={stats.verified_teachers || 0}
                    subtitle={`از مجموع ${(stats.total_teachers || 0).toLocaleString('fa-IR')} مدرس`}
                    icon={UserCheck}
                    color="amber"
                    trend="+5%"
                    href="/admin-panel/teachers"
                />

                {/* 4. Active Events */}
                <StatCard
                    title="رویدادهای فعال"
                    value={stats.active_events || 0}
                    subtitle={`از مجموع ${(stats.total_events || 0).toLocaleString('fa-IR')} رویداد`}
                    icon={Calendar}
                    color="purple"
                    href="/admin/events"
                />

                {/* 5. Classes in 24h */}
                <StatCard
                    title="کلاس‌های ۲۴ ساعت"
                    value={stats.classes_24h || 0}
                    subtitle="جلسات برگزار شده امروز"
                    icon={Clock}
                    color="brand"
                />

                {/* 6. Events in 7 Days */}
                <StatCard
                    title="رویدادهای ۷ روز اخیر"
                    value={stats.events_week || 0}
                    subtitle="کارگاه‌ها و وبینارها"
                    icon={Sparkles}
                    color="emerald"
                    href="/admin/events"
                />
            </div>

            {/* Quick Actions Shortcuts */}
            <QuickActions />

            {/* Team Members & QR Code Identity System */}
            <TeamMembersSection teamMembers={teamMembers} />

            {/* Analytics & Charts Section */}
            <div className="space-y-6">
                <div className="flex items-center justify-between px-1">
                    <h2 className="text-lg font-black text-white font-display flex items-center gap-2">
                        <Sparkles size={18} className="text-brand-400" />
                        <span>تحلیل و آمار عملکرد</span>
                    </h2>
                </div>

                {/* Row 1: User Registrations & Teacher Status */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="lg:col-span-2">
                        <RegistrationsChart chartData={registrationsChart} />
                    </div>
                    <div className="lg:col-span-1">
                        <TeacherStatusChart chartData={teacherStatusChart} />
                    </div>
                </div>

                {/* Row 2: Course Enrollments & Categories Breakdown */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="lg:col-span-2">
                        <EnrollmentsChart chartData={enrollmentsChart} />
                    </div>
                    <div className="lg:col-span-1">
                        <CategoryPieChart chartData={categoriesChart} />
                    </div>
                </div>

                {/* Row 3: 30-Day Live Class Sessions */}
                <div className="grid grid-cols-1 gap-6">
                    <SessionsLineChart chartData={sessionsChart} />
                </div>
            </div>

            {/* Recent Feeds & Lists */}
            <div className="space-y-4">
                <h2 className="text-lg font-black text-white font-display flex items-center gap-2 px-1">
                    <Clock size={18} className="text-brand-400" />
                    <span>فعالیت‌های اخیر و نیازمند بررسی</span>
                </h2>

                <RecentLists
                    pendingTeachers={pendingTeachers}
                    recentCourses={recentCourses}
                    recentMessages={recentMessages}
                />
            </div>
        </AdminLayout>
    );
}
