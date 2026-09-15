import React, { useState, useEffect, useMemo } from 'react';
import {
    Users,
    Search,
    X,
    Printer,
    Phone,
    Mail,
    MessageCircle,
    CheckCircle2,
    Clock,
    BookOpen,
    MapPin,
    User,
    Award,
    FileText,
    AlertCircle,
    Trash2,
    ExternalLink,
    RefreshCw,
    GraduationCap,
    Heart,
    Shield,
    Calendar,
    Sparkles,
    Briefcase
} from 'lucide-react';

export default function CourseStudentsModal({ course, isOpen, onClose, onCountUpdated }) {
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState(null);
    const [searchQuery, setSearchQuery] = useState('');
    const [statusFilter, setStatusFilter] = useState('all'); // 'all' | 'active' | 'completed'
    const [sortBy, setSortBy] = useState('recent'); // 'recent' | 'progress_desc' | 'progress_asc' | 'name'
    const [selectedStudent, setSelectedStudent] = useState(null);
    const [actionLoadingId, setActionLoadingId] = useState(null);
    const [feedbackMessage, setFeedbackMessage] = useState(null);

    // Fetch students data whenever modal opens or course changes
    const fetchStudents = async () => {
        if (!course?.id) return;
        setLoading(true);
        try {
            const res = await fetch(`/admin/courses/${course.id}/students`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            if (res.ok) {
                const json = await res.json();
                setData(json);
            } else {
                setFeedbackMessage({ type: 'error', text: 'خطا در دریافت اطلاعات شاگردان صنف.' });
            }
        } catch (err) {
            setFeedbackMessage({ type: 'error', text: 'خطا در برقراری ارتباط با سرور.' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (isOpen && course?.id) {
            fetchStudents();
            setSearchQuery('');
            setStatusFilter('all');
            setSelectedStudent(null);
        }
    }, [isOpen, course?.id]);

    // Handle ESC key to close
    useEffect(() => {
        const handleKeyDown = (e) => {
            if (e.key === 'Escape') {
                if (selectedStudent) {
                    setSelectedStudent(null);
                } else if (isOpen) {
                    onClose();
                }
            }
        };
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [isOpen, selectedStudent]);

    // Update status
    const handleStatusChange = async (studentUserId, newStatus) => {
        setActionLoadingId(studentUserId);
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`/admin/courses/${course.id}/students/${studentUserId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ status: newStatus }),
            });
            const result = await res.json();
            if (res.ok && result.success) {
                setFeedbackMessage({ type: 'success', text: result.message });
                fetchStudents();
            } else {
                setFeedbackMessage({ type: 'error', text: result.message || 'خطا در بروزرسانی وضعیت.' });
            }
        } catch (err) {
            setFeedbackMessage({ type: 'error', text: 'خطا در ارسال درخواست.' });
        } finally {
            setActionLoadingId(null);
        }
    };

    // Remove student from course
    const handleRemoveStudent = async (studentUserId, studentName) => {
        if (!confirm(`آیا از لغو ثبت‌نام و حذف شاگرد «${studentName}» از این صنف اطمینان دارید؟`)) {
            return;
        }

        setActionLoadingId(studentUserId);
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`/admin/courses/${course.id}/students/${studentUserId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const result = await res.json();
            if (res.ok && result.success) {
                setFeedbackMessage({ type: 'success', text: result.message });
                if (onCountUpdated) {
                    onCountUpdated(course.id, result.enrolled_count);
                }
                fetchStudents();
            } else {
                setFeedbackMessage({ type: 'error', text: result.message || 'خطا در حذف شاگرد.' });
            }
        } catch (err) {
            setFeedbackMessage({ type: 'error', text: 'خطا در ارسال درخواست.' });
        } finally {
            setActionLoadingId(null);
        }
    };

    // Filter & Sort
    const filteredStudents = useMemo(() => {
        if (!data?.students) return [];

        return data.students
            .filter((item) => {
                // Status Filter
                if (statusFilter !== 'all' && item.status !== statusFilter) {
                    return false;
                }

                // Search Query Filter
                if (!searchQuery.trim()) return true;

                const q = searchQuery.toLowerCase().trim();
                const userName = item.user.name?.toLowerCase() || '';
                const userEmail = item.user.email?.toLowerCase() || '';
                const userPhone = item.user.phone?.toLowerCase() || '';
                const fatherName = item.profile?.father_name?.toLowerCase() || '';
                const nationalId = item.profile?.national_id?.toLowerCase() || '';
                const province = item.profile?.province?.toLowerCase() || '';
                const field = item.profile?.field_of_study?.toLowerCase() || '';
                const school = item.profile?.last_school_name?.toLowerCase() || '';

                return (
                    userName.includes(q) ||
                    userEmail.includes(q) ||
                    userPhone.includes(q) ||
                    fatherName.includes(q) ||
                    nationalId.includes(q) ||
                    province.includes(q) ||
                    field.includes(q) ||
                    school.includes(q)
                );
            })
            .sort((a, b) => {
                if (sortBy === 'progress_desc') return b.progress_percentage - a.progress_percentage;
                if (sortBy === 'progress_asc') return a.progress_percentage - b.progress_percentage;
                if (sortBy === 'name') return a.user.name.localeCompare(b.user.name, 'fa');
                return 0; // Default recent
            });
    }, [data, searchQuery, statusFilter, sortBy]);

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 md:p-6 bg-slate-950/80 backdrop-blur-md animate-in fade-in duration-200">
            {/* Modal Container */}
            <div className="relative w-full max-w-6xl max-h-[92vh] flex flex-col rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden">
                
                {/* Header Section */}
                <div className="p-5 md:p-6 bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border-b border-slate-800/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="flex items-center gap-3.5">
                        <div className="w-12 h-12 rounded-2xl bg-brand-500/10 border border-brand-500/20 text-brand-400 flex items-center justify-center shrink-0 shadow-lg shadow-brand-500/5">
                            <GraduationCap size={26} />
                        </div>
                        <div>
                            <div className="flex items-center gap-2 flex-wrap">
                                <h2 className="text-lg md:text-xl font-bold text-white tracking-tight">
                                    مشخصات شاگردان صنف: {course?.title}
                                </h2>
                                <span className="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-300">
                                    {course?.category_name || 'صنف تخصصی'}
                                </span>
                            </div>
                            <p className="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                <span>مدرس دوره: <strong className="text-slate-200">{course?.teacher_name || 'تعیین نشده'}</strong></span>
                                {data?.stats && (
                                    <>
                                        <span className="text-slate-600">•</span>
                                        <span>تعداد کل شاگردان: <strong className="text-white font-bold">{data.stats.total.toLocaleString('fa-IR')}</strong></span>
                                    </>
                                )}
                            </p>
                        </div>
                    </div>

                    {/* Quick Stats in Header */}
                    <div className="flex items-center gap-2 flex-wrap">
                        {data?.stats && (
                            <div className="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-slate-800/60 border border-slate-700/60 text-xs text-slate-300">
                                <span className="flex items-center gap-1 text-emerald-400 font-bold">
                                    <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    {data.stats.active} فعال
                                </span>
                                <span className="text-slate-600">|</span>
                                <span className="text-blue-400 font-bold">
                                    {data.stats.completed} فارغ‌التحصیل
                                </span>
                                <span className="text-slate-600">|</span>
                                <span className="text-amber-400 font-bold">
                                    میانگین پیشرفت: {data.stats.average_progress}٪
                                </span>
                            </div>
                        )}

                        <button
                            onClick={() => window.print()}
                            className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700/80 transition-colors"
                            title="چاپ لیست مشخصات شاگردان"
                        >
                            <Printer size={17} />
                        </button>

                        <button
                            onClick={fetchStudents}
                            className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700/80 transition-colors"
                            title="بروزرسانی داده‌ها"
                        >
                            <RefreshCw size={17} className={loading ? 'animate-spin' : ''} />
                        </button>

                        <button
                            onClick={onClose}
                            className="p-2 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-slate-700/80 transition-colors"
                            title="بستن (Esc)"
                        >
                            <X size={18} />
                        </button>
                    </div>
                </div>

                {/* Feedback Toast */}
                {feedbackMessage && (
                    <div className={`px-4 py-2 text-xs font-semibold flex items-center justify-between transition-all ${
                        feedbackMessage.type === 'success' 
                            ? 'bg-emerald-500/20 text-emerald-300 border-b border-emerald-500/30' 
                            : 'bg-rose-500/20 text-rose-300 border-b border-rose-500/30'
                    }`}>
                        <div className="flex items-center gap-2">
                            {feedbackMessage.type === 'success' ? <CheckCircle2 size={16} /> : <AlertCircle size={16} />}
                            <span>{feedbackMessage.text}</span>
                        </div>
                        <button onClick={() => setFeedbackMessage(null)} className="opacity-70 hover:opacity-100">
                            <X size={14} />
                        </button>
                    </div>
                )}

                {/* Toolbar (Search & Filters) */}
                <div className="p-4 bg-slate-900/90 border-b border-slate-800/80 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 text-xs">
                    {/* Search Input */}
                    <div className="relative flex-1 max-w-md">
                        <Search size={15} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="جستجو بر اساس نام، نام پدر، تذکره، ایمیل، شماره تماس، ولایت..."
                            className="w-full pl-8 pr-10 py-2 rounded-xl bg-slate-850 border border-slate-755 text-white placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all text-xs"
                        />
                        {searchQuery && (
                            <button
                                onClick={() => setSearchQuery('')}
                                className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white"
                            >
                                <X size={14} />
                            </button>
                        )}
                    </div>

                    {/* Filter Tabs & Sorters */}
                    <div className="flex items-center gap-2 flex-wrap">
                        <div className="flex items-center rounded-xl bg-slate-850 p-1 border border-slate-750">
                            <button
                                onClick={() => setStatusFilter('all')}
                                className={`px-3 py-1 rounded-lg text-xs font-bold transition-all ${
                                    statusFilter === 'all'
                                        ? 'bg-brand-500 text-white shadow'
                                        : 'text-slate-400 hover:text-white'
                                }`}
                            >
                                همه ({data?.students?.length || 0})
                            </button>
                            <button
                                onClick={() => setStatusFilter('active')}
                                className={`px-3 py-1 rounded-lg text-xs font-bold transition-all ${
                                    statusFilter === 'active'
                                        ? 'bg-emerald-500 text-white shadow'
                                        : 'text-slate-400 hover:text-white'
                                }`}
                            >
                                فعال ({data?.stats?.active || 0})
                            </button>
                            <button
                                onClick={() => setStatusFilter('completed')}
                                className={`px-3 py-1 rounded-lg text-xs font-bold transition-all ${
                                    statusFilter === 'completed'
                                        ? 'bg-blue-500 text-white shadow'
                                        : 'text-slate-400 hover:text-white'
                                }`}
                            >
                                فارغ‌التحصیل ({data?.stats?.completed || 0})
                            </button>
                        </div>

                        <select
                            value={sortBy}
                            onChange={(e) => setSortBy(e.target.value)}
                            className="py-1.5 px-3 rounded-xl bg-slate-850 border border-slate-750 text-slate-300 text-xs focus:outline-none focus:border-brand-500"
                        >
                            <option value="recent">مرتب‌سازی: جدیدترین ثبت‌نام</option>
                            <option value="progress_desc">بیشترین پیشرفت درس</option>
                            <option value="progress_asc">کمترین پیشرفت درس</option>
                            <option value="name">بر اساس نام و تخلص</option>
                        </select>
                    </div>
                </div>

                {/* Content Area (Scrollable Students List) */}
                <div className="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
                    {loading ? (
                        <div className="py-20 text-center space-y-3">
                            <RefreshCw size={32} className="mx-auto text-brand-400 animate-spin" />
                            <p className="text-xs text-slate-400 font-medium">در حال بارگذاری مشخصات شاگردان صنف...</p>
                        </div>
                    ) : filteredStudents.length === 0 ? (
                        <div className="py-16 text-center space-y-3 rounded-2xl bg-slate-850/40 border border-slate-800">
                            <Users size={44} className="mx-auto text-slate-600" />
                            <h4 className="text-base font-bold text-white">هیچ شاگردی یافت نشد</h4>
                            <p className="text-xs text-slate-400 max-w-md mx-auto">
                                {searchQuery || statusFilter !== 'all'
                                    ? 'هیچ شاگردی با این معیار جستجو یا فیلتر پیدا نشد.'
                                    : 'هنوز شاگردی در این دوره آموزشی ثبت‌نام نکرده است.'}
                            </p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-right text-xs">
                                <thead className="bg-slate-950/60 text-slate-400 border-b border-slate-800 text-[11px] uppercase font-bold tracking-wider">
                                    <tr>
                                        <th className="p-3.5">مشخصات شاگرد</th>
                                        <th className="p-3.5">نام پدر / تذکره</th>
                                        <th className="p-3.5">اطلاعات تماس</th>
                                        <th className="p-3.5">سکونت و تحصیلات</th>
                                        <th className="p-3.5 text-center">پیشرفت در صنف</th>
                                        <th className="p-3.5 text-center">وضعیت</th>
                                        <th className="p-3.5 text-center">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-800/60 text-slate-300">
                                    {filteredStudents.map((item) => {
                                        const profile = item.profile;
                                        const user = item.user;
                                        const isActionLoading = actionLoadingId === user.id;

                                        return (
                                            <tr
                                                key={item.enrollment_id}
                                                className="hover:bg-slate-800/40 transition-colors group"
                                            >
                                                {/* Student Identity */}
                                                <td className="p-3.5">
                                                    <div className="flex items-center gap-3 min-w-[200px]">
                                                        <div className="relative w-11 h-11 rounded-xl bg-slate-800 border border-slate-700/80 overflow-hidden shrink-0 flex items-center justify-center">
                                                            {user.avatar || profile?.profile_photo ? (
                                                                <img
                                                                    src={profile?.profile_photo || user.avatar}
                                                                    alt={user.name}
                                                                    className="w-full h-full object-cover"
                                                                />
                                                            ) : (
                                                                <User size={18} className="text-slate-400" />
                                                            )}
                                                            <span
                                                                className={`absolute bottom-0.5 right-0.5 w-2.5 h-2.5 rounded-full border-2 border-slate-900 ${
                                                                    item.status === 'completed'
                                                                        ? 'bg-blue-400'
                                                                        : item.status === 'active'
                                                                        ? 'bg-emerald-400'
                                                                        : 'bg-rose-400'
                                                                }`}
                                                            />
                                                        </div>
                                                        <div className="min-w-0">
                                                            <div className="font-bold text-white group-hover:text-brand-400 transition-colors truncate">
                                                                {user.name}
                                                            </div>
                                                            <div className="text-[11px] text-slate-400 truncate mt-0.5" dir="ltr">
                                                                {user.email}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Father Name & National ID */}
                                                <td className="p-3.5 whitespace-nowrap">
                                                    <div className="space-y-0.5">
                                                        <div className="font-semibold text-slate-200">
                                                            {profile?.father_name ? `ولد: ${profile.father_name}` : '—'}
                                                        </div>
                                                        <div className="text-[11px] text-slate-400 font-mono" dir="ltr">
                                                            {profile?.national_id ? `تذکره: ${profile.national_id}` : 'بدون تذکره'}
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Contact Details */}
                                                <td className="p-3.5 whitespace-nowrap">
                                                    <div className="space-y-1">
                                                        <div className="flex items-center gap-1.5 text-slate-300">
                                                            <Phone size={12} className="text-brand-400" />
                                                            <span dir="ltr" className="font-mono text-[11px]">
                                                                {user.phone || profile?.phone_number || '—'}
                                                            </span>
                                                        </div>
                                                        {(profile?.whatsapp_number || user.phone) && (
                                                            <a
                                                                href={`https://wa.me/${(profile?.whatsapp_number || user.phone).replace(/[^0-9]/g, '')}`}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="inline-flex items-center gap-1 text-[10px] text-emerald-400 hover:text-emerald-300 transition-colors"
                                                            >
                                                                <MessageCircle size={11} />
                                                                <span>واتساپ</span>
                                                            </a>
                                                        )}
                                                    </div>
                                                </td>

                                                {/* Location & Education */}
                                                <td className="p-3.5">
                                                    <div className="space-y-0.5 min-w-[150px]">
                                                        <div className="flex items-center gap-1 text-slate-300 font-medium">
                                                            <MapPin size={12} className="text-slate-500 shrink-0" />
                                                            <span className="truncate">{profile?.province ? `${profile.province} ${profile.district ? `(${profile.district})` : ''}` : 'ولایت نامشخص'}</span>
                                                        </div>
                                                        <div className="text-[11px] text-slate-400 truncate">
                                                            {profile?.last_education_level || profile?.field_of_study || 'تحصیلات ثبت نشده'}
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Progress Bar in Course */}
                                                <td className="p-3.5 text-center whitespace-nowrap">
                                                    <div className="w-28 mx-auto space-y-1">
                                                        <div className="flex items-center justify-between text-[11px] font-bold">
                                                            <span className="text-white">{item.progress_percentage}٪</span>
                                                            <span className="text-[10px] text-slate-400">پیشرفت</span>
                                                        </div>
                                                        <div className="w-full h-1.5 rounded-full bg-slate-800 overflow-hidden">
                                                            <div
                                                                className={`h-full rounded-full transition-all duration-500 ${
                                                                    item.progress_percentage >= 100
                                                                        ? 'bg-emerald-400'
                                                                        : item.progress_percentage >= 50
                                                                        ? 'bg-brand-500'
                                                                        : 'bg-amber-500'
                                                                }`}
                                                                style={{ width: `${item.progress_percentage}%` }}
                                                            />
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Status Badge */}
                                                <td className="p-3.5 text-center whitespace-nowrap">
                                                    <span
                                                        className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${
                                                            item.status === 'completed'
                                                                ? 'bg-blue-500/10 text-blue-300 border-blue-500/30'
                                                                : item.status === 'active'
                                                                ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'
                                                                : 'bg-rose-500/10 text-rose-300 border-rose-500/30'
                                                        }`}
                                                    >
                                                        {item.status === 'completed' ? 'تکمیل شده' : item.status === 'active' ? 'مشغول به تحصیل' : 'انصراف / رد'}
                                                    </span>
                                                </td>

                                                {/* Actions */}
                                                <td className="p-3.5 text-center whitespace-nowrap">
                                                    <div className="flex items-center justify-center gap-1.5">
                                                        {/* View Full Dossier */}
                                                        <button
                                                            onClick={() => setSelectedStudent(item)}
                                                            className="px-2.5 py-1.5 rounded-xl bg-brand-500/15 hover:bg-brand-500/25 text-brand-300 border border-brand-500/30 text-xs font-bold transition-all flex items-center gap-1 shadow-sm"
                                                            title="مشاهده شناسنامه و پرونده کامل شاگرد"
                                                        >
                                                            <FileText size={13} />
                                                            <span>پرونده کامل</span>
                                                        </button>

                                                        {/* Toggle status */}
                                                        <select
                                                            disabled={isActionLoading}
                                                            value={item.status}
                                                            onChange={(e) => handleStatusChange(user.id, e.target.value)}
                                                            className="p-1 rounded-lg bg-slate-800 border border-slate-700 text-[11px] text-slate-300 hover:text-white focus:outline-none cursor-pointer"
                                                            title="تغییر وضعیت شمولیت در صنف"
                                                        >
                                                            <option value="active">فعال</option>
                                                            <option value="completed">فارغ‌التحصیل</option>
                                                            <option value="dropped">انصراف</option>
                                                        </select>

                                                        {/* Remove from course */}
                                                        <button
                                                            disabled={isActionLoading}
                                                            onClick={() => handleRemoveStudent(user.id, user.name)}
                                                            className="p-1.5 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition-colors"
                                                            title="حذف شاگرد از این صنف"
                                                        >
                                                            <Trash2 size={14} />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

                {/* Footer Section */}
                <div className="p-4 bg-slate-950/80 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                    <div>
                        نمایش <span className="font-bold text-white">{filteredStudents.length}</span> از{' '}
                        <span className="font-bold text-white">{data?.stats?.total || 0}</span> شاگرد ثبت‌نام‌شده
                    </div>
                    <div className="flex items-center gap-2">
                        <button
                            onClick={onClose}
                            className="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition-colors"
                        >
                            بستن پنجره
                        </button>
                    </div>
                </div>
            </div>

            {/* Complete Student Dossier Modal ("شناسنامه و پرونده جامع شاگرد") */}
            {selectedStudent && (
                <div className="fixed inset-0 z-60 flex items-center justify-center p-3 md:p-6 bg-slate-950/90 backdrop-blur-lg animate-in zoom-in-95 duration-150">
                    <div className="relative w-full max-w-3xl max-h-[90vh] flex flex-col rounded-3xl bg-slate-900 border border-slate-750 shadow-2xl overflow-hidden">
                        {/* Dossier Header */}
                        <div className="p-5 md:p-6 bg-gradient-to-r from-brand-900/40 via-slate-900 to-indigo-950/30 border-b border-slate-800 flex items-center justify-between">
                            <div className="flex items-center gap-3.5">
                                <div className="w-14 h-14 rounded-2xl bg-brand-500/20 border border-brand-500/30 overflow-hidden shrink-0 flex items-center justify-center">
                                    {selectedStudent.profile?.profile_photo || selectedStudent.user.avatar ? (
                                        <img
                                            src={selectedStudent.profile?.profile_photo || selectedStudent.user.avatar}
                                            alt={selectedStudent.user.name}
                                            className="w-full h-full object-cover"
                                        />
                                    ) : (
                                        <User size={26} className="text-brand-400" />
                                    )}
                                </div>
                                <div>
                                    <div className="flex items-center gap-2">
                                        <h3 className="text-lg font-bold text-white">{selectedStudent.user.name}</h3>
                                        <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                                            شاگرد صنف
                                        </span>
                                    </div>
                                    <p className="text-xs text-slate-400 mt-0.5" dir="ltr">
                                        {selectedStudent.user.email}
                                    </p>
                                </div>
                            </div>
                            <button
                                onClick={() => setSelectedStudent(null)}
                                className="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white border border-slate-700"
                            >
                                <X size={18} />
                            </button>
                        </div>

                        {/* Dossier Body (Categorized Specifications) */}
                        <div className="flex-1 overflow-y-auto p-5 md:p-6 space-y-6 text-xs custom-scrollbar">
                            
                            {/* Section 1: مشخصات فردی و هویتی */}
                            <div className="space-y-3">
                                <h4 className="font-bold text-sm text-brand-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                                    <User size={16} />
                                    <span>مشخصات فردی و شناسنامه‌ای</span>
                                </h4>
                                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">نام پدر (ولد)</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.father_name || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">نام مادر</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.mother_name || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">شماره تذکره (شناسنامه)</span>
                                        <span className="font-bold text-white mt-1 block font-mono" dir="ltr">
                                            {selectedStudent.profile?.national_id || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">جنسیت</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.gender === 'female' ? 'خانم (Female)' : selectedStudent.profile?.gender === 'male' ? 'آقا (Male)' : 'نامشخص'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">تاریخ تولد</span>
                                        <span className="font-bold text-white mt-1 block font-mono">
                                            {selectedStudent.profile?.date_of_birth || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">وضعیت تاهل / گروه خونی</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.marital_status ? (selectedStudent.profile.marital_status === 'single' ? 'مجرد' : 'متاهل') : '—'}{' '}
                                            {selectedStudent.profile?.blood_type ? `(${selectedStudent.profile.blood_type})` : ''}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* Section 2: اطلاعات تماس و سکونت */}
                            <div className="space-y-3">
                                <h4 className="font-bold text-sm text-emerald-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                                    <Phone size={16} />
                                    <span>اطلاعات تماس و آدرس سکونت</span>
                                </h4>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800 flex items-center justify-between">
                                        <div>
                                            <span className="text-slate-400 block text-[11px]">شماره تماس اصلی</span>
                                            <span className="font-bold text-white mt-1 block font-mono" dir="ltr">
                                                {selectedStudent.user.phone || selectedStudent.profile?.phone_number || 'ثبت نشده'}
                                            </span>
                                        </div>
                                        {(selectedStudent.user.phone || selectedStudent.profile?.phone_number) && (
                                            <a
                                                href={`tel:${selectedStudent.user.phone || selectedStudent.profile?.phone_number}`}
                                                className="p-2 rounded-xl bg-slate-800 hover:bg-slate-750 text-emerald-400"
                                                title="تماس تلفنی"
                                            >
                                                <Phone size={15} />
                                            </a>
                                        )}
                                    </div>

                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800 flex items-center justify-between">
                                        <div>
                                            <span className="text-slate-400 block text-[11px]">شماره واتساپ</span>
                                            <span className="font-bold text-white mt-1 block font-mono" dir="ltr">
                                                {selectedStudent.profile?.whatsapp_number || selectedStudent.user.phone || 'ثبت نشده'}
                                            </span>
                                        </div>
                                        {(selectedStudent.profile?.whatsapp_number || selectedStudent.user.phone) && (
                                            <a
                                                href={`https://wa.me/${(selectedStudent.profile?.whatsapp_number || selectedStudent.user.phone).replace(/[^0-9]/g, '')}`}
                                                target="_blank"
                                                rel="noreferrer"
                                                className="p-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300"
                                                title="ارسال پیام واتساپ"
                                            >
                                                <MessageCircle size={15} />
                                            </a>
                                        )}
                                    </div>

                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">ولایت و ولسوالی</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.province || 'نامشخص'} {selectedStudent.profile?.district ? `- ${selectedStudent.profile.district}` : ''}
                                        </span>
                                    </div>

                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">آدرس فعلی</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.current_address || 'ثبت نشده'}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* Section 3: سوابق تحصیلی و دانشگاهی */}
                            <div className="space-y-3">
                                <h4 className="font-bold text-sm text-blue-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                                    <BookOpen size={16} />
                                    <span>سوابق تحصیلی و مهارت‌ها</span>
                                </h4>
                                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">آخرین مدرک تحصیلی</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.last_education_level || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">نام مکتب / دانشگاه</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.last_school_name || selectedStudent.profile?.university_name || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">رشته تحصیلی / نمره (GPA)</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.field_of_study || 'عمومی'} {selectedStudent.profile?.gpa ? `(معدل: ${selectedStudent.profile.gpa})` : ''}
                                        </span>
                                    </div>
                                </div>
                                {selectedStudent.profile?.skills && (
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">مهارت‌ها و تخصص‌ها</span>
                                        <p className="text-slate-200 mt-1 leading-relaxed">
                                            {selectedStudent.profile.skills}
                                        </p>
                                    </div>
                                )}
                            </div>

                            {/* Section 4: تماس اضطراری */}
                            <div className="space-y-3">
                                <h4 className="font-bold text-sm text-rose-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                                    <Shield size={16} />
                                    <span>مشخصات تماس اضطراری</span>
                                </h4>
                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">نام شخص اضطراری</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.emergency_contact_name || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">نسبت با شاگرد</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.profile?.emergency_contact_relation || 'ثبت نشده'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">شماره تماس اضطراری</span>
                                        <span className="font-bold text-white mt-1 block font-mono" dir="ltr">
                                            {selectedStudent.profile?.emergency_contact_phone || 'ثبت نشده'}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* Section 5: وضعیت در این دوره */}
                            <div className="space-y-3">
                                <h4 className="font-bold text-sm text-amber-400 flex items-center gap-2 border-b border-slate-800 pb-2">
                                    <Award size={16} />
                                    <span>وضعیت عملکردی در صنف «{course?.title}»</span>
                                </h4>
                                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">میزان پیشرفت صنف</span>
                                        <span className="font-bold text-brand-400 mt-1 block text-sm">
                                            {selectedStudent.progress_percentage}٪
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">وضعیت شمولیت</span>
                                        <span className="font-bold text-white mt-1 block">
                                            {selectedStudent.status === 'completed' ? 'فارغ‌التحصیل' : selectedStudent.status === 'active' ? 'مشغول به تحصیل' : 'انصراف / رد'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">تاریخ شمولیت در صنف</span>
                                        <span className="font-bold text-white mt-1 block font-mono text-[11px]">
                                            {selectedStudent.enrolled_at || 'نامشخص'}
                                        </span>
                                    </div>
                                    <div className="p-3 rounded-2xl bg-slate-850/60 border border-slate-800">
                                        <span className="text-slate-400 block text-[11px]">تاریخ فراغت</span>
                                        <span className="font-bold text-white mt-1 block font-mono text-[11px]">
                                            {selectedStudent.completed_at || 'در حال آموزش'}
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {/* Dossier Footer */}
                        <div className="p-4 bg-slate-950 border-t border-slate-800 flex items-center justify-between">
                            <a
                                href={`mailto:${selectedStudent.user.email}`}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors"
                            >
                                <Mail size={14} />
                                <span>ارسال ایمیل مستقیم</span>
                            </a>
                            <button
                                onClick={() => setSelectedStudent(null)}
                                className="px-4 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-bold transition-colors"
                            >
                                بستن پرونده
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
