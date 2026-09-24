import React, { useState, useEffect } from 'react';
import { Head, usePage } from '@inertiajs/react';
import StudentSidebar from '@/Components/Student/StudentSidebar';
import StudentTopbar from '@/Components/Student/StudentTopbar';
import { useLanguage } from '@/Context/LanguageContext';
import { ThemeProvider, useTheme } from '@/Context/ThemeContext';
import { CheckCircle, AlertCircle, Info, X } from 'lucide-react';

function StudentLayoutInner({ children, title }) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false);
    const { flash } = usePage().props;
    const [showFlash, setShowFlash] = useState(true);
    const { isRtl, t } = useLanguage();
    const { theme, isDark } = useTheme();

    useEffect(() => {
        try {
            const saved = localStorage.getItem('studentSidebarCollapsed');
            if (saved === 'true') {
                setIsCollapsed(true);
            }
        } catch (e) {
            // ignore
        }
    }, []);

    useEffect(() => {
        if (flash?.success || flash?.error || flash?.info) {
            setShowFlash(true);
        }
    }, [flash]);

    return (
        <div
            className={`dashboard-wrapper ${theme} flex min-h-screen bg-slate-50 dark:bg-[#030712] text-slate-800 dark:text-slate-100 font-sans antialiased selection:bg-brand-500 selection:text-white transition-colors duration-200 ${isRtl ? 'font-vazir' : ''}`}
            dir={isRtl ? 'rtl' : 'ltr'}
        >
            <Head title={title} />

            {/* Collapsible Student Sidebar */}
            <StudentSidebar
                isOpen={sidebarOpen}
                setIsOpen={setSidebarOpen}
                isCollapsed={isCollapsed}
                setIsCollapsed={setIsCollapsed}
            />

            {/* Main Application Area */}
            <div className="main-content flex-1 flex flex-col min-w-0" id="mainContent">
                {/* Fixed Topbar */}
                <StudentTopbar
                    onToggleSidebar={() => setSidebarOpen(!sidebarOpen)}
                    title={title}
                />

                {/* Flash Messages */}
                {showFlash && (flash?.success || flash?.error || flash?.info) && (
                    <div className="mx-4 sm:mx-8 mt-4">
                        {flash?.success && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <CheckCircle size={20} className="text-emerald-600 shrink-0" />
                                    <span className="font-medium">{flash.success}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-emerald-500 hover:text-emerald-800 p-1"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.error && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-800 text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <AlertCircle size={20} className="text-rose-600 shrink-0" />
                                    <span className="font-medium">{flash.error}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-rose-500 hover:text-rose-800 p-1"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                        {flash?.info && (
                            <div className="flex items-center justify-between p-4 rounded-2xl bg-brand-500/10 border border-brand-500/30 text-brand-800 text-sm shadow-sm animate-in fade-in duration-200">
                                <div className="flex items-center gap-3">
                                    <Info size={20} className="text-brand-600 shrink-0" />
                                    <span className="font-medium">{flash.info}</span>
                                </div>
                                <button
                                    onClick={() => setShowFlash(false)}
                                    className="text-brand-500 hover:text-brand-800 p-1"
                                >
                                    <X size={16} />
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* Page Content Viewport */}
                <main className="flex-1 p-4 sm:p-6 lg:p-8">
                    {children}
                </main>

                {/* Dashboard Footer */}
                <footer className="mt-auto border-t border-slate-200/80 dark:border-slate-800/80 bg-white/50 dark:bg-[#081224]/60 backdrop-blur-xs py-4 px-6 sm:px-8 text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-3 transition-colors">
                    <p>© {new Date().getFullYear()} {t('brand_title')}. {isRtl ? 'تمامی حقوق محفوظ است.' : 'All rights reserved.'}</p>
                    <div className="flex items-center gap-4">
                        <a href="/privacy" className="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            {isRtl ? 'حریم خصوصی' : 'Privacy'}
                        </a>
                        <a href="/terms" className="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            {isRtl ? 'شرایط استفاده' : 'Terms'}
                        </a>
                        <a href="/contact" className="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            {isRtl ? 'تماس با ما' : 'Contact'}
                        </a>
                    </div>
                </footer>
            </div>
        </div>
    );
}

export default function StudentLayout({ children, title = 'Student Dashboard - Edvora Tech' }) {
    return (
        <ThemeProvider>
            <StudentLayoutInner title={title}>
                {children}
            </StudentLayoutInner>
        </ThemeProvider>
    );
}
