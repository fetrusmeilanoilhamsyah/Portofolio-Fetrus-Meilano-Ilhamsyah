{{--
    Toggle bahasa Indonesia / Inggris.
    Mengarahkan ke halaman yang sama dalam bahasa yang dipilih.
    - Bahasa ID: tanpa prefix (/)
    - Bahasa EN: dengan prefix (/en)
--}}
@php
    $locale       = app()->getLocale();
    $currentRoute = request()->route();

    // Tentukan URL untuk bahasa lain dengan membalik locale di rute saat ini.
    // Konvensi nama rute: 'home' (id) ↔ 'en.home' (en), 'about' ↔ 'en.about', dst.
    if ($locale === 'id') {
        $otherLocale   = 'en';
        $otherLabel    = 'EN';
        $otherLongLabel = __('ui.lang_en');
        try {
            $otherName = $currentRoute ? 'en.' . $currentRoute->getName() : 'en.home';
            $otherUrl  = route($otherName);
        } catch (\Throwable) {
            $otherUrl  = route('en.home');
        }
    } else {
        $otherLocale   = 'id';
        $otherLabel    = 'ID';
        $otherLongLabel = __('ui.lang_id');
        try {
            $routeName = $currentRoute ? $currentRoute->getName() : 'home';
            // Hapus prefix 'en.' jika ada
            $baseName  = str_starts_with($routeName, 'en.') ? substr($routeName, 3) : $routeName;
            $otherUrl  = route($baseName);
        } catch (\Throwable) {
            $otherUrl  = route('home');
        }
    }
@endphp

<a
    href="{{ $otherUrl }}"
    lang="{{ $otherLocale }}"
    aria-label="{{ __('ui.toggle_lang') }}: {{ $otherLongLabel }}"
    title="{{ __('ui.toggle_lang') }}: {{ $otherLongLabel }}"
    class="px-2 py-1.5 text-xs font-semibold rounded-md uppercase tracking-wide transition-colors"
    style="color: var(--ink-muted);"
    onmouseover="this.style.color='var(--ink)'; this.style.backgroundColor='color-mix(in srgb, var(--ink) 8%, transparent)';"
    onmouseout="this.style.color='var(--ink-muted)'; this.style.backgroundColor='transparent';"
>
    {{ $otherLabel }}
</a>
