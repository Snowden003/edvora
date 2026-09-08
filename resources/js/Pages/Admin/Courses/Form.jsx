import React, { useState, useRef } from 'react';
import { Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    GraduationCap,
    Save,
    ArrowRight,
    UploadCloud,
    FileSpreadsheet,
    Plus,
    Trash2,
    MoveUp,
    MoveDown,
    Check,
    AlertCircle,
    Calendar,
    Clock,
    Sparkles,
    Tag,
    Award,
    Image,
    X,
    FolderPlus
} from 'lucide-react';

export default function Form({ course = null, categories = [], teachers = [], isEdit = false }) {
    const [activeTab, setActiveTab] = useState('basic');
    const [categoryModalOpen, setCategoryModalOpen] = useState(false);
    const [newCatName, setNewCatName] = useState('');
    const [newCatDesc, setNewCatDesc] = useState('');
    const [catLoading, setCatLoading] = useState(false);
    const [catError, setCatError] = useState('');
    const [localCategories, setLocalCategories] = useState(categories);

    // Excel import state
    const [excelImportLoading, setExcelImportLoading] = useState(false);
    const [excelMessage, setExcelMessage] = useState(null);
    const fileInputRef = useRef(null);
    const thumbnailInputRef = useRef(null);

    const initialDays = [
        { key: 'saturday', label: 'شنبه' },
        { key: 'sunday', label: 'یکشنبه' },
        { key: 'monday', label: 'دوشنبه' },
        { key: 'tuesday', label: 'سه‌شنبه' },
        { key: 'wednesday', label: 'چهارشنبه' },
        { key: 'thursday', label: 'پنج‌شنبه' },
        { key: 'friday', label: 'جمعه' },
    ];

    const { data, setData, post, processing, errors } = useForm({
        title: course?.title || '',
        slug: course?.slug || '',
        category_id: course?.category_id || '',
        teacher_id: course?.teacher_id || '',
        description: course?.description || '',
        level: course?.level || 'beginner',
        duration_hours: course?.duration_hours || 10,
        status: course?.status || 'draft',
        start_date: course?.start_date || '',
        end_date: course?.end_date || '',
        is_featured: course?.is_featured || false,
        min_students: course?.min_students || '',
        max_students: course?.max_students || '',
        auto_start_enabled: course?.auto_start_enabled || false,

        // Benefits
        has_certificate: course?.has_certificate ?? true,
        has_lifetime_access: course?.has_lifetime_access ?? true,
        has_money_back: course?.has_money_back ?? false,
        has_downloadable_resources: course?.has_downloadable_resources ?? true,
        has_community_access: course?.has_community_access ?? true,
        has_mobile_access: course?.has_mobile_access ?? true,

        // Badges
        show_category_badge: course?.show_category_badge ?? true,
        show_level_badge: course?.show_level_badge ?? true,
        show_duration_badge: course?.show_duration_badge ?? true,
        show_certificate_badge: course?.show_certificate_badge ?? true,
        show_students_badge: course?.show_students_badge ?? true,

        // Schedule
        primary_class_start: course?.primary_class_start || '',
        primary_class_end: course?.primary_class_end || '',
        primary_class_days: course?.primary_class_days || [],
        primary_class_note: course?.primary_class_note || '',

        secondary_class_start: course?.secondary_class_start || '',
        secondary_class_end: course?.secondary_class_end || '',
        secondary_class_days: course?.secondary_class_days || [],
        secondary_class_note: course?.secondary_class_note || '',

        // Media
        thumbnail: null,
        existing_thumbnail: course?.thumbnail || null,

        // Lessons
        lessons: course?.lessons || [],
    });

    const [thumbnailPreview, setThumbnailPreview] = useState(course?.thumbnail || null);

    // Auto-generate slug from title
    const handleTitleChange = (e) => {
        const titleVal = e.target.value;
        setData((prev) => ({
            ...prev,
            title: titleVal,
            slug: !isEdit || prev.slug === '' ? generateSlug(titleVal) : prev.slug,
        }));
    };

    const generateSlug = (text) => {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\u0600-\u06FF\w\-]+/g, '')
            .replace(/\-\-+/g, '-');
    };

    // Thumbnail change
    const handleThumbnailChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('thumbnail', file);
            setThumbnailPreview(URL.createObjectURL(file));
        }
    };

    // Day toggles
    const handleDayToggle = (dayKey, isPrimary = true) => {
        const targetField = isPrimary ? 'primary_class_days' : 'secondary_class_days';
        const currentDays = data[targetField] || [];
        if (currentDays.includes(dayKey)) {
            setData(targetField, currentDays.filter((d) => d !== dayKey));
        } else {
            setData(targetField, [...currentDays, dayKey]);
        }
    };

    // Lessons handlers
    const handleAddLesson = () => {
        const newLesson = {
            id: null,
            title: '',
            duration_minutes: 30,
            description: '',
            order: data.lessons.length + 1,
        };
        setData('lessons', [...data.lessons, newLesson]);
    };

    const handleUpdateLesson = (index, field, value) => {
        const updated = [...data.lessons];
        updated[index] = { ...updated[index], [field]: value };
        setData('lessons', updated);
    };

    const handleRemoveLesson = (index) => {
        const updated = data.lessons.filter((_, i) => i !== index).map((l, i) => ({ ...l, order: i + 1 }));
        setData('lessons', updated);
    };

    const handleMoveLesson = (index, direction) => {
        if (
            (direction === 'up' && index === 0) ||
            (direction === 'down' && index === data.lessons.length - 1)
        ) {
            return;
        }
        const updated = [...data.lessons];
        const targetIndex = direction === 'up' ? index - 1 : index + 1;
        const temp = updated[index];
        updated[index] = updated[targetIndex];
        updated[targetIndex] = temp;

        // Re-assign order numbers
        const reordered = updated.map((l, i) => ({ ...l, order: i + 1 }));
        setData('lessons', reordered);
    };

    // Excel Curriculum Import
    const handleExcelUpload = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        setExcelImportLoading(true);
        setExcelMessage(null);

        const formData = new FormData();
        formData.append('file', file);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/admin/courses/import-curriculum', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    Accept: 'application/json',
                },
                body: formData,
            });

            const result = await res.json();
            if (res.ok && result.success) {
                // Merge or replace lessons
                const importedLessons = result.lessons.map((l, i) => ({
                    id: null,
                    title: l.title,
                    duration_minutes: l.duration_minutes,
                    description: l.description,
                    order: data.lessons.length + i + 1,
                }));

                setData('lessons', [...data.lessons, ...importedLessons]);
                setExcelMessage({ type: 'success', text: result.message });
            } else {
                setExcelMessage({ type: 'error', text: result.message || 'خطا در پردازش فایل اکسل.' });
            }
        } catch (err) {
            setExcelMessage({ type: 'error', text: 'خطا در ارتباط با سرور.' });
        } finally {
            setExcelImportLoading(false);
            if (fileInputRef.current) fileInputRef.current.value = '';
        }
    };

    // Inline Category Create
    const handleCreateCategory = async (e) => {
        e.preventDefault();
        if (!newCatName.trim()) return;

        setCatLoading(true);
        setCatError('');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/admin/courses/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    name: newCatName,
                    slug: generateSlug(newCatName),
                    description: newCatDesc,
                }),
            });

            const result = await res.json();
            if (res.ok && result.success) {
                setLocalCategories([...localCategories, result.category]);
                setData('category_id', result.category.id);
                setCategoryModalOpen(false);
                setNewCatName('');
                setNewCatDesc('');
            } else {
                setCatError(result.message || 'خطا در ثبت دسته‌بندی.');
            }
        } catch (err) {
            setCatError('خطا در برقراری ارتباط با سرور.');
        } finally {
            setCatLoading(false);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            post(`/admin/courses/${course.id}`);
        } else {
            post('/admin/courses');
        }
    };

    const tabs = [
        { key: 'basic', label: 'اطلاعات پایه و محتوا', icon: GraduationCap },
        { key: 'details', label: 'مشخصات و ظرفیت', icon: Tag },
        { key: 'schedule', label: 'برنامه کلاسی و زمان‌بندی', icon: Clock },
        { key: 'curriculum', label: `سرفصل‌ها (${data.lessons.length})`, icon: FileSpreadsheet },
        { key: 'benefits', label: 'مزایا و برچسب‌های دوره', icon: Award },
    ];

    return (
        <AdminLayout title={isEdit ? `ویرایش دوره: ${course?.title}` : 'افزودن دوره جدید'}>
            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Header with Breadcrumb and Submit Action */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 text-xs text-slate-400 mb-1">
                            <Link href="/admin/courses" className="hover:text-brand-400 transition-colors">
                                دوره‌های آموزشی
                            </Link>
                            <span>/</span>
                            <span className="text-slate-200">{isEdit ? 'ویرایش دوره' : 'ایجاد دوره جدید'}</span>
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight font-display">
                            {isEdit ? `ویرایش دوره «${course?.title}»` : 'تعریف دوره آموزشی جدید'}
                        </h1>
                    </div>

                    <div className="flex items-center gap-3 shrink-0">
                        <Link
                            href="/admin/courses"
                            className="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-xs font-bold transition-colors"
                        >
                            انصراف
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl bg-gradient-to-tr from-brand-600 via-brand-500 to-accent-500 hover:from-brand-500 hover:to-accent-400 text-white font-bold text-xs shadow-glow hover:shadow-lg transition-all active:scale-95 disabled:opacity-50"
                        >
                            <Save size={16} />
                            <span>{processing ? 'در حال ذخیره...' : isEdit ? 'ذخیره تغییرات' : 'انتشار / ثبت دوره'}</span>
                        </button>
                    </div>
                </div>

                {/* Form Navigation Tabs */}
                <div className="flex items-center gap-2 p-1.5 rounded-2xl bg-slate-900/80 border border-slate-800 overflow-x-auto">
                    {tabs.map((tab) => {
                        const Icon = tab.icon;
                        const isActive = activeTab === tab.key;
                        return (
                            <button
                                key={tab.key}
                                type="button"
                                onClick={() => setActiveTab(tab.key)}
                                className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all ${
                                    isActive
                                        ? 'bg-brand-500 text-white shadow-glow'
                                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
                                }`}
                            >
                                <Icon size={16} />
                                <span>{tab.label}</span>
                            </button>
                        );
                    })}
                </div>

                {/* Global Errors Banner */}
                {Object.keys(errors).length > 0 && (
                    <div className="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                        <div className="flex items-center gap-2 font-bold text-rose-400">
                            <AlertCircle size={16} />
                            <span>لطفاً خطاهای زیر را بررسی و اصلاح فرمایید:</span>
                        </div>
                        <ul className="list-disc list-inside space-y-0.5 pt-1 pr-2">
                            {Object.entries(errors).map(([key, msg]) => (
                                <li key={key}>{msg}</li>
                            ))}
                        </ul>
                    </div>
                )}

                {/* TAB 1: Basic Information */}
                {activeTab === 'basic' && (
                    <div className="rounded-3xl p-6 bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl shadow-2xl space-y-6 animate-in fade-in duration-200">
                        <div className="border-b border-slate-800 pb-3">
                            <h3 className="text-base font-bold text-white">اطلاعات اصلی دوره</h3>
                            <p className="text-xs text-slate-400">عنوان، دسته‌بندی، مدرس و تصویر کاور دوره</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Course Title */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    عنوان دوره <span className="text-rose-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={handleTitleChange}
                                    placeholder="مثال: دوره جامع آموزش برنامه‌نویسی ری‌اکت"
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                />
                                {errors.title && <p className="text-[11px] text-rose-400">{errors.title}</p>}
                            </div>

                            {/* Slug */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    اسلاگ و آدرس URL <span className="text-rose-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.slug}
                                    onChange={(e) => setData('slug', e.target.value)}
                                    placeholder="react-complete-course"
                                    dir="ltr"
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 text-left font-mono"
                                />
                                {errors.slug && <p className="text-[11px] text-rose-400">{errors.slug}</p>}
                            </div>

                            {/* Category with inline create */}
                            <div className="space-y-1.5">
                                <div className="flex items-center justify-between">
                                    <label className="text-xs font-bold text-slate-300">
                                        دسته‌بندی دوره <span className="text-rose-400">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        onClick={() => setCategoryModalOpen(true)}
                                        className="text-[11px] text-brand-400 hover:text-brand-300 font-bold flex items-center gap-1"
                                    >
                                        <Plus size={13} />
                                        <span>افزودن دسته‌بندی جدید</span>
                                    </button>
                                </div>
                                <select
                                    value={data.category_id}
                                    onChange={(e) => setData('category_id', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                >
                                    <option value="">انتخاب دسته‌بندی...</option>
                                    {localCategories.map((cat) => (
                                        <option key={cat.id} value={cat.id}>
                                            {cat.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.category_id && <p className="text-[11px] text-rose-400">{errors.category_id}</p>}
                            </div>

                            {/* Instructor / Teacher */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    مدرس و استاد دوره
                                </label>
                                <select
                                    value={data.teacher_id}
                                    onChange={(e) => setData('teacher_id', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                >
                                    <option value="">تعیین نشده (پیش‌نویس)</option>
                                    {teachers.map((t) => (
                                        <option key={t.id} value={t.id}>
                                            {t.name} ({t.email})
                                        </option>
                                    ))}
                                </select>
                                <p className="text-[11px] text-slate-400">
                                    برای انتشار رسمی دوره، انتخاب یک مدرس فعال الزامی است.
                                </p>
                                {errors.teacher_id && <p className="text-[11px] text-rose-400">{errors.teacher_id}</p>}
                            </div>

                            {/* Thumbnail Uploader */}
                            <div className="md:col-span-2 space-y-2">
                                <label className="block text-xs font-bold text-slate-300">
                                    تصویر کاور دوره (Thumbnail)
                                </label>
                                <div className="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl bg-slate-950/60 border border-slate-800 border-dashed">
                                    {/* Preview */}
                                    <div className="w-40 h-24 rounded-xl overflow-hidden bg-slate-900 border border-slate-800 shrink-0 flex items-center justify-center">
                                        {thumbnailPreview ? (
                                            <img
                                                src={thumbnailPreview}
                                                alt="Preview"
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <Image className="text-slate-600" size={32} />
                                        )}
                                    </div>

                                    <div className="flex-1 space-y-2 text-center sm:text-right">
                                        <input
                                            type="file"
                                            ref={thumbnailInputRef}
                                            onChange={handleThumbnailChange}
                                            accept="image/*"
                                            className="hidden"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => thumbnailInputRef.current?.click()}
                                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition-colors"
                                        >
                                            <UploadCloud size={16} />
                                            <span>انتخاب تصویر از سیستم</span>
                                        </button>
                                        <p className="text-[11px] text-slate-400">
                                            فرمت‌های مجاز: JPG, PNG, WEBP — حداکثر حجم: ۳ مگابایت
                                        </p>
                                    </div>
                                </div>
                                {errors.thumbnail && <p className="text-[11px] text-rose-400">{errors.thumbnail}</p>}
                            </div>

                            {/* Description */}
                            <div className="md:col-span-2 space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    توضیحات و معرفی دوره <span className="text-rose-400">*</span>
                                </label>
                                <textarea
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={5}
                                    placeholder="توضیحات کامل در مورد اهداف دوره، پیش‌نیازها و آنچه دانشجو خواهد آموخت..."
                                    className="w-full p-4 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                />
                                {errors.description && <p className="text-[11px] text-rose-400">{errors.description}</p>}
                            </div>
                        </div>
                    </div>
                )}

                {/* TAB 2: Details & Capacity */}
                {activeTab === 'details' && (
                    <div className="rounded-3xl p-6 bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl shadow-2xl space-y-6 animate-in fade-in duration-200">
                        <div className="border-b border-slate-800 pb-3">
                            <h3 className="text-base font-bold text-white">مشخصات، وضعیت و ظرفیت دوره</h3>
                            <p className="text-xs text-slate-400">سطح دشواری، مدت زمان، وضعیت انتشار و تاریخ‌های شروع و پایان</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {/* Level */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    سطح دشواری <span className="text-rose-400">*</span>
                                </label>
                                <select
                                    value={data.level}
                                    onChange={(e) => setData('level', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                >
                                    <option value="beginner">مبتدی (Beginner)</option>
                                    <option value="intermediate">متوسط (Intermediate)</option>
                                    <option value="advanced">پیشرفته (Advanced)</option>
                                </select>
                            </div>

                            {/* Duration */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    مدت زمان (ساعت) <span className="text-rose-400">*</span>
                                </label>
                                <input
                                    type="number"
                                    value={data.duration_hours}
                                    onChange={(e) => setData('duration_hours', parseInt(e.target.value) || 0)}
                                    min="1"
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                />
                            </div>

                            {/* Status */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    وضعیت دوره <span className="text-rose-400">*</span>
                                </label>
                                <select
                                    value={data.status}
                                    onChange={(e) => setData('status', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                >
                                    <option value="draft">پیش‌نویس (قابل درخواست توسط اساتید)</option>
                                    <option value="published" disabled={!data.teacher_id}>
                                        منتشر شده (قابل مشاهده و ثبت‌نام دانشجو) {!data.teacher_id ? '— نیازمند مدرس' : ''}
                                    </option>
                                    <option value="started" disabled={!data.teacher_id}>
                                        در حال برگزاری (شروع شده) {!data.teacher_id ? '— نیازمند مدرس' : ''}
                                    </option>
                                    <option value="archived">آرشیو شده</option>
                                </select>
                            </div>

                            {/* Start Date */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    تاریخ شروع دوره
                                </label>
                                <input
                                    type="date"
                                    value={data.start_date}
                                    onChange={(e) => setData('start_date', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                />
                            </div>

                            {/* End Date */}
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">
                                    تاریخ پایان دوره
                                </label>
                                <input
                                    type="date"
                                    value={data.end_date}
                                    onChange={(e) => setData('end_date', e.target.value)}
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                />
                            </div>

                            {/* Featured on Homepage */}
                            <div className="flex items-center gap-3 pt-6">
                                <label className="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={data.is_featured}
                                        onChange={(e) => setData('is_featured', e.target.checked)}
                                        className="sr-only peer"
                                    />
                                    <div className="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </label>
                                <div>
                                    <span className="text-xs font-bold text-white block">دوره ویژه (Hero هوم‌پیج)</span>
                                    <span className="text-[11px] text-slate-400">نمایش در اسلایدر ویژه بالای صفحه اصلی</span>
                                </div>
                            </div>
                        </div>

                        {/* Enrollment Settings & Auto-start */}
                        <div className="pt-6 border-t border-slate-800 space-y-4">
                            <h4 className="text-sm font-bold text-white flex items-center gap-2">
                                <Sparkles size={16} className="text-brand-400" />
                                <span>تنظیمات ثبت‌نام و شروع خودکار</span>
                            </h4>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-1.5">
                                    <label className="block text-xs font-bold text-slate-300">
                                        حداقل تعداد دانشجویان (برای شروع)
                                    </label>
                                    <input
                                        type="number"
                                        value={data.min_students}
                                        onChange={(e) => setData('min_students', e.target.value)}
                                        placeholder="مثال: ۱۰"
                                        min="1"
                                        className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                    />
                                </div>

                                <div className="space-y-1.5">
                                    <label className="block text-xs font-bold text-slate-300">
                                        حداکثر ظرفیت دوره
                                    </label>
                                    <input
                                        type="number"
                                        value={data.max_students}
                                        onChange={(e) => setData('max_students', e.target.value)}
                                        placeholder="اختیاری (مثال: ۵۰)"
                                        min="1"
                                        className="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50"
                                    />
                                </div>

                                <div className="md:col-span-2 flex items-center gap-3 p-4 rounded-2xl bg-slate-950/50 border border-slate-800">
                                    <input
                                        type="checkbox"
                                        id="auto_start_enabled"
                                        checked={data.auto_start_enabled}
                                        onChange={(e) => setData('auto_start_enabled', e.target.checked)}
                                        className="rounded border-slate-700 bg-slate-800 text-brand-500 focus:ring-brand-500 w-4 h-4"
                                    />
                                    <label htmlFor="auto_start_enabled" className="text-xs text-slate-300 cursor-pointer">
                                        <span className="font-bold text-white block">شروع خودکار پس از تکمیل حداقل ظرفیت</span>
                                        به محض رسیدن تعداد ثبت‌نامی‌ها به حد نصاب، وضعیت دوره به طور خودکار به حالت «شروع شده» تغییر می‌کند.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* TAB 3: Schedule */}
                {activeTab === 'schedule' && (
                    <div className="rounded-3xl p-6 bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl shadow-2xl space-y-6 animate-in fade-in duration-200">
                        <div className="border-b border-slate-800 pb-3">
                            <h3 className="text-base font-bold text-white">زمان‌بندی جلسات کلاسی</h3>
                            <p className="text-xs text-slate-400">
                                تعیین ساعات و روزهای برگزاری جلسه اصلی و جلسه رزرو/پشتیبان
                            </p>
                        </div>

                        {/* Primary Class Time */}
                        <div className="p-5 rounded-2xl bg-slate-950/50 border border-slate-800 space-y-4">
                            <h4 className="text-sm font-bold text-brand-400 flex items-center gap-2">
                                <Clock size={16} />
                                <span>زمان‌بندی کلاس اصلی (Primary Schedule)</span>
                            </h4>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="block text-xs font-bold text-slate-300">ساعت شروع</label>
                                    <input
                                        type="time"
                                        value={data.primary_class_start}
                                        onChange={(e) => setData('primary_class_start', e.target.value)}
                                        className="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-100"
                                    />
                                </div>
                                <div className="space-y-1.5">
                                    <label className="block text-xs font-bold text-slate-300">ساعت پایان</label>
                                    <input
                                        type="time"
                                        value={data.primary_class_end}
                                        onChange={(e) => setData('primary_class_end', e.target.value)}
                                        className="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-100"
                                    />
                                </div>
                            </div>

                            {/* Days Checklist */}
                            <div className="space-y-2">
                                <label className="block text-xs font-bold text-slate-300">روزهای برگزاری کلاس اصلی</label>
                                <div className="flex flex-wrap gap-2">
                                    {initialDays.map((d) => {
                                        const isSelected = (data.primary_class_days || []).includes(d.key);
                                        return (
                                            <button
                                                key={d.key}
                                                type="button"
                                                onClick={() => handleDayToggle(d.key, true)}
                                                className={`px-3 py-1.5 rounded-xl text-xs font-bold border transition-all ${
                                                    isSelected
                                                        ? 'bg-brand-500 text-white border-brand-500 shadow-glow'
                                                        : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white'
                                                }`}
                                            >
                                                {d.label}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">یادداشت جلسه اصلی</label>
                                <input
                                    type="text"
                                    value={data.primary_class_note}
                                    onChange={(e) => setData('primary_class_note', e.target.value)}
                                    placeholder="مثال: حضور کلیه دانشجویان در این ساعت الزامی است."
                                    className="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100"
                                />
                            </div>
                        </div>

                        {/* Secondary (Backup) Class Time */}
                        <div className="p-5 rounded-2xl bg-slate-950/50 border border-slate-800 space-y-4">
                            <h4 className="text-sm font-bold text-purple-400 flex items-center gap-2">
                                <Clock size={16} />
                                <span>زمان‌بندی کلاس رزرو / جبرانی (Backup Schedule)</span>
                            </h4>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="block text-xs font-bold text-slate-300">ساعت شروع رزرو</label>
                                    <input
                                        type="time"
                                        value={data.secondary_class_start}
                                        onChange={(e) => setData('secondary_class_start', e.target.value)}
                                        className="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-100"
                                    />
                                </div>
                                <div className="space-y-1.5">
                                    <label className="block text-xs font-bold text-slate-300">ساعت پایان رزرو</label>
                                    <input
                                        type="time"
                                        value={data.secondary_class_end}
                                        onChange={(e) => setData('secondary_class_end', e.target.value)}
                                        className="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm text-slate-100"
                                    />
                                </div>
                            </div>

                            {/* Backup Days Checklist */}
                            <div className="space-y-2">
                                <label className="block text-xs font-bold text-slate-300">روزهای جلسه رزرو</label>
                                <div className="flex flex-wrap gap-2">
                                    {initialDays.map((d) => {
                                        const isSelected = (data.secondary_class_days || []).includes(d.key);
                                        return (
                                            <button
                                                key={d.key}
                                                type="button"
                                                onClick={() => handleDayToggle(d.key, false)}
                                                className={`px-3 py-1.5 rounded-xl text-xs font-bold border transition-all ${
                                                    isSelected
                                                        ? 'bg-purple-500 text-white border-purple-500 shadow-glow-purple'
                                                        : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white'
                                                }`}
                                            >
                                                {d.label}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">یادداشت جلسه رزرو</label>
                                <input
                                    type="text"
                                    value={data.secondary_class_note}
                                    onChange={(e) => setData('secondary_class_note', e.target.value)}
                                    placeholder="مثال: در صورت عدم امکان حضور استاد در جلسه اصلی، کلاس در این ساعت برگزار می‌گردد."
                                    className="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100"
                                />
                            </div>
                        </div>
                    </div>
                )}

                {/* TAB 4: Curriculum (Lessons Builder & Excel Import) */}
                {activeTab === 'curriculum' && (
                    <div className="rounded-3xl p-6 bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl shadow-2xl space-y-6 animate-in fade-in duration-200">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                            <div>
                                <h3 className="text-base font-bold text-white">سرفصل‌ها و دروس دوره (Curriculum)</h3>
                                <p className="text-xs text-slate-400">
                                    تعریف دروس، مدت زمان جلسات، یا درون‌ریزی خودکار از طریق فایل اکسل
                                </p>
                            </div>

                            {/* Excel Import Button */}
                            <div className="flex items-center gap-2">
                                <input
                                    type="file"
                                    ref={fileInputRef}
                                    onChange={handleExcelUpload}
                                    accept=".xlsx, .xls"
                                    className="hidden"
                                />
                                <button
                                    type="button"
                                    disabled={excelImportLoading}
                                    onClick={() => fileInputRef.current?.click()}
                                    className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 text-xs font-bold transition-all disabled:opacity-50"
                                >
                                    <FileSpreadsheet size={16} />
                                    <span>{excelImportLoading ? 'در حال پردازش...' : 'ایمپورت سرفصل‌ها از فایل اکسل (.xlsx)'}</span>
                                </button>
                            </div>
                        </div>

                        {/* Excel feedback alert */}
                        {excelMessage && (
                            <div
                                className={`p-3 rounded-xl border text-xs flex items-center justify-between ${
                                    excelMessage.type === 'success'
                                        ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300'
                                        : 'bg-rose-500/10 border-rose-500/30 text-rose-300'
                                }`}
                            >
                                <span>{excelMessage.text}</span>
                                <button onClick={() => setExcelMessage(null)}>
                                    <X size={15} />
                                </button>
                            </div>
                        )}

                        {/* Lessons List */}
                        <div className="space-y-3">
                            {data.lessons.length === 0 ? (
                                <div className="p-8 rounded-2xl bg-slate-950/40 border border-slate-800 text-center space-y-2">
                                    <p className="text-xs text-slate-400">هنوز درسی برای این دوره ثبت نشده است.</p>
                                    <button
                                        type="button"
                                        onClick={handleAddLesson}
                                        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-500/20 text-brand-300 border border-brand-500/30 text-xs font-bold hover:bg-brand-500/30 transition-colors"
                                    >
                                        <Plus size={14} />
                                        <span>افزودن اولین درس</span>
                                    </button>
                                </div>
                            ) : (
                                data.lessons.map((lesson, idx) => (
                                    <div
                                        key={idx}
                                        className="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 hover:border-slate-700 transition-colors"
                                    >
                                        <div className="flex items-center justify-between gap-3">
                                            <div className="flex items-center gap-2">
                                                <span className="w-6 h-6 rounded-lg bg-brand-500/20 text-brand-400 font-bold text-xs flex items-center justify-center">
                                                    {lesson.order || idx + 1}
                                                </span>
                                                <span className="text-xs font-bold text-slate-300">
                                                    {lesson.title || `درس شماره ${idx + 1}`}
                                                </span>
                                            </div>

                                            {/* Action buttons */}
                                            <div className="flex items-center gap-1">
                                                <button
                                                    type="button"
                                                    disabled={idx === 0}
                                                    onClick={() => handleMoveLesson(idx, 'up')}
                                                    className="p-1 rounded-lg text-slate-500 hover:text-white disabled:opacity-30"
                                                    title="انتقال به بالا"
                                                >
                                                    <MoveUp size={15} />
                                                </button>
                                                <button
                                                    type="button"
                                                    disabled={idx === data.lessons.length - 1}
                                                    onClick={() => handleMoveLesson(idx, 'down')}
                                                    className="p-1 rounded-lg text-slate-500 hover:text-white disabled:opacity-30"
                                                    title="انتقال به پایین"
                                                >
                                                    <MoveDown size={15} />
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => handleRemoveLesson(idx)}
                                                    className="p-1 rounded-lg text-rose-400 hover:bg-rose-500/10"
                                                    title="حذف درس"
                                                >
                                                    <Trash2 size={15} />
                                                </button>
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                            <div className="sm:col-span-9 space-y-1">
                                                <input
                                                    type="text"
                                                    value={lesson.title}
                                                    onChange={(e) => handleUpdateLesson(idx, 'title', e.target.value)}
                                                    placeholder="عنوان درس (مثال: معرفی کامپوننت‌ها و استیت)"
                                                    className="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 placeholder-slate-500 focus:ring-1 focus:ring-brand-500"
                                                />
                                            </div>
                                            <div className="sm:col-span-3 space-y-1">
                                                <input
                                                    type="number"
                                                    value={lesson.duration_minutes}
                                                    onChange={(e) => handleUpdateLesson(idx, 'duration_minutes', parseInt(e.target.value) || 0)}
                                                    placeholder="مدت (دقیقه)"
                                                    min="1"
                                                    className="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 focus:ring-1 focus:ring-brand-500"
                                                />
                                            </div>
                                            <div className="sm:col-span-12">
                                                <textarea
                                                    value={lesson.description || ''}
                                                    onChange={(e) => handleUpdateLesson(idx, 'description', e.target.value)}
                                                    rows={2}
                                                    placeholder="توضیحات کوتاه یا سرفصل‌های فرعی این جلسه..."
                                                    className="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-100 placeholder-slate-500 focus:ring-1 focus:ring-brand-500"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}

                            {/* Add Lesson Button */}
                            <button
                                type="button"
                                onClick={handleAddLesson}
                                className="w-full py-3 rounded-2xl bg-slate-950/40 hover:bg-slate-950/80 border border-slate-800 border-dashed text-slate-400 hover:text-brand-300 text-xs font-bold flex items-center justify-center gap-2 transition-colors"
                            >
                                <Plus size={16} />
                                <span>افزودن درس جدید به سرفصل‌ها</span>
                            </button>
                        </div>
                    </div>
                )}

                {/* TAB 5: Benefits & Badges */}
                {activeTab === 'benefits' && (
                    <div className="rounded-3xl p-6 bg-slate-900/60 border border-slate-800/80 backdrop-blur-xl shadow-2xl space-y-6 animate-in fade-in duration-200">
                        <div className="border-b border-slate-800 pb-3">
                            <h3 className="text-base font-bold text-white">مزایای دوره و برچسب‌های اطلاعاتی</h3>
                            <p className="text-xs text-slate-400">
                                تمامی دوره‌های ادورا کاملاً رایگان هستند. مزایای جانبی و نحوه نمایش برچسب‌ها را مشخص کنید.
                            </p>
                        </div>

                        {/* Free Benefits Checkboxes */}
                        <div className="space-y-3">
                            <h4 className="text-sm font-bold text-brand-400">مزایای فعال این دوره</h4>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                {[
                                    { key: 'has_certificate', label: 'گواهینامه پایان دوره رایگان' },
                                    { key: 'has_lifetime_access', label: 'دسترسی همیشگی به ویدیوها' },
                                    { key: 'has_mobile_access', label: 'دسترسی در موبایل و دسکتاپ' },
                                    { key: 'has_downloadable_resources', label: 'فایل‌ها و منابع دانلودی' },
                                    { key: 'has_community_access', label: 'دسترسی به گروه و کامیونیتی' },
                                ].map((b) => (
                                    <label
                                        key={b.key}
                                        className="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-950/50 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors"
                                    >
                                        <input
                                            type="checkbox"
                                            checked={data[b.key]}
                                            onChange={(e) => setData(b.key, e.target.checked)}
                                            className="rounded border-slate-700 bg-slate-800 text-brand-500 focus:ring-brand-500 w-4 h-4"
                                        />
                                        <span className="text-xs font-semibold text-slate-200">{b.label}</span>
                                    </label>
                                ))}
                            </div>
                        </div>

                        {/* Display Badges */}
                        <div className="space-y-3 pt-6 border-t border-slate-800">
                            <h4 className="text-sm font-bold text-purple-400">نمایش برچسب‌ها در سایدبار دوره</h4>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                {[
                                    { key: 'show_category_badge', label: 'نمایش برچسب دسته‌بندی' },
                                    { key: 'show_level_badge', label: 'نمایش برچسب سطح دشواری' },
                                    { key: 'show_duration_badge', label: 'نمایش برچسب مدت زمان' },
                                    { key: 'show_certificate_badge', label: 'نمایش بج گواهینامه' },
                                    { key: 'show_students_badge', label: 'نمایش تعداد دانشجویان' },
                                ].map((b) => (
                                    <label
                                        key={b.key}
                                        className="flex items-center gap-3 p-3.5 rounded-2xl bg-slate-950/50 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors"
                                    >
                                        <input
                                            type="checkbox"
                                            checked={data[b.key]}
                                            onChange={(e) => setData(b.key, e.target.checked)}
                                            className="rounded border-slate-700 bg-slate-800 text-purple-500 focus:ring-purple-500 w-4 h-4"
                                        />
                                        <span className="text-xs font-semibold text-slate-200">{b.label}</span>
                                    </label>
                                ))}
                            </div>
                        </div>
                    </div>
                )}
            </form>

            {/* Quick Inline Category Modal */}
            {categoryModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-200">
                    <div className="w-full max-w-md rounded-3xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-4">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div className="flex items-center gap-2">
                                <FolderPlus size={18} className="text-brand-400" />
                                <h3 className="text-sm font-bold text-white">افزودن دسته‌بندی جدید</h3>
                            </div>
                            <button onClick={() => setCategoryModalOpen(false)} className="text-slate-400 hover:text-white">
                                <X size={16} />
                            </button>
                        </div>

                        {catError && <p className="text-xs text-rose-400">{catError}</p>}

                        <form onSubmit={handleCreateCategory} className="space-y-4">
                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">نام دسته‌بندی</label>
                                <input
                                    type="text"
                                    value={newCatName}
                                    onChange={(e) => setNewCatName(e.target.value)}
                                    placeholder="مثال: هوش مصنوعی و داده"
                                    required
                                    className="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-100"
                                />
                            </div>

                            <div className="space-y-1.5">
                                <label className="block text-xs font-bold text-slate-300">توضیحات (اختیاری)</label>
                                <textarea
                                    value={newCatDesc}
                                    onChange={(e) => setNewCatDesc(e.target.value)}
                                    rows={2}
                                    className="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-100"
                                />
                            </div>

                            <div className="flex items-center gap-2 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setCategoryModalOpen(false)}
                                    className="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold"
                                >
                                    انصراف
                                </button>
                                <button
                                    type="submit"
                                    disabled={catLoading}
                                    className="flex-1 py-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold shadow-glow"
                                >
                                    {catLoading ? 'در حال ثبت...' : 'ثبت دسته‌بندی'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
