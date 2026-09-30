import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// Plugin focus trap untuk menu mobile
Alpine.plugin(focus);

// ─────────────────────────────────────────────
// Store tema terang/gelap
// ─────────────────────────────────────────────
Alpine.store('theme', {
    isDark: false,

    init() {
        // Baca preferensi yang sudah disimpan, atau ikuti sistem
        const saved = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.isDark = saved === 'dark' || (saved === null && prefersDark);
        this._apply();

        // Ikuti perubahan preferensi sistem (saat tidak ada pilihan tersimpan)
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                this.isDark = e.matches;
                this._apply();
            }
        });
    },

    toggle() {
        this.isDark = !this.isDark;
        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        this._apply();
    },

    _apply() {
        document.documentElement.classList.toggle('dark', this.isDark);
    },
});

window.Alpine = Alpine;
Alpine.start();
