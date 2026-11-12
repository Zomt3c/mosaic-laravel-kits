import { Head } from '@inertiajs/react';
import { Moon, Sun } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useAppearance } from '@/hooks/use-appearance';

export default function Welcome() {
    const { t } = useTranslation();
    const { resolvedAppearance, updateAppearance } = useAppearance();
    const isDark = resolvedAppearance === 'dark';

    return (
        <>
            <Head title={t('Welcome')} />
            <div className="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
                <header className="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
                    <nav className="flex items-center justify-end gap-4">
                        <button
                            type="button"
                            onClick={() =>
                                updateAppearance(isDark ? 'light' : 'dark')
                            }
                            aria-label={
                                isDark
                                    ? t('Switch to light mode')
                                    : t('Switch to dark mode')
                            }
                            title={
                                isDark
                                    ? t('Switch to light mode')
                                    : t('Switch to dark mode')
                            }
                            className="inline-flex size-8 items-center justify-center rounded-sm text-[#1b1b18] hover:bg-[#19140010] dark:text-[#EDEDEC] dark:hover:bg-[#ffffff14]"
                        >
                            {isDark ? (
                                <Sun className="size-4" />
                            ) : (
                                <Moon className="size-4" />
                            )}
                        </button>
                    </nav>
                </header>
                <div className="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
                    <main className="flex w-full max-w-[335px] flex-col items-center gap-8 text-center lg:max-w-2xl">
                        <img
                            src="/images/mosaic-banner.png"
                            alt={t(
                                'Mosaic for Laravel + React. Spec it. Test it. Ship it.',
                            )}
                            className="w-full max-w-xl"
                        />
                        <div className="w-full rounded-lg bg-white p-6 text-sm leading-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:p-10 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                            <h1 className="mb-2 text-base font-medium">
                                {t('Your app starts here')}
                            </h1>
                            <p className="mb-4 text-[#58574f] dark:text-[#b5b4ae]">
                                {t(
                                    'Open Claude Code in this project and type:',
                                )}
                            </p>
                            <code className="inline-block rounded-md bg-[#1b1b18] px-4 py-2 font-mono text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                                /mosaic-feature
                            </code>
                            <p className="mt-4 text-[#58574f] dark:text-[#b5b4ae]">
                                {t(
                                    'Describe what you want to build. Mosaic plans it with you, writes the spec and the tests, builds it and checks it.',
                                )}
                            </p>
                        </div>
                    </main>
                </div>
                <div className="hidden h-14.5 lg:block"></div>
            </div>
        </>
    );
}
