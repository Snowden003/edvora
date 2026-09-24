import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { LanguageProvider } from '@/Context/LanguageContext';

const appName = import.meta.env.VITE_APP_NAME || 'Edvora Tech';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <LanguageProvider>
                <App {...props} />
            </LanguageProvider>
        );
    },
    progress: {
        color: '#0c8de9',
        showSpinner: true,
    },
});
