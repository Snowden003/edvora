import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx,ts,tsx}',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Vazirmatn', 'Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                vazir: ['Vazirmatn', 'sans-serif'],
                display: ['Plus Jakarta Sans', 'Vazirmatn', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0f7ff',
                    100: '#e0effe',
                    200: '#bae0fd',
                    300: '#7cc5fb',
                    400: '#36a7f7',
                    500: '#0c8de9',
                    600: '#016ec7',
                    700: '#0258a1',
                    800: '#064b85',
                    900: '#0b3f6f',
                    950: '#07284a',
                },
                accent: {
                    500: '#8b5cf6',
                    600: '#7c3aed',
                }
            },
            boxShadow: {
                'glow': '0 0 25px -5px rgba(12, 141, 233, 0.3)',
                'glow-purple': '0 0 25px -5px rgba(139, 92, 246, 0.3)',
                'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
            }
        },
    },

    plugins: [forms],
};
