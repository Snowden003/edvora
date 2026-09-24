import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import { useLanguage } from '@/Context/LanguageContext';
import { 
    Bell, 
    CheckCheck, 
    Trash2, 
    Sparkles, 
    Video, 
    CheckCircle2, 
    XCircle, 
    BookOpen, 
    FileText, 
    UserX, 
    UserCheck, 
    MessageSquare, 
    Calendar,
    ArrowLeft,
    ArrowRight,
    ExternalLink,
    Clock,
    Filter,
    Inbox
} from 'lucide-react';

export default function Notifications({
    notifications = { data: [], links: [] },
    unreadCount = 0
}) {
    const { t, isRtl } = useLanguage();
    const [filter, setFilter] = useState('all'); // 'all' | 'unread' | 'read'
    const [loadingAction, setLoadingAction] = useState(false);

    const items = notifications.data || [];

    // Filter items locally if needed
    const filteredItems = items.filter(item => {
        if (filter === 'unread') return !item.is_read;
        if (filter === 'read') return item.is_read;
        return true;
    });

    const handleMarkAsRead = async (id) => {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch(`/student/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            router.reload({ preserveScroll: true });
        } catch (err) {
            console.error('Failed to mark notification as read:', err);
        }
    };

    const handleMarkAllRead = async () => {
        setLoadingAction(true);
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch('/student/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            router.reload({ preserveScroll: true });
        } catch (err) {
            console.error('Failed to mark all notifications as read:', err);
        } finally {
            setLoadingAction(false);
        }
    };

    const handleDelete = async (id) => {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch(`/student/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            router.reload({ preserveScroll: true });
        } catch (err) {
            console.error('Failed to delete notification:', err);
        }
    };

    const handleDeleteAll = async () => {
        if (!confirm(t('notif_delete_confirm'))) return;
        setLoadingAction(true);
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch('/student/notifications/delete-all', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });
            router.reload({ preserveScroll: true });
        } catch (err) {
            console.error('Failed to delete all notifications:', err);
        } finally {
            setLoadingAction(false);
        }
    };

    // Helper to pick dynamic 3D-styled icons for notification types
    const getNotificationIcon = (type) => {
        switch (type) {
            case 'class_started':
                return {
                    icon: <Video className="w-5 h-5 text-emerald-400" />,
                    bg: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
                    glow: 'shadow-emerald-500/20'
                };
            case 'enrollment_approved':
                return {
                    icon: <CheckCircle2 className="w-5 h-5 text-cyan-400" />,
                    bg: 'bg-cyan-500/10 border-cyan-500/30 text-cyan-400',
                    glow: 'shadow-cyan-500/20'
                };
            case 'enrollment_rejected':
            case 'student_banned':
                return {
                    icon: <UserX className="w-5 h-5 text-rose-400" />,
                    bg: 'bg-rose-500/10 border-rose-500/30 text-rose-400',
                    glow: 'shadow-rose-500/20'
                };
            case 'student_unbanned':
                return {
                    icon: <UserCheck className="w-5 h-5 text-emerald-400" />,
                    bg: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
                    glow: 'shadow-emerald-500/20'
                };
            case 'exam_published':
                return {
                    icon: <FileText className="w-5 h-5 text-purple-400" />,
                    bg: 'bg-purple-500/10 border-purple-500/30 text-purple-400',
                    glow: 'shadow-purple-500/20'
                };
            case 'document_uploaded':
            case 'class_note_added':
                return {
                    icon: <BookOpen className="w-5 h-5 text-blue-400" />,
                    bg: 'bg-blue-500/10 border-blue-500/30 text-blue-400',
                    glow: 'shadow-blue-500/20'
                };
            case 'student_message':
            case 'admin_message':
                return {
                    icon: <MessageSquare className="w-5 h-5 text-amber-400" />,
                    bg: 'bg-amber-500/10 border-amber-500/30 text-amber-400',
                    glow: 'shadow-amber-500/20'
                };
            default:
                return {
                    icon: <Bell className="w-5 h-5 text-indigo-400" />,
                    bg: 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400',
                    glow: 'shadow-indigo-500/20'
                };
        }
    };

    return (
        <StudentLayout title={`${t('notifications')} - ${t('brand_title')}`}>
            <Head>
                <title>{`${t('notifications')} - ${t('brand_title')}`}</title>
                <meta name="description" content="View system notifications, live class alerts, and academic updates." />
            </Head>

            <div className="space-y-6 max-w-6xl mx-auto pb-12">
                {/* 3D Cyber Cosmic Hero Header */}
                <div className="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#0B1528] via-[#091E42] to-[#123970] text-white p-6 sm:p-10 border border-blue-800/40">
                    <div className="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none" />
                    <div className="absolute bottom-0 left-0 -mb-12 -ml-12 w-64 h-64 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none" />

                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="space-y-3 max-w-2xl">
                            <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-bold text-cyan-300 shadow-inner">
                                <Sparkles size={14} className="text-cyan-400 animate-pulse" />
                                <span>{t('notifications_page_hero_badge')}</span>
                            </span>
                            <h1 className="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                                {t('notifications_page_hero_title')}
                            </h1>
                            <p className="text-sm sm:text-base text-blue-100/90 leading-relaxed">
                                {t('notifications_page_hero_desc')}
                            </p>
                        </div>

                        {/* Stats pill counter */}
                        <div className="flex items-center gap-4 bg-slate-900/60 backdrop-blur-xl p-4 rounded-2xl border border-white/10 self-start md:self-auto shrink-0 shadow-lg">
                            <div className="text-center px-3 border-r border-white/10 dark:border-slate-800">
                                <span className="block text-2xl font-black text-white">{items.length}</span>
                                <span className="text-[11px] font-semibold text-slate-400">{t('notif_total')}</span>
                            </div>
                            <div className="text-center px-3">
                                <span className="block text-2xl font-black text-cyan-400">{unreadCount}</span>
                                <span className="text-[11px] font-semibold text-cyan-300">{t('notif_unread_badge')}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Filter and Bulk Action Controls */}
                <div className="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    {/* Filter Pills */}
                    <div className="flex items-center gap-2 overflow-x-auto scrollbar-none py-1">
                        <button
                            onClick={() => setFilter('all')}
                            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 ${
                                filter === 'all'
                                    ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20'
                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                            }`}
                        >
                            <Filter size={13} />
                            <span>{t('notif_filter_all')}</span>
                            <span className="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-white/20">
                                {items.length}
                            </span>
                        </button>

                        <button
                            onClick={() => setFilter('unread')}
                            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 ${
                                filter === 'unread'
                                    ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/20'
                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                            }`}
                        >
                            <Bell size={13} />
                            <span>{t('notif_filter_unread')}</span>
                            {unreadCount > 0 && (
                                <span className="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-white/20">
                                    {unreadCount}
                                </span>
                            )}
                        </button>

                        <button
                            onClick={() => setFilter('read')}
                            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 ${
                                filter === 'read'
                                    ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                            }`}
                        >
                            <CheckCheck size={13} />
                            <span>{t('notif_filter_read')}</span>
                        </button>
                    </div>

                    {/* Bulk Action Buttons */}
                    <div className="flex items-center gap-2 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 dark:border-slate-800 justify-end">
                        {unreadCount > 0 && (
                            <button
                                onClick={handleMarkAllRead}
                                disabled={loadingAction}
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-700 dark:text-cyan-400 font-bold text-xs transition-colors disabled:opacity-50"
                            >
                                <CheckCheck size={14} />
                                <span>{t('notif_mark_all_read')}</span>
                            </button>
                        )}

                        {items.length > 0 && (
                            <button
                                onClick={handleDeleteAll}
                                disabled={loadingAction}
                                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 font-bold text-xs transition-colors disabled:opacity-50"
                            >
                                <Trash2 size={14} />
                                <span>{t('notif_delete_all')}</span>
                            </button>
                        )}
                    </div>
                </div>

                {/* Notifications List */}
                {filteredItems.length > 0 ? (
                    <div className="space-y-3">
                        {filteredItems.map((n) => {
                            const style = getNotificationIcon(n.type);
                            const parsedData = typeof n.data === 'string' ? JSON.parse(n.data || '{}') : (n.data || {});

                            return (
                                <div
                                    key={n.id}
                                    className={`relative group rounded-2xl p-5 border transition-all duration-300 ${
                                        !n.is_read
                                            ? 'bg-gradient-to-r from-cyan-950/20 via-slate-900 to-slate-900/90 border-cyan-500/40 shadow-lg shadow-cyan-950/30'
                                            : 'bg-white dark:bg-slate-900/80 border-slate-200/80 dark:border-slate-800/80 hover:border-slate-300 dark:hover:border-slate-700'
                                    }`}
                                >
                                    <div className="flex items-start gap-4">
                                        {/* Icon */}
                                        <div className={`p-3 rounded-2xl border ${style.bg} ${style.glow} shadow-lg shrink-0 mt-0.5`}>
                                            {style.icon}
                                        </div>

                                        {/* Content */}
                                        <div className="flex-1 space-y-1.5 min-w-0">
                                            <div className="flex items-center justify-between gap-3">
                                                <div className="flex items-center gap-2">
                                                    <h3 className={`text-base font-extrabold ${!n.is_read ? 'text-slate-900 dark:text-white' : 'text-slate-800 dark:text-slate-200'}`}>
                                                        {n.title}
                                                    </h3>
                                                    {!n.is_read && (
                                                        <span className="px-2 py-0.5 rounded-full text-[10px] font-black bg-cyan-500 text-white uppercase tracking-wider animate-pulse">
                                                            {t('new_badge')}
                                                        </span>
                                                    )}
                                                </div>

                                                <span className="text-[11px] font-semibold text-slate-400 dark:text-slate-500 flex items-center gap-1 shrink-0">
                                                    <Clock size={12} />
                                                    <span>{n.time_ago || n.created_at}</span>
                                                </span>
                                            </div>

                                            <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                                {n.message}
                                            </p>

                                            {/* Action Bar (if applicable) */}
                                            <div className="pt-2 flex flex-wrap items-center justify-between gap-3">
                                                <div>
                                                    {n.action_url && (
                                                        <a
                                                            href={n.action_url}
                                                            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-sm transition-colors"
                                                        >
                                                            <span>{n.action_text || t('view_all')}</span>
                                                            <ExternalLink size={12} />
                                                        </a>
                                                    )}
                                                </div>

                                                <div className="flex items-center gap-2 opacity-90 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                                    {!n.is_read && (
                                                        <button
                                                            onClick={() => handleMarkAsRead(n.id)}
                                                            className="p-1.5 rounded-lg text-slate-400 hover:text-cyan-500 hover:bg-cyan-500/10 transition-colors"
                                                            title={t('notif_mark_read_btn')}
                                                        >
                                                            <CheckCheck size={16} />
                                                        </button>
                                                    )}
                                                    <button
                                                        onClick={() => handleDelete(n.id)}
                                                        className="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 transition-colors"
                                                        title={t('notif_delete_btn')}
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                ) : (
                    /* Empty State Card */
                    <div className="p-16 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm text-center space-y-4">
                        <div className="w-20 h-20 rounded-3xl bg-gradient-to-br from-cyan-500/10 to-indigo-500/10 border border-cyan-500/20 text-cyan-500 flex items-center justify-center mx-auto shadow-inner">
                            <Inbox size={40} />
                        </div>
                        <div className="space-y-1">
                            <h4 className="text-lg font-bold text-slate-900 dark:text-white">
                                {t('notif_empty_title')}
                            </h4>
                            <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                                {t('notif_empty_desc')}
                            </p>
                        </div>
                    </div>
                )}
            </div>
        </StudentLayout>
    );
}
