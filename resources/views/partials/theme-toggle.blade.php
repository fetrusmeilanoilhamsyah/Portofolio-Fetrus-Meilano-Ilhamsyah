{{--
    Toggle tema terang/gelap.
    Membaca dari Alpine store $store.theme.isDark.
    Pilihan disimpan di localStorage key 'theme'.
--}}
<button
    type="button"
    x-data
    @click="$store.theme.toggle()"
    :aria-label="$store.theme.isDark ? '{{ __('ui.theme_light') }}' : '{{ __('ui.theme_dark') }}'"
    :title="$store.theme.isDark ? '{{ __('ui.theme_light') }}' : '{{ __('ui.theme_dark') }}'"
    class="relative flex items-center w-16 h-8 rounded-full bg-canvas-muted p-1 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
>
    {{-- Sliding active pill --}}
    <div 
        class="absolute left-1 top-1 bottom-1 w-6 bg-brand rounded-full transition-transform duration-300 ease-out"
        :class="$store.theme.isDark ? 'translate-x-7' : 'translate-x-0'"
    ></div>
    
    {{-- Ikon matahari (Mode Terang) --}}
    <div class="relative z-10 flex-1 flex items-center justify-center transition-colors duration-200" :class="$store.theme.isDark ? 'text-ink-muted hover:text-ink' : 'text-brand-fg'">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
        </svg>
    </div>
    
    {{-- Ikon bulan (Mode Gelap) --}}
    <div class="relative z-10 flex-1 flex items-center justify-center transition-colors duration-200" :class="$store.theme.isDark ? 'text-brand-fg' : 'text-ink-muted hover:text-ink'">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
        </svg>
    </div>
</button>
