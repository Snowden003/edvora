import React, { useState, useEffect } from 'react';
import { Download, Check, Sparkles, X } from 'lucide-react';

export default function PwaInstallButton({ variant = 'topbar', className = '' }) {
    const [installable, setInstallable] = useState(false);
    const [installed, setInstalled] = useState(false);
    const [showHelpModal, setShowHelpModal] = useState(false);

    useEffect(() => {
        // Check if already in standalone mode (already installed app)
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true;
        if (isStandalone) {
            setInstalled(true);
            return;
        }

        if (window.deferredPwaPrompt) {
            setInstallable(true);
        }

        const handlePrompt = () => {
            setInstallable(true);
        };

        const handleInstalled = () => {
            setInstalled(true);
            setInstallable(false);
        };

        window.addEventListener('pwa-prompt-ready', handlePrompt);
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPwaPrompt = e;
            setInstallable(true);
        });
        window.addEventListener('appinstalled', handleInstalled);

        return () => {
            window.removeEventListener('pwa-prompt-ready', handlePrompt);
            window.removeEventListener('appinstalled', handleInstalled);
        };
    }, []);

    const handleInstallClick = async (e) => {
        e.preventDefault();
        e.stopPropagation();

        if (window.deferredPwaPrompt) {
            try {
                window.deferredPwaPrompt.prompt();
                const choiceResult = await window.deferredPwaPrompt.userChoice;
                if (choiceResult.outcome === 'accepted') {
                    setInstalled(true);
                    setInstallable(false);
                }
                window.deferredPwaPrompt = null;
            } catch (err) {
                console.warn('PWA install error:', err);
                setShowHelpModal(true);
            }
        } else {
            // Show guide modal if browser didn't provide native prompt event yet
            setShowHelpModal(true);
        }
    };

    if (installed) {
        if (variant === 'menu') {
            return (
                <div className="flex items-center justify-between p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                    <span className="flex items-center gap-2">
                        <Check size={16} />
                        اپلیکیشن ادورا روی دستگاه شما نصب است
                    </span>
                </div>
            );
        }
        return null;
    }

    return (
        <>
            {variant === 'topbar' ? (
                <button
                    type="button"
                    onClick={handleInstallClick}
                    className={`flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-cyan-500/30 dark:border-cyan-500/30 bg-gradient-to-tr from-cyan-500/10 via-brand-500/10 to-indigo-500/10 hover:from-cyan-500/20 hover:to-indigo-500/20 text-cyan-700 dark:text-cyan-300 font-extrabold text-xs shadow-xs hover:shadow-cyan-500/20 transition-all duration-200 active:scale-95 ${className}`}
                    title="نصب اپلیکیشن وب ادورا"
                >
                    <Download size={14} className="text-cyan-500 animate-bounce" />
                    <span>نصب اپلیکیشن</span>
                </button>
            ) : (
                <button
                    type="button"
                    onClick={handleInstallClick}
                    className={`w-full flex items-center justify-between p-3.5 rounded-2xl bg-gradient-to-r from-cyan-500/15 via-brand-500/15 to-indigo-500/15 border border-cyan-500/30 text-slate-800 dark:text-white hover:border-cyan-400 transition-all shadow-sm active:scale-98 ${className}`}
                >
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-brand-600 flex items-center justify-center text-white shadow-md shadow-brand-500/20">
                            <Download size={20} />
                        </div>
                        <div className="text-right">
                            <p className="text-xs font-black text-slate-800 dark:text-white">نصب نسخه وب‌اپلیکیشن (PWA)</p>
                            <p className="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">اجرای تمام‌صفحه و دسترسی سریع بدون نیاز به مرورگر</p>
                        </div>
                    </div>
                    <span className="px-2.5 py-1 rounded-lg bg-cyan-500 text-white text-[11px] font-extrabold shadow-xs">
                        نصب
                    </span>
                </button>
            )}

            {/* Help / Manual Install Modal */}
            {showHelpModal && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-in fade-in duration-200"
                    onClick={() => setShowHelpModal(false)}
                >
                    <div
                        className="bg-white dark:bg-[#0b1730] border border-slate-200 dark:border-cyan-500/30 rounded-3xl p-6 max-w-sm w-full shadow-2xl text-right animate-in zoom-in-95 duration-200"
                        onClick={(e) => e.stopPropagation()}
                        dir="rtl"
                    >
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div className="flex items-center gap-2">
                                <Sparkles size={18} className="text-cyan-500" />
                                <h3 className="text-sm font-bold text-slate-800 dark:text-white">راهنمای نصب اپلیکیشن</h3>
                            </div>
                            <button
                                type="button"
                                onClick={() => setShowHelpModal(false)}
                                className="p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200"
                            >
                                <X size={16} />
                            </button>
                        </div>

                        <div className="py-4 space-y-3 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            <p>
                                <strong>در مرورگر گوگل کروم (کامپیوتر یا گوشی):</strong>
                                <br />
                                روی آیکون سه‌نقطه یا آیکون دانلود در انتهای نوار آدرس کلیک کنید و گزینه <strong>«Install Edvora»</strong> یا <strong>«افزودن به صفحه اصلی»</strong> را انتخاب فرمایید.
                            </p>
                            <p>
                                <strong>در مرورگر Safari (آیفون / آیپد):</strong>
                                <br />
                                دکمه اشتراک‌گذاری <strong>(Share)</strong> را در پایین صفحه لمس کرده و گزینه <strong>«Add to Home Screen»</strong> را بزنید.
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={() => setShowHelpModal(false)}
                            className="w-full py-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-cyan-500 text-white font-bold text-xs shadow-md"
                        >
                            متوجه شدم
                        </button>
                    </div>
                </div>
            )}
        </>
    );
}
