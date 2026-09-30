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
    class="p-2 rounded-md transition-colors"
    style="color: var(--ink-muted);"
    onmouseover="this.style.color='var(--ink)'; this.style.backgroundColor='color-mix(in srgb, var(--ink) 8%, transparent)';"
    onmouseout="this.style.color='var(--ink-muted)'; this.style.backgroundColor='transparent';"
>
    {{-- Ikon matahari (mode gelap aktif → tampilkan matahari untuk beralih ke terang) --}}
    <svg x-show="$store.theme.isDark" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2v2"/><path d="M12 20v2"/>
        <path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/>
        <path d="M2 12h2"/><path d="M20 12h2"/>
        <path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
    </svg>
    {{-- Ikon bulan (mode terang aktif → tampilkan bulan untuk beralih ke gelap) --}}
    <svg x-show="!$store.theme.isDark" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
    </svg>
</button>
