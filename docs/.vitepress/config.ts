import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vitepress';
import timelinePlugin from 'vitepress-markdown-timeline';

// https://vitepress.dev/reference/site-config

export default defineConfig({
    // Site metadata
    title: 'Fiov',
    description: 'Financial Overview',

    // Theme configuration
    themeConfig: {
        // i18n configuration
        i18nRouting: true,

        // Site title in the navbar (can be overridden per-locale)
        siteTitle: 'Fiov',

        // Social links (can be overridden per-locale)
        socialLinks: [{ icon: 'github', link: 'https://github.com/pascalkleindienst/fiov' }],

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
                                { text: 'Getting Started', link: '/guide/' },
                                { text: 'Installation', link: '/guide/installation' },
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
                                { text: 'Erste Schritte', link: '/de/guide/' },
                                { text: 'Installation', link: '/de/guide/installation' },
                                { text: 'Local Development', link: '/de/guide/local-development' },
                                // { text: 'CLI Commands', link: '/de/guide/cli-commands' },
                                { text: 'Troubleshooting', link: '/de/guide/troubleshooting' }
                            ]
                        },
                        {
                            text: 'Verwendung',
                            items: [
                                { text: 'Konten', link: '/de/usage/wallets' },
                                { text: 'Kategorien', link: '/de/usage/categories' },
                                { text: 'Transaktionen', link: '/de/usage/transactions' },
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
        hostname: 'https://localhost'
    },

    // Vite configuration
    vite: {
        resolve: {
            alias: {
                '@': fileURLToPath(new URL('../../src', import.meta.url))
            }
        }
    }
});
