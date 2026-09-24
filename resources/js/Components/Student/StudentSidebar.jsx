import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { useLanguage } from '@/Context/LanguageContext';

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
        <>
            {/* Mobile Sidebar Toggle Button */}
            <button
                className="sidebar-mobile-toggle d-flex d-lg-none"
                id="sidebarMobileToggle"
                aria-label="Open sidebar"
                type="button"
                onClick={() => setIsOpen(!isOpen)}
            >
                <i className="bi bi-list"></i>
            </button>

            {/* Sidebar Overlay */}
            <div
                className={`sidebar-overlay ${isOpen ? 'active' : ''}`}
                id="sidebarOverlay"
                onClick={() => setIsOpen(false)}
            />

            {/* Unified Edvora Sidebar */}
            <aside
                className={`edvora-sidebar ${isCollapsed ? 'collapsed' : ''} ${isOpen ? 'active' : ''}`}
                id="sidebar"
            >
                {/* Desktop Collapse Toggle */}
                <div
                    className="sidebar-collapse-toggle d-none d-lg-flex"
                    id="sidebarCollapse"
                    title={isCollapsed ? t('view_all') : t('close')}
                    onClick={toggleCollapse}
                >
                    <i className={`bi ${isRtl ? (isCollapsed ? 'bi-chevron-left' : 'bi-chevron-right') : (isCollapsed ? 'bi-chevron-right' : 'bi-chevron-left')}`}></i>
                </div>

                {/* Mobile Close Button */}
                <button
                    type="button"
                    className="sidebar-mobile-close d-flex d-lg-none"
                    id="sidebarMobileClose"
                    aria-label="Close sidebar"
                    onClick={() => setIsOpen(false)}
                >
                    <i className="bi bi-x-lg"></i>
                </button>

                {/* Brand Header */}
                <a href="/" className="edvora-brand">
                    <div className="edvora-brand-logo-wrap">
                        <img src="/assets/images/logo1.jpg" alt="Edvora Tech" />
                    </div>
                    <div className="edvora-brand-info">
                        <span className="edvora-brand-title">{t('brand_title')}</span>
                        <span className="edvora-brand-badge">
                            <i className="bi bi-mortarboard-fill me-1"></i>{t('portal_name')}
                        </span>
                    </div>
                </a>

                {/* Navigation Menu */}
                <ul className="edvora-nav">
                    {/* 1. MAIN MENU */}
                    <li className="edvora-nav-section-title">{t('main_menu')}</li>

                    <li className="edvora-nav-item">
                        <a href="/" className="edvora-nav-link" title={t('home')}>
                            <span className="edvora-nav-icon"><i className="bi bi-house-door-fill"></i></span>
                            <span className="edvora-nav-text">{t('home')}</span>
                        </a>
                    </li>

                    <li className="edvora-nav-item">
                        <Link
                            href="/student/dashboard"
                            className={`edvora-nav-link ${isDashboardActive ? 'active' : ''}`}
                            title={t('dashboard')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-grid-1x2-fill"></i></span>
                            <span className="edvora-nav-text">{t('dashboard')}</span>
                        </Link>
                    </li>

                    {/* 2. ACADEMICS */}
                    <li className="edvora-nav-section-title">{t('academics')}</li>

                    <li className={`edvora-nav-item ${coursesSubmenuOpen ? 'open' : ''}`}>
                        <button
                            type="button"
                            className={`edvora-submenu-toggle ${isCoursesActive ? 'active' : ''}`}
                            onClick={() => setCoursesSubmenuOpen(!coursesSubmenuOpen)}
                            title={t('my_courses')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-journal-bookmark-fill"></i></span>
                            <span className="edvora-nav-text">{t('my_courses')}</span>
                            <i className="bi bi-chevron-down edvora-chevron"></i>
                        </button>
                        <ul className="edvora-submenu" style={{ display: coursesSubmenuOpen ? 'block' : 'none' }}>
                            <div className="edvora-submenu-inner">
                                <Link
                                    href="/student/courses"
                                    className={`edvora-sublink-all ${url === '/student/courses' ? 'active' : ''}`}
                                >
                                    <span><i className="bi bi-grid"></i> {t('all_courses')}</span>
                                </Link>

                                {studentCourses.map((c) => (
                                    <div
                                        key={c.id}
                                        className={`edvora-course-item ${url.includes(`/student/courses/${c.slug}`) ? 'active' : ''}`}
                                    >
                                        <div className="edvora-course-header">
                                            <Link href={`/student/courses/${c.slug}/learn`} className="edvora-course-title">
                                                <i className="bi bi-book edvora-course-icon"></i>
                                                <span className="edvora-course-name">{c.title}</span>
                                            </Link>
                                        </div>
                                        <div className="edvora-course-actions">
                                            <Link href={`/student/courses/${c.slug}/learn`} className="edvora-action-btn action-view">
                                                <i className="bi bi-play-circle"></i> {t('learn')}
                                            </Link>
                                            <a href={`/courses/${c.id}/chat`} className="edvora-action-btn action-chat">
                                                <i className="bi bi-chat-dots"></i> {t('chat')}
                                            </a>
                                        </div>
                                    </div>
                                ))}

                                {totalStudentCourses > 6 && (
                                    <Link href="/student/courses" className="edvora-view-all-link">
                                        {t('view_all_courses')} <i className={`bi ${isRtl ? 'bi-arrow-left-circle' : 'bi-arrow-right-circle'}`}></i>
                                    </Link>
                                )}
                            </div>
                        </ul>
                    </li>

                    <li className="edvora-nav-item">
                        <Link
                            href="/student/quizzes"
                            className={`edvora-nav-link ${isQuizzesActive ? 'active' : ''}`}
                            title={t('quizzes')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-clipboard-check-fill"></i></span>
                            <span className="edvora-nav-text">{t('quizzes')}</span>
                        </Link>
                    </li>

                    <li className="edvora-nav-item">
                        <Link
                            href="/student/certificates"
                            className={`edvora-nav-link ${isCertificatesActive ? 'active' : ''}`}
                            title={t('certificates')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-award-fill"></i></span>
                            <span className="edvora-nav-text">{t('certificates')}</span>
                        </Link>
                    </li>

                    <li className="edvora-nav-item">
                        <a href="/courses" className="edvora-nav-link" title={t('explore_courses')}>
                            <span className="edvora-nav-icon"><i className="bi bi-compass-fill"></i></span>
                            <span className="edvora-nav-text">{t('explore_courses')}</span>
                        </a>
                    </li>

                    {/* 3. COMMUNITY */}
                    <li className="edvora-nav-section-title">{t('community')}</li>

                    <li className="edvora-nav-item">
                        <a href="/leaderboard" className="edvora-nav-link" title={t('leaderboard')}>
                            <span className="edvora-nav-icon"><i className="bi bi-trophy-fill"></i></span>
                            <span className="edvora-nav-text">{t('leaderboard')}</span>
                        </a>
                    </li>

                    <li className="edvora-nav-item">
                        <Link
                            href="/scoring-help"
                            className={`edvora-nav-link ${isScoringHelpActive ? 'active' : ''}`}
                            title={t('how_scoring_works')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-question-circle-fill"></i></span>
                            <span className="edvora-nav-text">{t('how_scoring_works')}</span>
                        </Link>
                    </li>

                    <li className="edvora-nav-item">
                        <a
                            href="/student/notifications"
                            className={`edvora-nav-link ${isNotificationsActive ? 'active' : ''}`}
                            title={t('notifications')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-bell-fill"></i></span>
                            <span className="edvora-nav-text">{t('notifications')}</span>
                            {unreadNotifications > 0 && (
                                <span className="edvora-badge edvora-badge-danger ms-auto">
                                    {unreadNotifications}
                                </span>
                            )}
                        </a>
                    </li>

                    {/* 4. ACCOUNT */}
                    <li className="edvora-nav-section-title">{t('account')}</li>

                    <li className="edvora-nav-item">
                        <Link
                            href="/student/profile"
                            className={`edvora-nav-link ${isProfileActive ? 'active' : ''}`}
                            title={t('profile')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-person-circle"></i></span>
                            <span className="edvora-nav-text">{t('profile')}</span>
                        </Link>
                    </li>

                    <li className="edvora-nav-item">
                        <Link
                            href="/student/profile-details"
                            className={`edvora-nav-link ${isProfileDetailsActive ? 'active' : ''}`}
                            title={t('my_details')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-person-vcard-fill"></i></span>
                            <span className="edvora-nav-text">{t('my_details')}</span>
                        </Link>
                    </li>

                    <li className="edvora-nav-item">
                        <Link
                            method="post"
                            as="button"
                            href="/logout"
                            className="edvora-nav-link danger w-100 text-start border-0 bg-transparent cursor-pointer"
                            title={t('logout')}
                        >
                            <span className="edvora-nav-icon"><i className="bi bi-box-arrow-right"></i></span>
                            <span className="edvora-nav-text">{t('logout')}</span>
                        </Link>
                    </li>
                </ul>

                {/* Bottom Student Mini Profile Card */}
                {user && (
                    <a href="/student/profile" className="edvora-sidebar-user-card" title={t('profile')}>
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
                        <div className="edvora-user-action-btn">
                            <i className="bi bi-gear-fill"></i>
                        </div>
                    </a>
                )}
            </aside>
        </>
    );
}
