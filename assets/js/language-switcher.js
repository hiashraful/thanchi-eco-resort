/**
 * Thanchi Eco Resort - Language Switcher
 * Client-side translation system (EN <-> BN)
 *
 * @package Thanchi_Eco_Resort
 */

(function() {
    'use strict';

    var STORAGE_KEY = 'thanchi_language';
    var settings = window.thanchiSettings || {};
    var defaultLang = settings.defaultLanguage || (typeof thanchiLangDefault !== 'undefined' ? thanchiLangDefault : 'en');
    var showSwitcher = settings.showLangSwitcher !== false; // default true
    var currentLang = localStorage.getItem(STORAGE_KEY) || defaultLang;

    /**
     * Get translation for a given English string
     */
    function t(text) {
        if (currentLang === 'en' || !window.THANCHI_TRANSLATIONS || !window.THANCHI_TRANSLATIONS.bn) {
            return text;
        }
        return window.THANCHI_TRANSLATIONS.bn[text] || text;
    }

    /**
     * Find all translatable text nodes in the DOM
     */
    function getTextNodes(root) {
        var walker = document.createTreeWalker(
            root,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    // Skip script, style, noscript, textarea, input elements
                    var parent = node.parentElement;
                    if (!parent) return NodeFilter.FILTER_REJECT;
                    var tag = parent.tagName.toLowerCase();
                    if (tag === 'script' || tag === 'style' || tag === 'noscript' ||
                        tag === 'textarea' || tag === 'code' || tag === 'pre') {
                        return NodeFilter.FILTER_REJECT;
                    }
                    // Only accept nodes with actual visible text
                    if (node.textContent.trim().length > 0) {
                        return NodeFilter.FILTER_ACCEPT;
                    }
                    return NodeFilter.FILTER_REJECT;
                }
            }
        );

        var nodes = [];
        var node;
        while (node = walker.nextNode()) {
            nodes.push(node);
        }
        return nodes;
    }

    /**
     * Store original English text for restoration
     */
    var originalTexts = new Map();

    /**
     * Translate all text in the page
     */
    function translatePage(lang) {
        currentLang = lang;
        localStorage.setItem(STORAGE_KEY, lang);

        var html = document.documentElement;

        if (lang === 'bn') {
            html.setAttribute('lang', 'bn');
            html.classList.add('lang-bn');
            document.body.classList.add('lang-bn');
        } else {
            html.setAttribute('lang', 'en-US');
            html.classList.remove('lang-bn');
            document.body.classList.remove('lang-bn');
        }

        // Translate visible text nodes
        var textNodes = getTextNodes(document.body);
        var translations = (window.THANCHI_TRANSLATIONS && window.THANCHI_TRANSLATIONS.bn) || {};

        textNodes.forEach(function(node) {
            var trimmed = node.textContent.trim();
            if (!trimmed) return;

            // Store original text on first pass
            if (!originalTexts.has(node)) {
                originalTexts.set(node, node.textContent);
            }

            if (lang === 'bn') {
                // Try exact match first
                if (translations[trimmed]) {
                    node.textContent = node.textContent.replace(trimmed, translations[trimmed]);
                    return;
                }
                // Try matching after removing extra whitespace
                var normalized = trimmed.replace(/\s+/g, ' ');
                if (translations[normalized]) {
                    node.textContent = node.textContent.replace(trimmed, translations[normalized]);
                    return;
                }
            } else {
                // Restore original English text
                var original = originalTexts.get(node);
                if (original) {
                    node.textContent = original;
                }
            }
        });

        // Translate placeholders and alt text
        document.querySelectorAll('[placeholder]').forEach(function(el) {
            var key = el.getAttribute('data-placeholder-en') || el.getAttribute('placeholder');
            if (!el.getAttribute('data-placeholder-en')) {
                el.setAttribute('data-placeholder-en', key);
            }
            if (lang === 'bn' && translations[key]) {
                el.setAttribute('placeholder', translations[key]);
            } else if (el.getAttribute('data-placeholder-en')) {
                el.setAttribute('placeholder', el.getAttribute('data-placeholder-en'));
            }
        });

        // Translate aria-labels
        document.querySelectorAll('[aria-label]').forEach(function(el) {
            var key = el.getAttribute('data-aria-en') || el.getAttribute('aria-label');
            if (!el.getAttribute('data-aria-en')) {
                el.setAttribute('data-aria-en', key);
            }
            if (lang === 'bn' && translations[key]) {
                el.setAttribute('aria-label', translations[key]);
            } else if (el.getAttribute('data-aria-en')) {
                el.setAttribute('aria-label', el.getAttribute('data-aria-en'));
            }
        });

        // Translate title attributes
        document.querySelectorAll('[title]').forEach(function(el) {
            var key = el.getAttribute('data-title-en') || el.getAttribute('title');
            if (!el.getAttribute('data-title-en')) {
                el.setAttribute('data-title-en', key);
            }
            if (lang === 'bn' && translations[key]) {
                el.setAttribute('title', translations[key]);
            } else if (el.getAttribute('data-title-en')) {
                el.setAttribute('title', el.getAttribute('data-title-en'));
            }
        });

        // Update toggle UI
        updateToggleUI(lang);

        // Dispatch custom event for other scripts
        document.dispatchEvent(new CustomEvent('thanchi:languageChanged', {
            detail: { language: lang }
        }));
    }

    /**
     * Update the toggle switch UI
     */
    function updateToggleUI(lang) {
        var toggles = document.querySelectorAll('.lang-toggle');
        toggles.forEach(function(toggle) {
            var checkbox = toggle.querySelector('input[type="checkbox"]');
            var enLabel = toggle.querySelector('.lang-label-en');
            var bnLabel = toggle.querySelector('.lang-label-bn');

            if (checkbox) {
                checkbox.checked = (lang === 'bn');
            }
            if (enLabel) {
                enLabel.classList.toggle('opacity-100', lang === 'en');
                enLabel.classList.toggle('opacity-50', lang !== 'en');
                enLabel.classList.toggle('font-bold', lang === 'en');
                enLabel.classList.toggle('font-normal', lang !== 'en');
            }
            if (bnLabel) {
                bnLabel.classList.toggle('opacity-100', lang === 'bn');
                bnLabel.classList.toggle('opacity-50', lang !== 'bn');
                bnLabel.classList.toggle('font-bold', lang === 'bn');
                bnLabel.classList.toggle('font-normal', lang !== 'bn');
            }
        });
    }

    /**
     * Create and inject the language toggle HTML
     */
    function createToggle() {
        var rightActions = document.querySelector('header .flex.items-center.gap-4');
        if (!rightActions) return;

        var toggle = document.createElement('div');
        toggle.className = 'lang-toggle hidden sm:flex items-center gap-2';
        toggle.innerHTML =
            '<span class="lang-label-en text-xs font-bold transition-all cursor-pointer" data-lang="en">EN</span>' +
            '<label class="relative inline-flex items-center cursor-pointer" aria-label="Switch language">' +
                '<input type="checkbox" class="sr-only peer" ' + (currentLang === 'bn' ? 'checked' : '') + '>' +
                '<div class="w-9 h-5 bg-[#3a342e] peer-focus:ring-2 peer-focus:ring-primary/50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>' +
            '</label>' +
            '<span class="lang-label-bn text-xs opacity-50 transition-all cursor-pointer" style="font-family: AlinurBoisakh, sans-serif;" data-lang="bn">\u09AC\u09BE\u0982</span>';

        // Insert before the Book button
        var bookBtn = rightActions.querySelector('a.bg-primary');
        if (bookBtn) {
            rightActions.insertBefore(toggle, bookBtn);
        } else {
            rightActions.insertBefore(toggle, rightActions.firstChild);
        }

        // Also add to mobile menu
        var mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenu) {
            var mobileToggle = document.createElement('div');
            mobileToggle.className = 'lang-toggle flex items-center gap-3 py-2 px-6';
            mobileToggle.innerHTML =
                '<span class="lang-label-en text-sm font-bold transition-all cursor-pointer" data-lang="en">English</span>' +
                '<label class="relative inline-flex items-center cursor-pointer" aria-label="Switch language">' +
                    '<input type="checkbox" class="sr-only peer" ' + (currentLang === 'bn' ? 'checked' : '') + '>' +
                    '<div class="w-11 h-6 bg-[#3a342e] peer-focus:ring-2 peer-focus:ring-primary/50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[3px] after:start-[3px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>' +
                '</label>' +
                '<span class="lang-label-bn text-sm opacity-50 transition-all cursor-pointer" style="font-family: AlinurBoisakh, sans-serif;" data-lang="bn">\u09AC\u09BE\u0982\u09B2\u09BE</span>';

            var mobileContent = mobileMenu.querySelector('.flex.flex-col');
            if (mobileContent) {
                mobileContent.insertBefore(mobileToggle, mobileContent.firstChild);
            }
        }

        // Bind events
        document.querySelectorAll('.lang-toggle').forEach(function(t) {
            var checkbox = t.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    translatePage(this.checked ? 'bn' : 'en');
                });
            }

            // Click on labels
            t.querySelectorAll('[data-lang]').forEach(function(label) {
                label.addEventListener('click', function() {
                    translatePage(this.getAttribute('data-lang'));
                });
            });
        });

        // Set initial UI state
        updateToggleUI(currentLang);
    }

    /**
     * Initialize language system
     */
    function initLanguage() {
        // Only show toggle if admin setting allows it
        if (!showSwitcher) {
            // Still apply default language if Bengali
            if (currentLang === 'bn') {
                translatePage('bn');
            }
            return;
        }
        createToggle();

        // Apply stored language preference immediately (translations.js is a dependency so already loaded)
        if (currentLang !== 'en') {
            translatePage(currentLang);
        }
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanguage);
    } else {
        initLanguage();
    }

    // Expose API for external use
    window.thanchiLanguage = {
        set: translatePage,
        get: function() { return currentLang; },
        translate: t
    };

})();
