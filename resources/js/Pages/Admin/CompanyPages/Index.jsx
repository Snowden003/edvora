import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    FileText,
    Sparkles,
    Save,
    ExternalLink,
    Wand2,
    Copy,
    Check,
    AlertCircle,
    Info,
    RefreshCw,
    Code,
    Sliders,
    CheckCircle2,
    Mic,
    MicOff
} from 'lucide-react';

export default function CompanyPagesIndex({ pagesList, currentPageKey, currentPage, content }) {
    // Form state
    const [pageContent, setPageContent] = useState(content || {});
    const [jsonMode, setJsonMode] = useState(false);
    const [jsonText, setJsonText] = useState(JSON.stringify(content || {}, null, 2));
    const [jsonError, setJsonError] = useState(null);
    const [saving, setSaving] = useState(false);

    // AI Assistant state
    const [aiPrompt, setAiPrompt] = useState('');
    const [aiCurrentText, setAiCurrentText] = useState('');
    const [aiResultText, setAiResultText] = useState('');
    const [aiLoading, setAiLoading] = useState(false);
    const [aiError, setAiError] = useState(null);
    const [copied, setCopied] = useState(false);
    const [isListening, setIsListening] = useState(false);

    // Voice Recorder States
    const [isRecordingMedia, setIsRecordingMedia] = useState(false);
    const [mediaRecorderObj, setMediaRecorderObj] = useState(null);
    const [recordingTime, setRecordingTime] = useState(0);

    // Recording Timer
    useEffect(() => {
        let interval;
        if (isRecordingMedia) {
            interval = setInterval(() => setRecordingTime((prev) => prev + 1), 1000);
        } else {
            setRecordingTime(0);
        }
        return () => clearInterval(interval);
    }, [isRecordingMedia]);

    // Direct MediaRecorder Microphone Capture (Network-error free)
    const toggleMediaAudioRecording = async () => {
        if (isRecordingMedia && mediaRecorderObj) {
            mediaRecorderObj.stop();
            setIsRecordingMedia(false);
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const recorder = new MediaRecorder(stream);
            const chunks = [];

            recorder.ondataavailable = (e) => {
                if (e.data.size > 0) chunks.push(e.data);
            };

            recorder.onstop = async () => {
                const blob = new Blob(chunks, { type: 'audio/webm' });
                stream.getTracks().forEach((track) => track.stop());

                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = async () => {
                    const base64Audio = reader.result;
                    handleSendAudioToGemini(base64Audio, 'audio/webm');
                };
            };

            recorder.start();
            setMediaRecorderObj(recorder);
            setIsRecordingMedia(true);
            setAiError(null);
        } catch (err) {
            console.error('Mic error:', err);
            setAiError('امکان دسترسی به میکروفون نیست: ' + err.message);
        }
    };

    const handleSendAudioToGemini = async (base64Audio, mimeType) => {
        setAiLoading(true);
        setAiError(null);
        setAiResultText('');

        try {
            const response = await fetch('/admin/company-pages/ai/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    page_key: currentPageKey,
                    prompt: aiPrompt || 'لطفاً این فایل صوتی را آنالیز کن و متنی جذاب برای این بخش بنویس.',
                    current_text: aiCurrentText,
                    audio_data: base64Audio,
                    mime_type: mimeType
                })
            });

            const data = await response.json();

            if (data.success) {
                setAiResultText(data.text);
            } else {
                setAiError(data.message || 'خطایی در پردازش صدای اختصاصی Gemini رخ داد.');
            }
        } catch (err) {
            setAiError('خطای ارتباط با سرور: ' + err.message);
        } finally {
            setAiLoading(false);
        }
    };

    // SpeechRecognition (Speech-to-text fallback)
    const toggleVoiceRecording = () => {
        if (!isRecordingMedia) {
            toggleMediaAudioRecording();
        } else {
            toggleMediaAudioRecording();
        }
    };

    useEffect(() => {
        setPageContent(content || {});
        setJsonText(JSON.stringify(content || {}, null, 2));
        setJsonError(null);
    }, [currentPageKey, content]);

    // Handle deep updates to nested content object
    const updateNestedField = (path, value) => {
        const keys = path.split('.');
        const updated = { ...pageContent };
        let current = updated;

        for (let i = 0; i < keys.length - 1; i++) {
            const key = keys[i];
            if (!current[key] || typeof current[key] !== 'object') {
                current[key] = {};
            }
            current[key] = { ...current[key] };
            current = current[key];
        }

        current[keys[keys.length - 1]] = value;
        setPageContent(updated);
        setJsonText(JSON.stringify(updated, null, 2));
    };

    // Save Page Content
    const handleSave = (e) => {
        e.preventDefault();
        setSaving(true);

        let payload = pageContent;
        if (jsonMode) {
            try {
                payload = JSON.parse(jsonText);
                setJsonError(null);
            } catch (err) {
                setJsonError('فرمت کد JSON نامعتبر است: ' + err.message);
                setSaving(false);
                return;
            }
        }

        router.post(`/admin/company-pages/${currentPageKey}`, {
            content: payload
        }, {
            onFinish: () => setSaving(false),
        });
    };

    // Call Gemini AI Endpoint
    const handleGenerateAi = async () => {
        if (!aiPrompt.trim()) return;

        setAiLoading(true);
        setAiError(null);
        setAiResultText('');

        try {
            const response = await fetch('/admin/company-pages/ai/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    page_key: currentPageKey,
                    prompt: aiPrompt,
                    current_text: aiCurrentText
                })
            });

            const data = await response.json();

            if (data.success) {
                setAiResultText(data.text);
            } else {
                setAiError(data.message || 'خطایی در تولید محتوا رخ داد.');
            }
        } catch (err) {
            setAiError('خطای ارتباط با سرور: ' + err.message);
        } finally {
            setAiLoading(false);
        }
    };

    const copyToClipboard = (text) => {
        navigator.clipboard.writeText(text);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    // Render Form Fields as TextAreas
    const renderSectionFields = () => {
        if (!pageContent || Object.keys(pageContent).length === 0) {
            return (
                <div className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 text-center text-slate-400 text-sm">
                    محتوایی برای این صفحه ثبت نشده است. می‌توانید با فرم کد JSON یا Gemini AI متنی تولید کنید.
                </div>
            );
        }

        return Object.entries(pageContent).map(([sectionKey, sectionVal]) => {
            if (typeof sectionVal === 'string' || typeof sectionVal === 'number') {
                return (
                    <div key={sectionKey} className="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
                        <label className="block text-xs font-bold text-brand-400 uppercase tracking-wider">
                            بخش {sectionKey}
                        </label>
                        <textarea
                            rows={3}
                            value={sectionVal ?? ''}
                            onChange={(e) => updateNestedField(sectionKey, e.target.value)}
                            className="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-sm focus:border-brand-500 focus:outline-none leading-relaxed"
                        />
                    </div>
                );
            }

            if (typeof sectionVal === 'object' && sectionVal !== null && !Array.isArray(sectionVal)) {
                return (
                    <div key={sectionKey} className="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-4">
                        <h3 className="text-sm font-extrabold text-white flex items-center gap-2 pb-3 border-b border-slate-800/80 uppercase tracking-wider">
                            <span className="w-2.5 h-2.5 rounded-full bg-brand-400" />
                            بخش {sectionKey}
                        </h3>

                        <div className="space-y-4">
                            {Object.entries(sectionVal).map(([fieldKey, fieldVal]) => {
                                if (typeof fieldVal === 'string' || typeof fieldVal === 'number') {
                                    const rowsCount = fieldKey.includes('text') || fieldKey.includes('subtitle') || fieldKey.includes('description') || fieldKey.includes('content') ? 5 : 2;
                                    return (
                                        <div key={fieldKey}>
                                            <label className="block text-xs font-bold text-slate-300 mb-2">
                                                {fieldKey}
                                            </label>
                                            <textarea
                                                rows={rowsCount}
                                                value={fieldVal ?? ''}
                                                onChange={(e) => updateNestedField(`${sectionKey}.${fieldKey}`, e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-sm focus:border-brand-500 focus:outline-none leading-relaxed"
                                            />
                                        </div>
                                    );
                                }
                                return null;
                            })}
                        </div>
                    </div>
                );
            }

            return null;
        });
    };

    return (
        <AdminLayout title={`مدیریت ${currentPage?.title || 'صفحات عمومی'}`}>
            <Head title={`مدیریت ${currentPage?.title || 'صفحات عمومی'}`} />

            {/* Top Page Banner */}
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-slate-800 shadow-2xl relative overflow-hidden">
                <div className="absolute top-0 left-1/4 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl pointer-events-none" />

                <div className="relative z-10 space-y-1">
                    <div className="flex items-center gap-2 text-xs font-semibold text-brand-400 uppercase tracking-widest">
                        <FileText size={16} />
                        <span>مدیریت صفحات عمومی وب‌سایت</span>
                    </div>
                    <h1 className="text-2xl font-black text-white flex items-center gap-2">
                        {currentPage?.title}
                    </h1>
                    <p className="text-sm text-slate-400 max-w-2xl">
                        {currentPage?.description}
                    </p>
                </div>

                <div className="relative z-10 flex items-center gap-3">
                    <a
                        href={currentPage?.route}
                        target="_blank"
                        rel="noreferrer"
                        className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-sm font-medium border border-slate-700 transition-colors"
                    >
                        <ExternalLink size={16} />
                        <span>مشاهده صفحه زنده</span>
                    </a>

                    <button
                        onClick={handleSave}
                        disabled={saving}
                        className="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white font-bold text-sm shadow-glow transition-all disabled:opacity-50"
                    >
                        {saving ? <RefreshCw size={18} className="animate-spin" /> : <Save size={18} />}
                        <span>ذخیره تغییرات</span>
                    </button>
                </div>
            </div>

            {/* Navigation Tabs for the 5 About Pages */}
            <div className="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-800/80">
                {pagesList.map((p) => {
                    const isActive = p.active;
                    return (
                        <Link
                            key={p.key}
                            href={`/admin/company-pages/${p.key}`}
                            className={`flex items-center gap-2.5 px-4 py-2.5 rounded-2xl text-sm font-bold transition-all whitespace-nowrap ${
                                isActive
                                    ? 'bg-brand-500/20 text-brand-300 border border-brand-500/40 shadow-sm'
                                    : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'
                            }`}
                        >
                            <span>{p.title}</span>
                        </Link>
                    );
                })}
            </div>

            {/* Main Content Grid (Editor + Gemini AI Drawer) */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {/* Left Side: Page Content Form (8 cols) */}
                <div className="lg:col-span-7 space-y-6">

                    {/* Mode Switch Header */}
                    <div className="flex items-center justify-between bg-slate-900/80 p-4 rounded-2xl border border-slate-800">
                        <div className="flex items-center gap-2 text-slate-300 text-sm font-semibold">
                            <Sliders size={18} className="text-brand-400" />
                            <span>ویرایشگر فرمی فیلدها (TextArea)</span>
                        </div>

                        <div className="flex items-center gap-2 bg-slate-950 p-1 rounded-xl border border-slate-800">
                            <button
                                onClick={() => setJsonMode(false)}
                                className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-colors ${
                                    !jsonMode ? 'bg-brand-500 text-white' : 'text-slate-400 hover:text-white'
                                }`}
                            >
                                ویرایشگر متنی (TextArea)
                            </button>
                            <button
                                onClick={() => setJsonMode(true)}
                                className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5 ${
                                    jsonMode ? 'bg-brand-500 text-white' : 'text-slate-400 hover:text-white'
                                }`}
                            >
                                <Code size={14} />
                                <span>کد JSON</span>
                            </button>
                        </div>
                    </div>

                    {/* Form Fields Mode */}
                    {!jsonMode ? (
                        <div className="space-y-6">
                            {renderSectionFields()}

                            {/* Additional JSON editor shortcut info */}
                            <div className="p-4 rounded-xl bg-slate-900/40 border border-slate-800/80 text-xs text-slate-400 flex items-center gap-3">
                                <Info size={16} className="text-brand-400 shrink-0" />
                                <span>
                                    تمام باکس‌های بالا به صورت متنی multi-line (TextArea) قرار گرفته‌اند. برای آرایه‌های پیچیده‌تر می‌توانید از حالت <strong>کد JSON</strong> استفاده فرمایید.
                                </span>
                            </div>
                        </div>
                    ) : (
                        /* RAW JSON Editor Mode */
                        <div className="space-y-3">
                            <textarea
                                rows={24}
                                value={jsonText}
                                onChange={(e) => {
                                    setJsonText(e.target.value);
                                    setJsonError(null);
                                }}
                                className="w-full p-4 rounded-2xl bg-slate-950 border border-slate-800 text-emerald-400 font-mono text-xs focus:border-brand-500 focus:outline-none leading-relaxed shadow-inner"
                            />
                            {jsonError && (
                                <div className="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
                                    <AlertCircle size={16} />
                                    <span>{jsonError}</span>
                                </div>
                            )}
                        </div>
                    )}
                </div>

                {/* Right Side: Gemini AI Assistant Drawer (5 cols) */}
                <div className="lg:col-span-5 space-y-6">
                    <div className="p-6 rounded-3xl bg-gradient-to-b from-slate-900 via-indigo-950/30 to-slate-900 border border-indigo-500/30 shadow-2xl relative space-y-5 sticky top-6">
                        
                        {/* AI Assistant Header */}
                        <div className="flex items-center justify-between pb-4 border-b border-slate-800/80">
                            <div className="flex items-center gap-2.5">
                                <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-500 to-indigo-500 flex items-center justify-center text-white shadow-glow">
                                    <Wand2 size={18} />
                                </div>
                                <div>
                                    <h3 className="text-sm font-black text-white flex items-center gap-1.5">
                                        دستیار هوش مصنوعی Gemini
                                        <span className="text-[10px] font-bold px-1.5 py-0.5 rounded bg-brand-500/20 text-brand-300 border border-brand-500/30">
                                            AI
                                        </span>
                                    </h3>
                                    <p className="text-[11px] text-slate-400">تولید، بازنویسی و غنی‌سازی متن برای {currentPage?.title}</p>
                                </div>
                            </div>
                        </div>

                        {/* AI Prompt Input Form */}
                        <div className="space-y-4">
                            <div>
                                <div className="flex items-center justify-between mb-2">
                                    <label className="text-xs font-bold text-slate-300">
                                        دستور تولید متن برای هوش مصنوعی:
                                    </label>
                                    <button
                                        type="button"
                                        onClick={toggleMediaAudioRecording}
                                        className={`flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold transition-all ${
                                            isRecordingMedia
                                                ? 'bg-rose-500/20 text-rose-300 border border-rose-500/50 animate-pulse'
                                                : 'bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700'
                                        }`}
                                        title={isRecordingMedia ? 'در حال ضبط ویس مستقیم... کلیک جهت توقف و ارسال به Gemini' : 'ضبط مستقیم صدا (بدون نیاز به شبکه مرورگر)'}
                                    >
                                        {isRecordingMedia ? (
                                            <>
                                                <MicOff size={14} className="text-rose-400" />
                                                <span>در حال ضبط ({String(Math.floor(recordingTime / 60)).padStart(2, '0')}:{String(recordingTime % 60).padStart(2, '0')}) - توقف</span>
                                            </>
                                        ) : (
                                            <>
                                                <Mic size={14} className="text-brand-400" />
                                                <span>ضبط ویس (مستقیم)</span>
                                            </>
                                        )}
                                    </button>
                                </div>
                                <textarea
                                    rows={4}
                                    value={aiPrompt}
                                    onChange={(e) => setAiPrompt(e.target.value)}
                                    placeholder="مثلاً: یک متن حماسی، جذاب و الهام‌بخش در مورد ماموریت ادورا تک در آموزش برنامه‌نویسی و هوش مصنوعی بنویس... (یا دکمه ضبط ویس را بزنید)"
                                    className="w-full p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-indigo-500 focus:outline-none leading-relaxed placeholder:text-slate-600"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-300 mb-2">
                                    متن فعلی (جهت بازنویسی):
                                </label>
                                <textarea
                                    rows={3}
                                    value={aiCurrentText}
                                    onChange={(e) => setAiCurrentText(e.target.value)}
                                    placeholder="متن موجود را جهت بازنویسی اینجا قرار دهید..."
                                    className="w-full p-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-300 text-xs focus:border-indigo-500 focus:outline-none placeholder:text-slate-600"
                                />
                            </div>

                            <button
                                onClick={handleGenerateAi}
                                disabled={aiLoading || !aiPrompt.trim()}
                                className="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-brand-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-glow transition-all disabled:opacity-50"
                            >
                                {aiLoading ? (
                                    <>
                                        <RefreshCw size={16} className="animate-spin" />
                                        <span>Gemini در حال تولید محتوا...</span>
                                    </>
                                ) : (
                                    <>
                                        <Sparkles size={16} />
                                        <span>تولید متن با Gemini AI</span>
                                    </>
                                )}
                            </button>
                        </div>

                        {/* AI Error Alert */}
                        {aiError && (
                            <div className="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-start gap-2">
                                <AlertCircle size={16} className="shrink-0 mt-0.5" />
                                <span>{aiError}</span>
                            </div>
                        )}

                        {/* AI Result Box */}
                        {aiResultText && (
                            <div className="space-y-3 pt-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                                        <CheckCircle2 size={15} />
                                        متن خروجی Gemini AI:
                                    </span>
                                    <button
                                        onClick={() => copyToClipboard(aiResultText)}
                                        className="flex items-center gap-1 text-[11px] font-bold text-slate-300 hover:text-white px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 transition-colors"
                                    >
                                        {copied ? <Check size={13} className="text-emerald-400" /> : <Copy size={13} />}
                                        <span>{copied ? 'کپی شد' : 'کپی متن'}</span>
                                    </button>
                                </div>

                                <div className="p-4 rounded-2xl bg-slate-950 border border-emerald-500/30 text-slate-200 text-xs leading-relaxed max-h-60 overflow-y-auto whitespace-pre-wrap">
                                    {aiResultText}
                                </div>
                            </div>
                        )}

                    </div>
                </div>

            </div>
        </AdminLayout>
    );
}
