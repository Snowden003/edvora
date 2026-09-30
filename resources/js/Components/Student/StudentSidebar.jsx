import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { useLanguage } from '@/Context/LanguageContext';
import {
    Home,
    LayoutDashboard,
    BookOpen,
    ClipboardCheck,
    Award,
    Compass,
    Trophy,
    HelpCircle,
    Bell,
    User,
    FileText,
    LogOut,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    X,
    Play,
    MessageSquare,
    Layers,
    Settings,
    GraduationCap
} from 'lucide-react';

export default function StudentSidebar({
    isOpen,
    setIsOpen,
    isCollapsed,
    setIsCollapsed
}) {
    const { url } = usePage();
    const { auth, studentNav, unreadNotificationsCount } = usePage().props;
    const { t, isRtl } = useLanguage();
    const [coursesSubmenuOpen, setCoursesSubmenuOpen] = useState(
        url.startsWith('/student/courses')
    );

    const toggleCollapse = () => {
        const next = !isCollapsed;
        setIsCollapsed(next);
        try {
            localStorage.setItem('studentSidebarCollapsed', String(next));
        } catch (e) {
            // ignore
        }
    };

    const isDashboardActive = url === '/student/dashboard' || url === '/student';
    const isCoursesActive = url.startsWith('/student/courses');
    const isQuizzesActive = url.startsWith('/student/quizzes');
    const isCertificatesActive = url.startsWith('/student/certificates');
    const isNotificationsActive = url.startsWith('/student/notifications');
    const isProfileActive = url === '/student/profile' || url === '/profile';
    const isProfileDetailsActive = url.startsWith('/student/profile-details');
    const isScoringHelpActive = url.startsWith('/scoring-help');

    const studentCourses = studentNav?.courses || [];
    const totalStudentCourses = studentNav?.totalCourses || studentCourses.length;
    const studentLevel = studentNav?.level || { level: 1, title: 'Scholar', progress: 0 };
    const unreadNotifications = unreadNotificationsCount || 0;
    const user = auth?.user;

    const userAvatar = user?.avatar_url || (user?.avatar
        ? (user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`)
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'Student')}&background=1f8fff&color=fff&size=100`);

    return (
        <aside
            className={`hidden lg:flex edvora-sidebar fixed top-0 bottom-0 z-40 h-screen transition-all duration-300 select-none ${isRtl ? 'right-0' : 'left-0'
                } ${isCollapsed
                    ? 'w-[78px] min-w-[78px] max-w-[78px] collapsed'
                    : 'w-[275px] min-w-[275px] max-w-[275px]'
                }`}
            id="sidebar"
        >
            {/* Brand Header with Integrated Collapse Toggle Button */}
            <div className={`edvora-brand-header flex items-center justify-between gap-2 p-3 mb-2 border-b border-slate-100 dark:border-slate-800/80 transition-all ${isCollapsed ? 'flex-col justify-center' : ''}`}>
                {!isCollapsed ? (
                    <>
                        <a href="/" className="edvora-brand flex items-center gap-2.5 overflow-hidden flex-1 min-w-0 !border-b-0 !p-0 !m-0">
                            <div className="edvora-brand-logo-wrap w-9 h-9 rounded-xl overflow-hidden shrink-0 flex items-center justify-center bg-slate-900 border border-slate-700/50 shadow-sm">
                                <img src="/logo.png" alt="Edvora Tech" className="w-full h-full object-cover" />
                            </div>
                            <div className="edvora-brand-info flex flex-col min-w-0">
                                <span className="edvora-brand-title text-sm font-extrabold text-slate-800 dark:text-white truncate">
                                    {t('brand_title')}
                                </span>
                                <span className="edvora-brand-badge flex items-center gap-1 text-[10px] font-bold text-cyan-600 dark:text-cyan-400">
                                    <GraduationCap size={11} className="shrink-0" />
                                    <span>{t('portal_name')}</span>
                                </span>
                            </div>
                        </a>

                        {/* Collapse Button - Clearly visible, stylish, and easily clickable */}
                        <button
                            type="button"
                            onClick={toggleCollapse}
                            title={t('close')}
                            className="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/90 dark:hover:bg-slate-700/90 text-slate-600 dark:text-slate-300 hover:text-cyan-500 dark:hover:text-cyan-400 flex items-center justify-center transition-all duration-200 shadow-xs active:scale-90 border border-slate-200/80 dark:border-cyan-500/20 shrink-0"
                            aria-label="Collapse sidebar"
                        >
                            {isRtl ? <ChevronRight size={17} /> : <ChevronLeft size={17} />}
                        </button>
                    </>
                ) : (
                    <>
                        <a href="/" className="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center bg-slate-900 border border-slate-700/50 shadow-sm hover:scale-105 transition-transform" title={t('brand_title')}>
                            <img src="/extension_icon.png" alt="Edvora Tech" className="w-8 h-8 object-contain" />
                        </a>

                        {/* Expand Button */}
                        <button
                            type="button"
                            onClick={toggleCollapse}
                            title={t('view_all')}
                            className="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/90 dark:hover:bg-slate-700/90 text-slate-600 dark:text-slate-300 hover:text-cyan-500 dark:hover:text-cyan-400 flex items-center justify-center transition-all duration-200 shadow-xs active:scale-90 border border-slate-200/80 dark:border-cyan-500/20"
                            aria-label="Expand sidebar"
                        >
                            {isRtl ? <ChevronLeft size={17} /> : <ChevronRight size={17} />}
                        </button>
                    </>
                )}
            </div>

            {/* Navigation Menu */}
            <ul className="edvora-nav">
                {/* 1. MAIN MENU */}
                <li className="edvora-nav-section-title">
                    <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shrink-0"></span>
                    <span>{t('main_menu')}</span>
                </li >

                <li className="edvora-nav-item">
                    <a href="/" className="edvora-nav-link group" title={t('home')}>
                        <span className="edvora-nav-icon text-sky-500 bg-sky-500/10 dark:bg-sky-500/15">
                            <Home size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('home')}</span>
                    </a>
                </li>

                <li className="edvora-nav-item">
                    <Link
                        href="/student/dashboard"
                        className={`edvora-nav-link group ${isDashboardActive ? 'active' : ''}`}
                        title={t('dashboard')}
                    >
                        <span className="edvora-nav-icon text-cyan-500 bg-cyan-500/10 dark:bg-cyan-500/15">
                            <LayoutDashboard size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('dashboard')}</span>
                    </Link>
                </li>

                {/* 2. ACADEMICS & LEARNING */}
                <li className="edvora-nav-section-title">
                    <span className="w-1.5 h-1.5 rounded-full bg-indigo-400 shrink-0"></span>
                    <span>{t('academics')}</span>
                </li>

                <li className={`edvora-nav-item ${coursesSubmenuOpen ? 'open' : ''}`}>
                    <button
                        type="button"
                        className={`edvora-submenu-toggle group ${isCoursesActive ? 'active' : ''}`}
                        onClick={() => setCoursesSubmenuOpen(!coursesSubmenuOpen)}
                        title={t('my_courses')}
                    >
                        <span className="edvora-nav-icon text-indigo-500 bg-indigo-500/10 dark:bg-indigo-500/15">
                            <BookOpen size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('my_courses')}</span>
                        <ChevronDown size={14} className="edvora-chevron" />
                    </button>
                    <ul className="edvora-submenu" style={{ display: coursesSubmenuOpen ? 'block' : 'none' }}>
                        <div className="edvora-submenu-inner">
                            <Link
                                href="/student/courses"
                                className={`edvora-sublink-all ${url === '/student/courses' ? 'active' : ''}`}
                            >
                                <span className="flex items-center gap-1.5">
                                    <Layers size={14} className="text-cyan-500" />
                                    {t('all_courses')}
                                </span>
                            </Link>

                            {studentCourses.map((c) => (
                                <div
                                    key={c.id}
                                    className={`edvora-course-item ${url.includes(`/student/courses/${c.slug}`) ? 'active' : ''}`}
                                >
                                    <div className="edvora-course-header">
                                        <Link href={`/student/courses/${c.slug}/learn`} className="edvora-course-title">
                                            <BookOpen size={13} className="edvora-course-icon shrink-0" />
                                            <span className="edvora-course-name">{c.title}</span>
                                        </Link>
                                    </div>
                                    <div className="edvora-course-actions">
                                        <Link href={`/student/courses/${c.slug}/learn`} className="edvora-action-btn action-view flex items-center gap-1">
                                            <Play size={10} fill="currentColor" /> {t('learn')}
                                        </Link>
                                        <a href={`/courses/${c.id}/chat`} className="edvora-action-btn action-chat flex items-center gap-1">
                                            <MessageSquare size={10} /> {t('chat')}
                                        </a>
                                    </div>
                                </div>
                            ))}

                            {totalStudentCourses > 6 && (
                                <Link href="/student/courses" className="edvora-view-all-link flex items-center gap-1">
                                    <span>{t('view_all_courses')}</span>
                                    {isRtl ? <ChevronLeft size={13} /> : <ChevronRight size={13} />}
                                </Link>
                            )}
                        </div>
                    </ul>
                </li>

                <li className="edvora-nav-item">
                    <Link
                        href="/student/quizzes"
                        className={`edvora-nav-link group ${isQuizzesActive ? 'active' : ''}`}
                        title={t('quizzes')}
                    >
                        <span className="edvora-nav-icon text-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/15">
                            <ClipboardCheck size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('quizzes')}</span>
                    </Link>
                </li>

                <li className="edvora-nav-item">
                    <Link
                        href="/student/certificates"
                        className={`edvora-nav-link group ${isCertificatesActive ? 'active' : ''}`}
                        title={t('certificates')}
                    >
                        <span className="edvora-nav-icon text-amber-500 bg-amber-500/10 dark:bg-amber-500/15">
                            <Award size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('certificates')}</span>
                    </Link>
                </li>

                <li className="edvora-nav-item">
                    <a href="/courses" className="edvora-nav-link group" title={t('explore_courses')}>
                        <span className="edvora-nav-icon text-teal-500 bg-teal-500/10 dark:bg-teal-500/15">
                            <Compass size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('explore_courses')}</span>
                    </a>
                </li>

                {/* 3. COMMUNITY & LEADERBOARD */}
                <li className="edvora-nav-section-title">
                    <span className="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                    <span>{t('community')}</span>
                </li>

                <li className="edvora-nav-item">
                    <a href="/leaderboard" className="edvora-nav-link group" title={t('leaderboard')}>
                        <span className="edvora-nav-icon text-amber-500 bg-amber-500/10 dark:bg-amber-500/15">
                            <Trophy size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('leaderboard')}</span>
                    </a>
                </li>

                <li className="edvora-nav-item">
                    <Link
                        href="/scoring-help"
                        className={`edvora-nav-link group ${isScoringHelpActive ? 'active' : ''}`}
                        title={t('how_scoring_works')}
                    >
                        <span className="edvora-nav-icon text-blue-500 bg-blue-500/10 dark:bg-blue-500/15">
                            <HelpCircle size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('how_scoring_works')}</span>
                    </Link>
                </li>

                <li className="edvora-nav-item">
                    <a
                        href="/student/notifications"
                        className={`edvora-nav-link group ${isNotificationsActive ? 'active' : ''}`}
                        title={t('notifications')}
                    >
                        <span className="edvora-nav-icon text-rose-500 bg-rose-500/10 dark:bg-rose-500/15">
                            <Bell size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('notifications')}</span>
                        {unreadNotifications > 0 && (
                            <span className="edvora-badge edvora-badge-danger ms-auto px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-extrabold shadow-sm shadow-rose-500/40">
                                {unreadNotifications}
                            </span>
                        )}
                    </a>
                </li>

                {/* 4. ACCOUNT & SETTINGS */}
                <li className="edvora-nav-section-title">
                    <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 shrink-0"></span>
                    <span>{t('account')}</span>
                </li>

                <li className="edvora-nav-item">
                    <Link
                        href="/student/profile"
                        className={`edvora-nav-link group ${isProfileActive ? 'active' : ''}`}
                        title={t('profile')}
                    >
                        <span className="edvora-nav-icon text-cyan-500 bg-cyan-500/10 dark:bg-cyan-500/15">
                            <User size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('profile')}</span>
                    </Link>
                </li>

                <li className="edvora-nav-item">
                    <Link
                        href="/student/profile-details"
                        className={`edvora-nav-link group ${isProfileDetailsActive ? 'active' : ''}`}
                        title={t('my_details')}
                    >
                        <span className="edvora-nav-icon text-purple-500 bg-purple-500/10 dark:bg-purple-500/15">
                            <FileText size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('my_details')}</span>
                    </Link>
                </li>

                <li className="edvora-nav-item">
                    <Link
                        method="post"
                        as="button"
                        href="/logout"
                        className="edvora-nav-link danger w-100 text-start border-0 bg-transparent cursor-pointer group"
                        title={t('logout')}
                    >
                        <span className="edvora-nav-icon text-rose-500 bg-rose-500/10 dark:bg-rose-500/15">
                            <LogOut size={18} strokeWidth={2.2} />
                        </span>
                        <span className="edvora-nav-text">{t('logout')}</span>
                    </Link>
                </li>
            </ul>

            {/* Pinned Bottom Student Mini Profile Card */}
            {user && (
                <div className="edvora-sidebar-footer">
                    <a href="/student/profile" className="edvora-sidebar-user-card group" title={t('profile')}>
                        <div className="edvora-user-avatar-wrap">
                            <img src={userAvatar} alt={user.name || 'Student'} className="edvora-user-avatar" />
                            <span className="edvora-user-status-dot" title={t('active')}></span>
                        </div>
                        <div className="edvora-user-details">
                            <span className="edvora-user-name">{user.name}</span>
                            <span className="edvora-user-role">
                                {t('level')} {studentLevel.level || 1} · {studentLevel.title || t('scholar')}
                            </span>
                        </div>
                        <div className="edvora-user-action-btn flex items-center justify-center">
                            <Settings size={16} />
                        </div>
                    </a>
                </div>
            )}
        </aside>
    );
}
