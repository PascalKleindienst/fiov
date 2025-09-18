import { fileURLToPath, URL } from 'node:url';
import path from 'path';
// import svgLoader from 'vite-svg-loader';
import { defineConfig } from 'vitepress';
import timelinePlugin from 'vitepress-markdown-timeline';

const baseUrl = 'https://pascalkleindienst.github.io/fiov/';

export default defineConfig({
    // Site metadata
    title: 'Fiov',
    description: 'Financial Overview',
    head: [['link', { rel: 'icon', href: '/logo.svg', type: 'image/svg+xml' }]],
    base: '/fiov/',

    // Theme configuration
    themeConfig: {
        logo: '/logo.svg',

        // i18n configuration
        i18nRouting: true,

        // Site title in the navbar (can be overridden per-locale)
        siteTitle: 'Fiov',

        // Social links (can be overridden per-locale)
        socialLinks: [{ icon: 'github', link: 'https://github.com/pascalkleindienst/fiov' }],

        outline: {
            level: 'deep'
        },

        // Search configuration (can be overridden per-locale)
        search: {
            provider: 'local',
            options: {
                translations: {
                    button: {
                        buttonText: 'Search',
                        buttonAriaLabel: 'Search'
                    },
                    modal: {
                        noResultsText: 'No results for',
                        resetButtonTitle: 'Clear',
                        footer: {
                            selectText: 'to select',
                            navigateText: 'to navigate',
                            closeText: 'to close'
                        }
                    }
                }
            }
        }
    },

    // Locales configuration
    locales: {
        root: {
            label: 'English',
            lang: 'en',
            link: '/',
            title: 'Fiov',
            description: 'Financial Overview',
            themeConfig: {
                // English-specific navigation
                nav: [
                    { text: 'Introduction', link: '/guide/' },
                    { text: 'Usage', link: '/usage/' }
                ],
                // English sidebar configuration
                sidebar: {
                    '/': [
                        {
                            text: 'Introduction',
                            items: [
                                { text: 'Was ist Fiov?', link: '/guide/' },
                                { text: 'Getting Started', link: '/guide/getting-started' },
                                { text: 'Local Development', link: '/guide/local-development' },
                                // { text: 'CLI Commands', link: '/guide/cli-commands' },
                                { text: 'Troubleshooting', link: '/guide/troubleshooting' }
                            ]
                        },
                        {
                            text: 'Usage',
                            items: [
                                { text: 'Wallets', link: '/usage/wallets' },
                                { text: 'Categories', link: '/usage/categories' },
                                { text: 'Transactions', link: '/usage/transactions' },
                                { text: 'Budgets', link: '/usage/budgets' },
                                { text: 'Configuration', link: '/usage/configuration' }
                            ]
                        }
                    ]
                },
                // English-specific footer
                footer: {
                    message: 'Released under the MIT License.',
                    copyright: `Copyright © ${new Date().getFullYear()} Fiov`
                }
            }
        },

        de: {
            label: 'Deutsch',
            lang: 'de',
            link: '/de/',
            title: 'Fiov',
            description: 'Finanzübersicht',
            themeConfig: {
                // German-specific navigation
                nav: [
                    { text: 'Anleitung', link: '/de/guide/' },
                    { text: 'Verwendung', link: '/de/usage/' }
                ],
                // German sidebar configuration
                sidebar: {
                    '/': [
                        {
                            text: 'Anleitung',
                            items: [
                                { text: 'Was ist Fiov?', link: '/de/guide/' },
                                { text: 'Erste Schritte', link: '/de/guide/getting-started' },
                                { text: 'Lokale Entwicklung', link: '/de/guide/local-development' },
                                // { text: 'CLI Commands', link: '/de/guide/cli-commands' },
                                { text: 'Fehlerbehebung', link: '/de/guide/troubleshooting' }
                            ]
                        },
                        {
                            text: 'Verwendung',
                            items: [
                                { text: 'Konten', link: '/de/usage/wallets' },
                                { text: 'Kategorien', link: '/de/usage/categories' },
                                { text: 'Transaktionen', link: '/de/usage/transactions' },
                                { text: 'Budgets', link: '/de/usage/budgets' },
                                { text: 'Einstellungen', link: '/de/usage/configuration' }
                            ]
                        }
                    ]
                },
                // German-specific footer
                footer: {
                    message: 'Veröffentlicht unter der MIT-Lizenz.',
                    copyright: `Copyright © ${new Date().getFullYear()} Fiov`
                },
                // German search translations
                search: {
                    provider: 'local',
                    options: {
                        translations: {
                            button: {
                                buttonText: 'Suchen',
                                buttonAriaLabel: 'Suchen'
                            },
                            modal: {
                                noResultsText: 'Keine Ergebnisse für',
                                resetButtonTitle: 'Zurücksetzen',
                                footer: {
                                    selectText: 'auswählen',
                                    navigateText: 'navigieren',
                                    closeText: 'schließen'
                                }
                            }
                        }
                    }
                }
            }
        }
    },

    // Markdown configuration
    markdown: {
        lineNumbers: true,
        // Configure the MDX components
        config: (md) => {
            md.use(timelinePlugin);
        }
    },

    // Sitemap configuration
    sitemap: {
        hostname: baseUrl
    },

    // Vite configuration
    vite: {
        // plugins: [svgLoader()],
        resolve: {
            alias: {
                '@': fileURLToPath(new URL('../../src', import.meta.url)),
                '@layouts': path.resolve(__dirname, 'layout/'),
                '@assets': path.resolve(__dirname, 'assets/')
            }
        }
    },

    transformHead({ assets }) {
        const font = assets.find(() => /.\w+\.woff2/);

        if (font) {
            return [
                [
                    'link',
                    {
                        rel: 'preload',
                        href: font,
                        as: 'font',
                        type: 'font/woff2',
                        crossorigin: ''
                    }
                ]
            ];
        }
    }
});
