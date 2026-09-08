import React, { useState, useRef } from 'react';
import { Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    BookOpen,
    Save,
    ArrowRight,
    UploadCloud,
    Plus,
    Trash2,
    Check,
    AlertCircle,
    FileText,
    Sparkles,
    Tag,
    Image,
    X,
    FolderPlus,
    Download,
    Eye,
    ExternalLink,
    HelpCircle,
    Info
} from 'lucide-react';

export default function Form({ book = null, categories = [], isEdit = false }) {
    const [localCategories, setLocalCategories] = useState(categories);
    const [categoryModalOpen, setCategoryModalOpen] = useState(false);
    const [newCatName, setNewCatName] = useState('');
    const [newCatDesc, setNewCatDesc] = useState('');
    const [catLoading, setCatLoading] = useState(false);
    const [catError, setCatError] = useState('');

    // Preview state for newly selected cover image
    const [coverPreview, setCoverPreview] = useState(book?.cover_url || null);
    const [pdfFileName, setPdfFileName] = useState(book?.pdf_name || null);

    const coverInputRef = useRef(null);
    const pdfInputRef = useRef(null);

    const { data, setData, post, processing, errors } = useForm({
        title: book?.title || '',
        slug: book?.slug || '',
        author: book?.author || '',
        category_id: book?.category_id || '',
        description: book?.description || '',
        is_published: book?.is_published ?? true,
        cover_image: null,
        pdf_file: null,
    });

    // Handle Title Change & Auto Slug Generation
    const handleTitleChange = (e) => {
        const val = e.target.value;
        setData((prev) => {
            const shouldUpdateSlug = !isEdit && (!prev.slug || prev.slug === generateSlug(prev.title));
            return {
                ...prev,
                title: val,
                slug: shouldUpdateSlug ? generateSlug(val) : prev.slug,
            };
        });
    };

    const generateSlug = (text) => {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\u0600-\u06FF\-]+/g, '')
            .replace(/\-\-+/g, '-');
    };

    // Handle Cover Image Selection
    const handleCoverChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('cover_image', file);
            const reader = new FileReader();
            reader.onload = () => {
                setCoverPreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleRemoveCover = () => {
        setData('cover_image', null);
        setCoverPreview(null);
        if (coverInputRef.current) {
            coverInputRef.current.value = '';
        }
    };

    // Handle PDF File Selection
    const handlePdfChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('pdf_file', file);
            setPdfFileName(file.name);
        }
    };

    // Quick Category Creation Modal
    const handleCreateCategory = async (e) => {
        e.preventDefault();
        if (!newCatName.trim()) {
            setCatError('لطفاً نام دسته‌بندی را وارد کنید.');
            return;
        }

        setCatLoading(true);
        setCatError('');

        try {
            const response = await fetch('/admin/books/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: newCatName.trim(),
                    description: newCatDesc.trim(),
                }),
            });

            const result = await response.json();
            if (response.ok && result.category) {
                const newCat = result.category;
                setLocalCategories((prev) => [...prev, newCat]);
                setData('category_id', newCat.id);
                setNewCatName('');
                setNewCatDesc('');
                setCategoryModalOpen(false);
            } else {
                setCatError(result.message || 'خطا در ثبت دسته‌بندی.');
            }
        } catch (err) {
            setCatError('خطا در ارتباط با سرور.');
        } finally {
            setCatLoading(false);
        }
    };

    // Form Submit
    const handleSubmit = (e) => {
        e.preventDefault();

        if (isEdit) {
            post(`/admin/books/${book.id}`, {
                preserveScroll: true,
            });
        } else {
            post('/admin/books', {
                preserveScroll: true,
            });
        }
    };

    return (
        <AdminLayout title={isEdit ? `ویرایش کتاب: ${book?.title}` : 'افزودن کتاب یا مقاله جدید | ادورا'}>
            <form onSubmit={handleSubmit} className="space-y-8">
                {/* Header with Back link and Actions */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/60 backdrop-blur-xl p-6 rounded-3xl border border-slate-800/80 shadow-2xl relative overflow-hidden">
                    <div className="flex items-center gap-4">
                        <Link
                            href="/admin/books"
                            className="w-12 h-12 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-all border border-slate-700 shrink-0"
                            title="بازگشت به لیست کتاب‌ها"
                        >
                            <ArrowRight size={20} />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2">
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight font-display">
                                    {isEdit ? `ویرایش: ${book?.title}` : 'افزودن کتاب یا مقاله جدید'}
                                </h1>
                                <span
                                    className={`px-2.5 py-0.5 rounded-full text-[11px] font-bold border ${
                                        data.is_published
                                            ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'
                                            : 'bg-amber-500/20 text-amber-300 border-amber-500/30'
                                    }`}
                                >
                                    {data.is_published ? 'منتشر خواهد شد' : 'پیش‌نویس'}
                                </span>
                            </div>
                            <p className="text-xs sm:text-sm text-slate-400 mt-1">
                                {isEdit
                                    ? 'ویرایش مشخصات، فایل PDF، تصویر کاور و وضعیت انتشار کتاب'
                                    : 'اطلاعات، فایل PDF و کاور کتاب یا مقاله جدید را وارد نمایید'}
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-3">
                        <Link
                            href="/admin/books"
                            className="px-5 py-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-bold transition-all border border-slate-700"
                        >
                            انصراف
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs sm:text-sm font-bold shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition-all duration-300 disabled:opacity-50"
                        >
                            <Save size={16} />
                            <span>{processing ? 'در حال ذخیره‌سازی...' : isEdit ? 'ذخیره تغییرات' : 'انتشار و ایجاد کتاب'}</span>
                        </button>
                    </div>
                </div>

                {/* Form Body Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Left 2 Columns: Main Details and Description */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Section 1: Basic Information */}
                        <div className="p-6 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-6">
                            <div className="flex items-center gap-3 pb-4 border-b border-slate-800/80">
                                <div className="p-2 rounded-xl bg-brand-500/20 text-brand-400 border border-brand-500/30">
                                    <FileText size={20} />
                                </div>
                                <div>
                                    <h2 className="text-base font-bold text-white">مشخصات و عنوان اثر</h2>
                                    <p className="text-xs text-slate-400">اطلاعات هویتی، نویسنده و نامک کتاب را مشخص کنید</p>
                                </div>
                            </div>

                            <div className="space-y-4">
                                {/* Title */}
                                <div>
                                    <label className="block text-xs font-bold text-slate-300 mb-1.5">
                                        عنوان کتاب / مقاله <span className="text-rose-400">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        value={data.title}
                                        onChange={handleTitleChange}
                                        placeholder="مثال: آموزش جامع معماری تمیز در نرم‌افزار (Clean Code)"
                                        className={`w-full px-4 py-3 rounded-2xl bg-slate-950/70 border text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 transition-all ${
                                            errors.title
                                                ? 'border-rose-500 focus:ring-rose-500/20'
                                                : 'border-slate-800 focus:border-brand-500 focus:ring-brand-500/20'
                                        }`}
                                    />
                                    {errors.title && <p className="text-xs text-rose-400 mt-1">{errors.title}</p>}
                                </div>

                                {/* Slug and Author Grid */}
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {/* Slug */}
                                    <div>
                                        <div className="flex items-center justify-between mb-1.5">
                                            <label className="text-xs font-bold text-slate-300">
                                                نامک آدرس (URL Slug) <span className="text-rose-400">*</span>
                                            </label>
                                            <span className="text-[10px] text-slate-500 font-mono">یکتا در وب‌سایت</span>
                                        </div>
                                        <input
                                            type="text"
                                            dir="ltr"
                                            value={data.slug}
                                            onChange={(e) => setData('slug', e.target.value)}
                                            placeholder="clean-code-architecture"
                                            className={`w-full px-4 py-3 rounded-2xl bg-slate-950/70 border text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 font-mono transition-all ${
                                                errors.slug
                                                    ? 'border-rose-500 focus:ring-rose-500/20'
                                                    : 'border-slate-800 focus:border-brand-500 focus:ring-brand-500/20'
                                            }`}
                                        />
                                        {errors.slug && <p className="text-xs text-rose-400 mt-1">{errors.slug}</p>}
                                        <p className="text-[11px] text-slate-500 mt-1" dir="rtl">
                                            لینک عمومی: <span className="font-mono text-brand-400" dir="ltr">/books/{data.slug || 'slug'}</span>
                                        </p>
                                    </div>

                                    {/* Author */}
                                    <div>
                                        <label className="block text-xs font-bold text-slate-300 mb-1.5">
                                            نویسنده / مترجم / گردآورنده
                                        </label>
                                        <input
                                            type="text"
                                            value={data.author}
                                            onChange={(e) => setData('author', e.target.value)}
                                            placeholder="مثال: رابرت سی مارتین (Uncle Bob)"
                                            className="w-full px-4 py-3 rounded-2xl bg-slate-950/70 border border-slate-800 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all"
                                        />
                                        {errors.author && <p className="text-xs text-rose-400 mt-1">{errors.author}</p>}
                                    </div>
                                </div>

                                {/* Category with Add Category Modal Trigger */}
                                <div>
                                    <div className="flex items-center justify-between mb-1.5">
                                        <label className="text-xs font-bold text-slate-300">دسته‌بندی موضوعی</label>
                                        <button
                                            type="button"
                                            onClick={() => setCategoryModalOpen(true)}
                                            className="inline-flex items-center gap-1 text-[11px] font-bold text-brand-400 hover:text-brand-300 transition-colors"
                                        >
                                            <Plus size={13} />
                                            <span>دسته‌بندی جدید</span>
                                        </button>
                                    </div>
                                    <select
                                        value={data.category_id}
                                        onChange={(e) => setData('category_id', e.target.value)}
                                        className="w-full px-4 py-3 rounded-2xl bg-slate-950/70 border border-slate-800 text-sm text-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all"
                                    >
                                        <option value="">بدون دسته‌بندی (عمومی)</option>
                                        {localCategories.map((cat) => (
                                            <option key={cat.id} value={cat.id}>
                                                {cat.name}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.category_id && <p className="text-xs text-rose-400 mt-1">{errors.category_id}</p>}
                                </div>
                            </div>
                        </div>

                        {/* Section 2: PDF Document Upload */}
                        <div className="p-6 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-6">
                            <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                                        <UploadCloud size={20} />
                                    </div>
                                    <div>
                                        <h2 className="text-base font-bold text-white">
                                            فایل کتاب / سند PDF {!isEdit && <span className="text-rose-400">*</span>}
                                        </h2>
                                        <p className="text-xs text-slate-400">آپلود نسخه دیجیتال کتاب (حداکثر ۵۰ مگابایت)</p>
                                    </div>
                                </div>
                                <span className="text-xs font-mono px-2.5 py-1 rounded-xl bg-indigo-950/50 text-indigo-300 border border-indigo-500/30">
                                    PDF Max 50MB
                                </span>
                            </div>

                            <input
                                ref={pdfInputRef}
                                type="file"
                                accept=".pdf,application/pdf"
                                onChange={handlePdfChange}
                                className="hidden"
                            />

                            {/* PDF Dropzone / File Status */}
                            <div
                                onClick={() => pdfInputRef.current?.click()}
                                className={`p-6 rounded-2xl border-2 border-dashed transition-all cursor-pointer text-center space-y-3 ${
                                    data.pdf_file || pdfFileName
                                        ? 'border-indigo-500/50 bg-indigo-950/10'
                                        : errors.pdf_file
                                        ? 'border-rose-500/50 bg-rose-950/10'
                                        : 'border-slate-800 hover:border-indigo-500/40 bg-slate-950/40 hover:bg-slate-950/70'
                                }`}
                            >
                                <div className="w-14 h-14 rounded-2xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center mx-auto shadow-md">
                                    <FileText size={28} />
                                </div>

                                {data.pdf_file ? (
                                    <div className="space-y-1">
                                        <p className="text-sm font-bold text-emerald-400 flex items-center justify-center gap-1.5">
                                            <Check size={16} />
                                            <span>فایل جدید انتخاب شد: {data.pdf_file.name}</span>
                                        </p>
                                        <p className="text-xs text-slate-400">
                                            حجم: {(data.pdf_file.size / (1024 * 1024)).toFixed(2)} مگابایت — برای تغییر کلیک کنید
                                        </p>
                                    </div>
                                ) : pdfFileName ? (
                                    <div className="space-y-1">
                                        <p className="text-sm font-bold text-white">فایل فعلی: {pdfFileName}</p>
                                        <p className="text-xs text-slate-400">
                                            برای جایگزینی با فایل جدید روی این کادر کلیک کنید.
                                        </p>
                                        {book?.pdf_url && (
                                            <div className="pt-2">
                                                <a
                                                    href={book.pdf_url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    onClick={(e) => e.stopPropagation()}
                                                    className="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-600/30 text-indigo-300 hover:bg-indigo-600/50 text-xs font-semibold"
                                                >
                                                    <Download size={13} />
                                                    <span>دانلود و بررسی فایل موجود</span>
                                                </a>
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <div className="space-y-1">
                                        <p className="text-sm font-bold text-slate-200">
                                            برای انتخاب فایل PDF اینجا کلیک کنید یا فایل را بکشید و رها کنید
                                        </p>
                                        <p className="text-xs text-slate-500">فرمت مجاز: PDF | حداکثر اندازه مجاز: ۵۰MB</p>
                                    </div>
                                )}
                            </div>
                            {errors.pdf_file && <p className="text-xs text-rose-400">{errors.pdf_file}</p>}
                        </div>

                        {/* Section 3: Description & Content */}
                        <div className="p-6 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
                            <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 rounded-xl bg-teal-500/20 text-teal-400 border border-teal-500/30">
                                        <Sparkles size={20} />
                                    </div>
                                    <div>
                                        <h2 className="text-base font-bold text-white">توضیحات و معرفی کتاب</h2>
                                        <p className="text-xs text-slate-400">چکیده، سرفصل‌ها و توضیحات تکمیلی اثر را بنویسید</p>
                                    </div>
                                </div>
                                <span className="text-[11px] text-slate-500 font-mono">
                                    {data.description ? data.description.length : 0} کاراکتر
                                </span>
                            </div>

                            <div>
                                <textarea
                                    rows={8}
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    placeholder="توضیحات جامع درباره محتوای کتاب، سرفصل‌ها، مخاطبان هدف و پیش‌نیازهای مطالعه..."
                                    className="w-full p-4 rounded-2xl bg-slate-950/70 border border-slate-800 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all leading-relaxed"
                                />
                                {errors.description && <p className="text-xs text-rose-400 mt-1">{errors.description}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Right 1 Column: Cover Image, Publishing status & Meta */}
                    <div className="space-y-6">
                        {/* Section 4: Cover Image Upload */}
                        <div className="p-6 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
                            <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                                <div className="flex items-center gap-3">
                                    <div className="p-2 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        <Image size={20} />
                                    </div>
                                    <div>
                                        <h2 className="text-base font-bold text-white">تصویر جلد کتاب</h2>
                                        <p className="text-xs text-slate-400">نسبت استاندارد ۲:۳ (پرتره)</p>
                                    </div>
                                </div>
                            </div>

                            <input
                                ref={coverInputRef}
                                type="file"
                                accept="image/jpeg,image/png,image/webp,image/svg+xml"
                                onChange={handleCoverChange}
                                className="hidden"
                            />

                            {/* Cover Preview Box */}
                            <div className="space-y-3">
                                <div
                                    onClick={() => coverInputRef.current?.click()}
                                    className={`relative aspect-[2/3] rounded-2xl overflow-hidden border-2 border-dashed transition-all cursor-pointer flex items-center justify-center group ${
                                        coverPreview
                                            ? 'border-emerald-500/40 bg-slate-950'
                                            : 'border-slate-800 hover:border-emerald-500/40 bg-slate-950/60'
                                    }`}
                                >
                                    {coverPreview ? (
                                        <>
                                            <img
                                                src={coverPreview}
                                                alt="پیش‌نمایش جلد"
                                                className="w-full h-full object-cover transition-transform group-hover:scale-105"
                                            />
                                            <div className="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white gap-2 p-4 text-center">
                                                <Image size={24} />
                                                <span className="text-xs font-bold">برای تغییر تصویر کلیک کنید</span>
                                            </div>
                                        </>
                                    ) : (
                                        <div className="p-6 text-center space-y-2 text-slate-500">
                                            <div className="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto text-slate-400">
                                                <Image size={24} />
                                            </div>
                                            <p className="text-xs font-bold text-slate-300">کلیک جهت انتخاب تصویر جلد</p>
                                            <p className="text-[11px] text-slate-500">
                                                نسبت ۲:۳ (مثلاً ۴۰۰×۶۰۰ یا ۶۰۰×۹۰۰)
                                                <br />
                                                JPG, PNG, WebP تا حداکثر ۴MB
                                            </p>
                                        </div>
                                    )}
                                </div>

                                {coverPreview && (
                                    <button
                                        type="button"
                                        onClick={handleRemoveCover}
                                        className="w-full py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-semibold transition-colors flex items-center justify-center gap-1.5 border border-rose-500/20"
                                    >
                                        <Trash2 size={14} />
                                        <span>حذف تصویر جلد</span>
                                    </button>
                                )}
                            </div>
                            {errors.cover_image && <p className="text-xs text-rose-400">{errors.cover_image}</p>}
                        </div>

                        {/* Section 5: Publishing Status Switcher */}
                        <div className="p-6 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-800/80">
                                <h3 className="text-sm font-bold text-white">وضعیت انتشار</h3>
                                <span
                                    className={`w-2 h-2 rounded-full ${
                                        data.is_published ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'
                                    }`}
                                />
                            </div>

                            <label className="flex items-center justify-between p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 cursor-pointer hover:border-slate-700 transition-all">
                                <div className="space-y-0.5">
                                    <span className="text-xs font-bold text-white">انتشار عمومی</span>
                                    <p className="text-[11px] text-slate-400">نمایش در بخش کتابخانه و مقالات سایت</p>
                                </div>
                                <input
                                    type="checkbox"
                                    checked={data.is_published}
                                    onChange={(e) => setData('is_published', e.target.checked)}
                                    className="rounded-lg border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500 w-5 h-5 cursor-pointer"
                                />
                            </label>
                        </div>

                        {/* Section 6: Meta Stats (in Edit Mode) */}
                        {isEdit && book && (
                            <div className="p-6 rounded-3xl bg-slate-900/70 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
                                <h3 className="text-sm font-bold text-white pb-3 border-b border-slate-800/80">
                                    آمار و اطلاعات ثبت
                                </h3>

                                <div className="space-y-2.5 text-xs">
                                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/50 border border-slate-800">
                                        <span className="text-slate-400 flex items-center gap-1.5">
                                            <Download size={14} className="text-indigo-400" />
                                            مجموع دانلودها
                                        </span>
                                        <span className="font-bold text-indigo-300 font-mono">{book.download_count}</span>
                                    </div>

                                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/50 border border-slate-800">
                                        <span className="text-slate-400 flex items-center gap-1.5">
                                            <Eye size={14} className="text-sky-400" />
                                            مجموع بازدیدها
                                        </span>
                                        <span className="font-bold text-sky-300 font-mono">{book.view_count}</span>
                                    </div>

                                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/50 border border-slate-800 text-[11px]">
                                        <span className="text-slate-400">تاریخ ایجاد:</span>
                                        <span className="font-mono text-slate-300">{book.created_at || '—'}</span>
                                    </div>

                                    <div className="flex items-center justify-between p-2.5 rounded-xl bg-slate-950/50 border border-slate-800 text-[11px]">
                                        <span className="text-slate-400">آخرین تغییر:</span>
                                        <span className="font-mono text-slate-300">{book.updated_at || '—'}</span>
                                    </div>

                                    {book.is_published && (
                                        <div className="pt-2">
                                            <a
                                                href={`/books/${book.slug}`}
                                                target="_blank"
                                                rel="noreferrer"
                                                className="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all flex items-center justify-center gap-2 border border-slate-700"
                                            >
                                                <ExternalLink size={14} />
                                                <span>مشاهده صفحه عمومی اثر</span>
                                            </a>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </form>

            {/* Quick Create Category Modal */}
            {categoryModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                    <div className="w-full max-w-md p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl space-y-4">
                        <div className="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div className="flex items-center gap-2.5">
                                <div className="p-2 rounded-xl bg-brand-500/20 text-brand-400">
                                    <FolderPlus size={18} />
                                </div>
                                <h3 className="text-base font-bold text-white">افزودن دسته‌بندی جدید</h3>
                            </div>
                            <button
                                type="button"
                                onClick={() => setCategoryModalOpen(false)}
                                className="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800"
                            >
                                <X size={18} />
                            </button>
                        </div>

                        {catError && (
                            <div className="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs">
                                {catError}
                            </div>
                        )}

                        <div className="space-y-3 text-xs">
                            <div>
                                <label className="block font-bold text-slate-300 mb-1">
                                    نام دسته‌بندی <span className="text-rose-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={newCatName}
                                    onChange={(e) => setNewCatName(e.target.value)}
                                    placeholder="مثال: مهندسی نرم‌افزار، هوش مصنوعی..."
                                    className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/70 border border-slate-800 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20"
                                />
                            </div>

                            <div>
                                <label className="block font-bold text-slate-300 mb-1">توضیحات (اختیاری)</label>
                                <textarea
                                    rows={3}
                                    value={newCatDesc}
                                    onChange={(e) => setNewCatDesc(e.target.value)}
                                    placeholder="توضیح کوتاه درباره این دسته‌بندی..."
                                    className="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/70 border border-slate-800 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500/20"
                                />
                            </div>
                        </div>

                        <div className="flex items-center gap-3 pt-2">
                            <button
                                type="button"
                                onClick={handleCreateCategory}
                                disabled={catLoading}
                                className="flex-1 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold transition-all disabled:opacity-50"
                            >
                                {catLoading ? 'در حال ثبت...' : 'افزودن دسته‌بندی'}
                            </button>
                            <button
                                type="button"
                                onClick={() => setCategoryModalOpen(false)}
                                className="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all"
                            >
                                انصراف
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
