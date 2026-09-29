import React, { createContext, useContext, useState, useEffect } from 'react';

const ThemeContext = createContext();

export function ThemeProvider({ children }) {
    const [theme, setTheme] = useState(() => {
        try {
            const saved = localStorage.getItem('edvora_theme') || localStorage.getItem('edvora_student_theme');
            if (saved === 'dark' || saved === 'light') return saved;
            return 'dark'; // Premium dark mode by default
        } catch (e) {
            return 'dark';
        }
    });

    useEffect(() => {
        try {
            localStorage.setItem('edvora_theme', theme);
            localStorage.setItem('edvora_student_theme', theme);
            const root = document.documentElement;
            if (theme === 'dark') {
                root.classList.add('dark');
                root.setAttribute('data-theme', 'dark');
            } else {
                root.classList.remove('dark');
                root.setAttribute('data-theme', 'light');
            }
        } catch (e) {
            // ignore
        }
    }, [theme]);

    const toggleTheme = () => {
        setTheme(prev => (prev === 'dark' ? 'light' : 'dark'));
    };

    return (
        <ThemeContext.Provider value={{ theme, isDark: theme === 'dark', setTheme, toggleTheme }}>
            {children}
        </ThemeContext.Provider>
    );
}

export function useTheme() {
    const context = useContext(ThemeContext);
    if (!context) {
        return {
            theme: 'dark',
            isDark: true,
            setTheme: () => {},
            toggleTheme: () => {}
        };
    }
    return context;
}
