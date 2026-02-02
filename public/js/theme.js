/**
 * TechSmart Theme & Language Controller
 */

(function () {
    'use strict';

    // ========================================
    // DARK MODE CONTROLLER
    // ========================================

    const ThemeController = {
        storageKey: 'techsmart_theme',

        init: function () {
            // Load saved theme or default to light
            const savedTheme = localStorage.getItem(this.storageKey) || 'light';
            this.setTheme(savedTheme);

            // Bind toggle button
            const toggleBtn = document.getElementById('themeToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => this.toggle());
            }
        },

        setTheme: function (theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem(this.storageKey, theme);

            // Update toggle icon
            const toggleBtn = document.getElementById('themeToggle');
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                const text = toggleBtn.querySelector('span');

                if (theme === 'dark') {
                    if (icon) {
                        icon.className = 'fa-solid fa-sun';
                    }
                    if (text) {
                        text.textContent = window.LANG?.light_mode || 'Sáng';
                    }
                } else {
                    if (icon) {
                        icon.className = 'fa-solid fa-moon';
                    }
                    if (text) {
                        text.textContent = window.LANG?.dark_mode || 'Tối';
                    }
                }
            }
        },

        toggle: function () {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            this.setTheme(newTheme);
        },

        getTheme: function () {
            return document.documentElement.getAttribute('data-theme') || 'light';
        }
    };

    // ========================================
    // LANGUAGE CONTROLLER
    // ========================================

    const LanguageController = {
        storageKey: 'techsmart_lang',

        init: function () {
            // Bind language buttons
            document.querySelectorAll('[data-lang]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const lang = btn.getAttribute('data-lang');
                    this.setLanguage(lang);
                });
            });
        },

        setLanguage: function (lang) {
            // Save to localStorage
            localStorage.setItem(this.storageKey, lang);

            // Redirect to set session
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('lang', lang);
            window.location.href = currentUrl.toString();
        },

        getLanguage: function () {
            return localStorage.getItem(this.storageKey) || 'vi';
        }
    };

    // ========================================
    // INITIALIZE ON DOM READY
    // ========================================

    document.addEventListener('DOMContentLoaded', function () {
        ThemeController.init();
        LanguageController.init();
    });

    // Export to global scope
    window.ThemeController = ThemeController;
    window.LanguageController = LanguageController;

})();
