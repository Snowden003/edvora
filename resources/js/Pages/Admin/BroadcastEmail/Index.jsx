import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Mail,
    Send,
    Users,
    GraduationCap,
    UserCheck,
    Image as ImageIcon,
    Upload,
    ExternalLink,
    Eye,
    CheckCircle2,
    AlertCircle,
    Clock,
    Sparkles,
    RefreshCw,
    X,
    FileText,
    Link2
} from 'lucide-react';

export default function BroadcastEmailIndex({ stats, history, defaultBanner }) {
    const [previewTab, setPreviewTab] = useState('desktop'); // desktop or mobile
    const [bannerPreview, setBannerPreview] = useState(defaultBanner);
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [useCustomUrl, setUseCustomUrl] = useState(false);
    const [enableCta, setEnableCta] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        subject: '',
        preheader: '',
        body: '',
        target_audience: 'students',
        banner_file: null,
        banner_url: '',
        cta_text: '',
        cta_url: '',
    });

    const testForm = useForm({
        subject: '',
        preheader: '',
        body: '',
        banner_file: null,
        banner_url: '',
        cta_text: '',
        cta_url: '',
    });

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('banner_file', file);
            const reader = new FileReader();
            reader.onloadend = () => {
                setBannerPreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleRemoveBanner = () => {
        setData('banner_file', null);
        setData('banner_url', '');
        setBannerPreview(defaultBanner);
    };

    const handleInsertVariable = (variable) => {
        setData('body', (prev) => prev + ' ' + variable + ' ');
    };

    const handleSendTest = (e) => {
        e.preventDefault();
        testForm.setData({
            subject: data.subject || 'ایمیل آزمایشی ادورا تک',
            preheader: data.preheader,
            body: data.body || 'این یک متن نمونه جهت تست ظاهر ایمیل است.',
            banner_file: data.banner_file,
            banner_url: data.banner_url,
            cta_text: enableCta ? data.cta_text : '',
            cta_url: enableCta ? data.cta_url : '',
        });

        testForm.post('/admin/broadcast-emails/test', {
            preserveScroll: true,
        });
    };

    const handleSendBroadcast = (e) => {
        e.preventDefault();
        setShowConfirmModal(false);
        post('/admin/broadcast-emails/send', {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setBannerPreview(defaultBanner);
                setEnableCta(false);
            },
        });
    };

    const currentRecipientCount =
        data.target_audience === 'students'
            ? stats?.students_count || 0
            : data.target_audience === 'teachers'
            ? stats?.teachers_count || 0
            : stats?.all_users_count || 0;

    return (
        <AdminLayout title="ارسال ایمیل همگانی">
            <Head title="ارسال ایمیل همگانی - ادورا تک" />

            <div className="space-y-8 pb-12">
                {/* Header Banner */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-slate-900/40 p-6 sm:p-8 rounded-3xl border border-slate-800/80 backdrop-blur-xl relative overflow-hidden shadow-2xl">
                    <div className="absolute top-0 right-0 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl pointer-events-none -mr-20 -mt-20" />
                    <div className="relative z-10 space-y-2">
                        <div className="flex items-center gap-2">
                            <span className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" />
                            <span className="text-xs font-bold text-brand-400 uppercase tracking-wider">
                                سیستم ارتباطات جمعی ادورا تک
                            </span>
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                            <Mail className="text-brand-400" size={28} />
                            ارسال ایمیل همگانی به شاگردان
                        </h1>
                        <p className="text-slate-400 text-sm max-w-2xl leading-relaxed">
                            متن اطلاعیه، خبرنامه یا رویداد را بنویسید، تصویر یا بنر دلخواه را اضافه کنید و برای تمام دانشجویان فعال یا اعضای وب‌سایت ارسال نمایید.
                        </p>
                    </div>

                    <div className="relative z-10 flex items-center gap-3">
                        <button
                            onClick={() => router.reload()}
                            className="p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white transition-all border border-slate-700/60 shadow-md"
                            title="بروزرسانی داده‌ها"
                        >
                            <RefreshCw size={18} />
                        </button>
                    </div>
                </div>

                {/* Live Stats Row */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 backdrop-blur-lg flex items-center justify-between">
                        <div className="space-y-1">
                            <span className="text-xs font-bold text-slate-400">دانشجویان فعال</span>
                            <h3 className="text-2xl font-black text-white">{stats?.students_count?.toLocaleString('fa-IR') || 0} نفر</h3>
                        </div>
                        <div className="w-12 h-12 rounded-xl bg-brand-500/15 border border-brand-500/30 flex items-center justify-center text-brand-400">
                            <GraduationCap size={24} />
                        </div>
                    </div>

                    <div className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 backdrop-blur-lg flex items-center justify-between">
                        <div className="space-y-1">
                            <span className="text-xs font-bold text-slate-400">اساتید و مدرسان</span>
                            <h3 className="text-2xl font-black text-white">{stats?.teachers_count?.toLocaleString('fa-IR') || 0} نفر</h3>
                        </div>
                        <div className="w-12 h-12 rounded-xl bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-purple-400">
                            <UserCheck size={24} />
                        </div>
                    </div>

                    <div className="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-5 backdrop-blur-lg flex items-center justify-between sm:col-span-2 lg:col-span-1">
                        <div className="space-y-1">
                            <span className="text-xs font-bold text-slate-400">کل اعضای وب‌سایت</span>
                            <h3 className="text-2xl font-black text-white">{stats?.all_users_count?.toLocaleString('fa-IR') || 0} نفر</h3>
                        </div>
                        <div className="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                            <Users size={24} />
                        </div>
                    </div>
                </div>

                {/* Composer Form & Live Preview Grid */}
                <div className="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
                    {/* Left: Compose Form (7 cols) */}
                    <div className="xl:col-span-7 bg-slate-900/70 border border-slate-800/80 rounded-3xl p-6 sm:p-8 backdrop-blur-xl space-y-6 shadow-xl">
                        <div className="flex items-center justify-between border-b border-slate-800/80 pb-4">
                            <h2 className="text-lg font-extrabold text-white flex items-center gap-2.5">
                                <FileText className="text-brand-400" size={20} />
                                تنظیم و نگارش ایمیل
                            </h2>
                            <span className="text-xs font-semibold px-3 py-1 rounded-full bg-brand-500/10 text-brand-300 border border-brand-500/20">
                                {currentRecipientCount.toLocaleString('fa-IR')} گیرنده انتخاب‌شده
                            </span>
                        </div>

                        <form onSubmit={(e) => { e.preventDefault(); setShowConfirmModal(true); }} className="space-y-6">
                            {/* Target Audience Selector */}
                            <div className="space-y-2">
                                <label className="text-xs font-bold text-slate-300">مخاطبان هدف (گیرندگان):</label>
                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <button
                                        type="button"
                                        onClick={() => setData('target_audience', 'students')}
                                        className={`p-3.5 rounded-2xl border text-right transition-all flex flex-col gap-1 ${
                                            data.target_audience === 'students'
                                                ? 'bg-brand-500/20 border-brand-500/60 text-white shadow-md'
                                                : 'bg-slate-800/40 border-slate-800 text-slate-400 hover:text-slate-200 hover:bg-slate-800/80'
                                        }`}
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-bold">دانشجویان فعال</span>
                                            <GraduationCap size={18} />
                                        </div>
                                        <span className="text-xs text-slate-400">{stats?.students_count || 0} نفر</span>
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() => setData('target_audience', 'teachers')}
                                        className={`p-3.5 rounded-2xl border text-right transition-all flex flex-col gap-1 ${
                                            data.target_audience === 'teachers'
                                                ? 'bg-brand-500/20 border-brand-500/60 text-white shadow-md'
                                                : 'bg-slate-800/40 border-slate-800 text-slate-400 hover:text-slate-200 hover:bg-slate-800/80'
                                        }`}
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-bold">اساتید و مدرسان</span>
                                            <UserCheck size={18} />
                                        </div>
                                        <span className="text-xs text-slate-400">{stats?.teachers_count || 0} نفر</span>
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() => setData('target_audience', 'all')}
                                        className={`p-3.5 rounded-2xl border text-right transition-all flex flex-col gap-1 ${
                                            data.target_audience === 'all'
                                                ? 'bg-brand-500/20 border-brand-500/60 text-white shadow-md'
                                                : 'bg-slate-800/40 border-slate-800 text-slate-400 hover:text-slate-200 hover:bg-slate-800/80'
                                        }`}
                                    >
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-bold">تمام کاربران سایت</span>
                                            <Users size={18} />
                                        </div>
                                        <span className="text-xs text-slate-400">{stats?.all_users_count || 0} نفر</span>
                                    </button>
                                </div>
                            </div>

                            {/* Subject Line */}
                            <div className="space-y-1.5">
                                <label className="text-xs font-bold text-slate-300">
                                    عنوان / موضوع ایمیل (Subject) <span className="text-rose-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.subject}
                                    onChange={(e) => setData('subject', e.target.value)}
                                    placeholder="مثال: شروع دوره جدید برنامه‌نویسی پایتون و هوش مصنوعی در ادورا تک"
                                    className="w-full px-4 py-3 rounded-xl bg-slate-800/60 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all"
                                    required
                                />
                                {errors.subject && <p className="text-xs text-rose-400 font-medium">{errors.subject}</p>}
                            </div>

                            {/* Preheader / Subtitle */}
                            <div className="space-y-1.5">
                                <label className="text-xs font-bold text-slate-300 flex items-center justify-between">
                                    <span>پیش‌متن کوتاه (Preheader - اختیاری)</span>
                                    <span className="text-[11px] text-slate-500">متنی که در لیست اینباکس نمایش داده می‌شود</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.preheader}
                                    onChange={(e) => setData('preheader', e.target.value)}
                                    placeholder="مثال: به جمع دانشجویان این ترم بپیوندید و مهارت‌های جدید بیاموزید..."
                                    className="w-full px-4 py-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all"
                                />
                            </div>

                            {/* Banner Image Upload & Controls */}
                            <div className="space-y-2 p-4 rounded-2xl bg-slate-800/30 border border-slate-800">
                                <div className="flex items-center justify-between">
                                    <label className="text-xs font-bold text-slate-200 flex items-center gap-2">
                                        <ImageIcon className="text-brand-400" size={16} />
                                        تصویر / بنر بالای ایمیل
                                    </label>
                                    <button
                                        type="button"
                                        onClick={() => setUseCustomUrl(!useCustomUrl)}
                                        className="text-xs text-brand-400 hover:text-brand-300 font-medium"
                                    >
                                        {useCustomUrl ? 'آپلود فایل به جای لینک' : 'ورود مستقیم لینک تصویر'}
                                    </button>
                                </div>

                                {!useCustomUrl ? (
                                    <div className="space-y-3">
                                        <div className="flex items-center gap-4">
                                            <label className="flex-1 cursor-pointer flex items-center justify-center gap-3 px-4 py-3.5 rounded-xl border border-dashed border-slate-700 hover:border-brand-500 bg-slate-800/50 hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-all">
                                                <Upload size={18} />
                                                <span className="text-xs font-semibold">
                                                    {data.banner_file ? data.banner_file.name : 'انتخاب تصویر جدید (PNG, JPG, WEBP - حداکثر ۶ مگابایت)'}
                                                </span>
                                                <input
                                                    type="file"
                                                    accept="image/*"
                                                    onChange={handleFileChange}
                                                    className="hidden"
                                                />
                                            </label>

                                            {(data.banner_file || data.banner_url) && (
                                                <button
                                                    type="button"
                                                    onClick={handleRemoveBanner}
                                                    className="p-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all"
                                                    title="حذف و بازگشت به بنر پیش‌فرض"
                                                >
                                                    <X size={18} />
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ) : (
                                    <div className="space-y-1.5">
                                        <input
                                            type="url"
                                            value={data.banner_url}
                                            onChange={(e) => {
                                                setData('banner_url', e.target.value);
                                                if (e.target.value) setBannerPreview(e.target.value);
                                            }}
                                            placeholder="https://example.com/banner.jpg"
                                            className="w-full px-4 py-2.5 rounded-xl bg-slate-800/60 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-brand-500 transition-all"
                                        />
                                    </div>
                                )}
                            </div>

                            {/* Message Body Textarea */}
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <label className="text-xs font-bold text-slate-300">
                                        متن اصلی پیام <span className="text-rose-400">*</span>
                                    </label>
                                    <div className="flex items-center gap-1.5 text-xs text-slate-400">
                                        <span>متغیرهای سریع:</span>
                                        <button
                                            type="button"
                                            onClick={() => handleInsertVariable('{name}')}
                                            className="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-mono text-[11px] border border-slate-700"
                                            title="درج نام دانشجو"
                                        >
                                            {'{name}'}
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleInsertVariable('{email}')}
                                            className="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-brand-300 font-mono text-[11px] border border-slate-700"
                                            title="درج ایمیل دانشجو"
                                        >
                                            {'{email}'}
                                        </button>
                                    </div>
                                </div>

                                <textarea
                                    rows={8}
                                    value={data.body}
                                    onChange={(e) => setData('body', e.target.value)}
                                    placeholder="متن پیام خود را در اینجا بنویسید. پاراگراف‌ها و خطوط شکسته شده دقیقاً به همین صورت برای دانشجو ارسال خواهند شد..."
                                    className="w-full p-4 rounded-xl bg-slate-800/60 border border-slate-700/80 text-white text-sm placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all leading-relaxed"
                                    required
                                />
                                {errors.body && <p className="text-xs text-rose-400 font-medium">{errors.body}</p>}
                            </div>

                            {/* Optional Call to Action Button */}
                            <div className="space-y-3 p-4 rounded-2xl bg-slate-800/30 border border-slate-800">
                                <div className="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        id="enableCtaCheck"
                                        checked={enableCta}
                                        onChange={(e) => setEnableCta(e.target.checked)}
                                        className="w-4 h-4 rounded text-brand-500 bg-slate-800 border-slate-700 focus:ring-brand-500"
                                    />
                                    <label htmlFor="enableCtaCheck" className="text-xs font-bold text-slate-200 cursor-pointer">
                                        افزودن دکمه کلیدواژه و لینک (Call to Action) به انتهای ایمیل
                                    </label>
                                </div>

                                {enableCta && (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                        <div className="space-y-1">
                                            <label className="text-[11px] font-bold text-slate-400">متن روی دکمه:</label>
                                            <input
                                                type="text"
                                                value={data.cta_text}
                                                onChange={(e) => setData('cta_text', e.target.value)}
                                                placeholder="مثال: مشاهده و ثبت‌نام در دوره"
                                                className="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/60 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-brand-500"
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <label className="text-[11px] font-bold text-slate-400">آدرس لینک دکمه (URL):</label>
                                            <input
                                                type="url"
                                                value={data.cta_url}
                                                onChange={(e) => setData('cta_url', e.target.value)}
                                                placeholder="https://edvora.org/courses"
                                                className="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/60 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-brand-500"
                                            />
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Bottom Form Actions */}
                            <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-800">
                                <button
                                    type="button"
                                    onClick={handleSendTest}
                                    disabled={testForm.processing || processing}
                                    className="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs flex items-center justify-center gap-2 border border-slate-700 transition-all"
                                >
                                    <Mail size={16} />
                                    {testForm.processing ? 'در حال ارسال تست...' : 'ارسال آزمایشی به ایمیل خودم'}
                                </button>

                                <button
                                    type="submit"
                                    disabled={processing || !data.subject || !data.body}
                                    className="w-full sm:w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-brand-600 via-brand-500 to-accent-500 hover:from-brand-500 hover:to-accent-400 text-white font-black text-sm flex items-center justify-center gap-2.5 shadow-glow transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <Send size={16} />
                                    {processing ? 'در حال پردازش...' : `ارسال همگانی به ${currentRecipientCount.toLocaleString('fa-IR')} کاربر`}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Right: Live Interactive Email Preview (5 cols) */}
                    <div className="xl:col-span-5 sticky top-6 space-y-4">
                        <div className="bg-slate-900/70 border border-slate-800/80 rounded-3xl p-5 backdrop-blur-xl shadow-xl space-y-4">
                            <div className="flex items-center justify-between border-b border-slate-800 pb-3">
                                <div className="flex items-center gap-2 text-white font-bold text-sm">
                                    <Eye className="text-brand-400" size={18} />
                                    پیش‌نمایش ظاهر ایمیل ارسالی
                                </div>
                                <div className="flex items-center bg-slate-800/80 rounded-lg p-1 border border-slate-700/60">
                                    <button
                                        type="button"
                                        onClick={() => setPreviewTab('desktop')}
                                        className={`px-3 py-1 text-xs font-bold rounded-md transition-all ${
                                            previewTab === 'desktop'
                                                ? 'bg-brand-500 text-white shadow-sm'
                                                : 'text-slate-400 hover:text-white'
                                        }`}
                                    >
                                        دسکتاپ
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setPreviewTab('mobile')}
                                        className={`px-3 py-1 text-xs font-bold rounded-md transition-all ${
                                            previewTab === 'mobile'
                                                ? 'bg-brand-500 text-white shadow-sm'
                                                : 'text-slate-400 hover:text-white'
                                        }`}
                                    >
                                        موبایل
                                    </button>
                                </div>
                            </div>

                            {/* Mock Email Frame */}
                            <div className={`mx-auto transition-all duration-300 ${previewTab === 'mobile' ? 'max-w-[340px]' : 'w-full'}`}>
                                <div className="bg-slate-100 rounded-2xl overflow-hidden shadow-2xl border border-slate-300 text-slate-800 font-sans">
                                    {/* Email Header bar */}
                                    <div className="bg-slate-950 p-4 text-center">
                                        <span className="text-white font-extrabold text-base tracking-wider">EDVORA TECH</span>
                                        <p className="text-[10px] text-slate-400 font-semibold tracking-widest uppercase mt-0.5">Free Digital Education</p>
                                    </div>

                                    {/* Cover Banner Image */}
                                    <div className="w-full bg-slate-900 max-h-48 overflow-hidden">
                                        <img
                                            src={bannerPreview}
                                            alt="Cover Banner"
                                            className="w-full h-44 object-cover"
                                            onError={(e) => { e.target.src = defaultBanner; }}
                                        />
                                    </div>

                                    {/* Email Content */}
                                    <div className="p-6 bg-white space-y-4">
                                        <span className="inline-block px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-700 text-[10px] font-black uppercase tracking-wider">
                                            Official Announcement
                                        </span>

                                        <h2 className="text-base font-black text-slate-900 leading-snug">
                                            {data.subject || 'عنوان ایمیل در اینجا نمایش داده خواهد شد'}
                                        </h2>

                                        <p className="text-xs text-slate-600 font-medium">
                                            Dear <strong>Student Name</strong>,
                                        </p>

                                        <div className="text-xs text-slate-600 leading-relaxed space-y-2 whitespace-pre-wrap">
                                            {data.body
                                                ? data.body.replace('{name}', 'Student Name').replace('{email}', 'student@example.com')
                                                : 'متن پیام شما در اینجا قرار خواهد گرفت و دانشجو آن را به همین صورت دریافت خواهد کرد.'}
                                        </div>

                                        {enableCta && data.cta_text && (
                                            <div className="pt-2 text-center">
                                                <span className="inline-block px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-xs shadow-md">
                                                    {data.cta_text} &rarr;
                                                </span>
                                            </div>
                                        )}

                                        <div className="pt-4 border-t border-slate-100 text-[11px] text-slate-500 space-y-0.5">
                                            <p className="font-bold text-slate-700">The Edvora Tech Team</p>
                                            <p>www.edvora.org</p>
                                        </div>
                                    </div>

                                    {/* Email Footer */}
                                    <div className="bg-slate-50 p-4 border-t border-slate-200 text-center text-[10px] text-slate-400 space-y-1">
                                        <p>You received this email because you are a registered learner on Edvora Tech.</p>
                                        <p>&copy; {new Date().getFullYear()} Edvora Tech. All rights reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Broadcast History Table */}
                <div className="bg-slate-900/70 border border-slate-800/80 rounded-3xl p-6 sm:p-8 backdrop-blur-xl space-y-6 shadow-xl">
                    <div className="flex items-center justify-between border-b border-slate-800/80 pb-4">
                        <h2 className="text-lg font-extrabold text-white flex items-center gap-2.5">
                            <Clock className="text-brand-400" size={20} />
                            تاریخچه پیام‌های همگانی ارسال‌شده
                        </h2>
                        <span className="text-xs text-slate-400 font-medium">
                            {history?.data?.length || 0} کمپین ثبت شده
                        </span>
                    </div>

                    {!history?.data || history.data.length === 0 ? (
                        <div className="text-center py-12 space-y-3">
                            <div className="w-16 h-16 rounded-2xl bg-slate-800/60 border border-slate-700 flex items-center justify-center text-slate-500 mx-auto">
                                <Mail size={28} />
                            </div>
                            <h4 className="text-sm font-bold text-slate-300">هنوز ایمیل همگانی ارسال نشده است</h4>
                            <p className="text-xs text-slate-500 max-w-sm mx-auto">
                                نخستین اطلاعیه یا پیام گروهی خود را از طریق فرم بالا تنظیم و برای شاگردان ارسال کنید.
                            </p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-right text-sm">
                                <thead>
                                    <tr className="border-b border-slate-800 text-slate-400 text-xs font-bold">
                                        <th className="pb-3 px-4">موضوع ایمیل</th>
                                        <th className="pb-3 px-4">مخاطبان</th>
                                        <th className="pb-3 px-4">تعداد دریافت‌کنندگان</th>
                                        <th className="pb-3 px-4">ارسال‌کننده</th>
                                        <th className="pb-3 px-4">تاریخ ارسال</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-800/60">
                                    {history.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-slate-800/30 transition-colors">
                                            <td className="py-4 px-4 font-bold text-white max-w-xs truncate">
                                                {item.subject}
                                            </td>
                                            <td className="py-4 px-4">
                                                <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${
                                                    item.target_audience === 'students'
                                                        ? 'bg-brand-500/15 text-brand-300 border-brand-500/30'
                                                        : item.target_audience === 'teachers'
                                                        ? 'bg-purple-500/15 text-purple-300 border-purple-500/30'
                                                        : 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'
                                                }`}>
                                                    {item.target_audience === 'students' ? 'دانشجویان' : item.target_audience === 'teachers' ? 'اساتید' : 'تمام کاربران'}
                                                </span>
                                            </td>
                                            <td className="py-4 px-4 text-slate-300 font-mono text-xs font-bold">
                                                {item.recipients_count?.toLocaleString('fa-IR')} نفر
                                            </td>
                                            <td className="py-4 px-4 text-slate-400 text-xs font-medium">
                                                {item.sender_name}
                                            </td>
                                            <td className="py-4 px-4 text-slate-400 text-xs font-mono">
                                                {item.sent_at || '-'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>

            {/* Confirmation Modal */}
            {showConfirmModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in">
                    <div className="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-6 shadow-2xl relative">
                        <div className="w-14 h-14 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 mx-auto">
                            <AlertCircle size={28} />
                        </div>

                        <div className="text-center space-y-2">
                            <h3 className="text-lg font-black text-white">تایید ارسال ایمیل همگانی</h3>
                            <p className="text-xs text-slate-400 leading-relaxed">
                                آیا از ارسال این ایمیل به <strong className="text-white">{currentRecipientCount.toLocaleString('fa-IR')}</strong> کاربر اطمینان دارید؟
                                این عملیات پس از شروع غیرقابل بازگشت خواهد بود.
                            </p>
                        </div>

                        <div className="p-4 rounded-xl bg-slate-800/40 border border-slate-800 text-xs space-y-1.5">
                            <div className="flex justify-between text-slate-400">
                                <span>موضوع:</span>
                                <span className="text-white font-bold truncate max-w-[200px]">{data.subject}</span>
                            </div>
                            <div className="flex justify-between text-slate-400">
                                <span>گروه هدف:</span>
                                <span className="text-brand-400 font-bold">
                                    {data.target_audience === 'students' ? 'شاگردان فعال' : data.target_audience === 'teachers' ? 'اساتید' : 'همه کاربران'}
                                </span>
                            </div>
                        </div>

                        <div className="flex items-center gap-3">
                            <button
                                type="button"
                                onClick={() => setShowConfirmModal(false)}
                                className="flex-1 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all border border-slate-700"
                            >
                                انصراف
                            </button>
                            <button
                                type="button"
                                onClick={handleSendBroadcast}
                                className="flex-1 py-3 rounded-xl bg-gradient-to-r from-brand-600 to-accent-500 hover:from-brand-500 hover:to-accent-400 text-white font-black text-xs shadow-glow transition-all flex items-center justify-center gap-2"
                            >
                                <Send size={14} />
                                تایید و ارسال نهایی
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
