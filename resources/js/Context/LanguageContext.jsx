import React, { createContext, useContext, useState, useEffect } from 'react';
import { studentTranslations } from '@/Locales/studentTranslations';

const LanguageContext = createContext();

export function LanguageProvider({ children }) {
    const [lang, setLangState] = useState(() => {
        try {
            return localStorage.getItem('edvora_lang') || 'fa';
        } catch (e) {
            return 'fa';
        }
    });

    const isRtl = lang === 'fa';

    useEffect(() => {
        try {
            localStorage.setItem('edvora_lang', lang);
        } catch (e) {
            // ignore
        }

        if (typeof document !== 'undefined') {
            document.documentElement.lang = lang;
            document.documentElement.dir = isRtl ? 'rtl' : 'ltr';
            if (isRtl) {
                document.body.classList.add('rtl-layout');
            } else {
                document.body.classList.remove('rtl-layout');
            }
        }
    }, [lang, isRtl]);

    const setLang = (newLang) => {
        if (newLang === 'fa' || newLang === 'en') {
            setLangState(newLang);
        }
    };

    const toggleLang = () => {
        setLangState((prev) => (prev === 'fa' ? 'en' : 'fa'));
    };

    const t = (key, fallback = '') => {
        const currentDict = studentTranslations[lang] || {};
        const fallbackDict = studentTranslations.en || {};
        return currentDict[key] ?? fallbackDict[key] ?? fallback ?? key;
    };

    return (
        <LanguageContext.Provider value={{ lang, setLang, toggleLang, isRtl, t }}>
            {children}
        </LanguageContext.Provider>
    );
}

export function useLanguage() {
    const context = useContext(LanguageContext);
    if (!context) {
        // Fallback if used outside provider
        const isRtl = false;
        return {
            lang: 'en',
            setLang: () => {},
            toggleLang: () => {},
            isRtl,
            t: (key, fallback = '') => studentTranslations.en?.[key] ?? fallback ?? key,
        };
    }
    return context;
}
