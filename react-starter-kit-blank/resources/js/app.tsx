import { createInertiaApp, router } from '@inertiajs/react';
import { initializeTheme } from '@/hooks/use-appearance';
import { followLanguage } from '@/i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
});

// Follow the language Laravel used for every page the user navigates to.
router.on('navigate', (event) => {
    followLanguage(event.detail.page.props.locale);
});

// This will set light / dark mode on load...
initializeTheme();
