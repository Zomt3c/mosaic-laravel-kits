import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

/*
 * Texts of the React pages. Every language is one file in resources/js/locales/<language>.json.
 * The English text is the key, like Laravel's __('…'): t('Log in') shows "Log in" in English
 * and the translation of the other file, and falls back to English when a text is missing.
 * The language is the one Laravel uses for the request (the shared prop "locale"); the files are
 * named like Laravel's language folders (e.g. de.json, pt_BR.json).
 */

const files = import.meta.glob<{ default: Record<string, string> }>(
    './locales/*.json',
    { eager: true },
);

const resources = Object.fromEntries(
    Object.entries(files).map(([path, file]) => [
        path.replace(/^.*\/(.+)\.json$/, '$1'),
        { translation: file.default },
    ]),
);

void i18n.use(initReactI18next).init({
    resources,
    // The page's <html lang> is Laravel's language with a hyphen (pt-BR), the files use Laravel's code (pt_BR).
    lng: document.documentElement.lang.replace('-', '_') || 'en',
    fallbackLng: 'en',
    initAsync: false,
    keySeparator: false,
    nsSeparator: false,
    returnEmptyString: false,
    interpolation: { escapeValue: false },
});

/**
 * Switches the texts to the language Laravel used for a page, e.g. after a navigation.
 */
export function followLanguage(locale: string | undefined): void {
    if (locale && i18n.language !== locale) {
        void i18n.changeLanguage(locale);
        document.documentElement.lang = locale.replace('_', '-');
    }
}

export default i18n;
