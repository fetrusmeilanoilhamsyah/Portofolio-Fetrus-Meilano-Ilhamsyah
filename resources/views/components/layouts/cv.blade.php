@props(['title' => null])
<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data
    x-bind:class="$store.theme.isDark ? 'dark' : ''"
    class="transition-theme"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CV tidak diindeks mesin pencari --}}
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'CV' }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    {{-- Skrip tema: cegah kedipan --}}
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var isDark = saved === 'dark' || (saved === null && prefersDark);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas-muted min-h-screen">

    {{-- Bilah aksi: hanya layar, tersembunyi saat cetak --}}
    <div class="cv-action-bar print:hidden">
        <div class="max-w-[210mm] mx-auto px-4 py-3 flex items-center gap-4">
            {{-- Tautan kembali --}}
            @php
                $backRoute = app()->getLocale() === 'en' ? route('en.about') : route('about');
            @endphp
            <a
                href="{{ $backRoute }}"
                class="inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                {{ __('ui.back', ['default' => 'Kembali']) }}
            </a>

            {{-- LocaleSwitcher --}}
            @php
                $routeName = request()->route()->getName();
                $isEn = str_starts_with($routeName, 'en.');
            @endphp
            <a
                href="{{ $isEn ? route('cv') : route('en.cv') }}"
                class="inline-flex items-center gap-1 text-sm text-ink-muted hover:text-ink transition-colors"
                hreflang="{{ $isEn ? 'id' : 'en' }}"
            >
                {{ $isEn ? 'ID' : 'EN' }}
            </a>

            <div class="ml-auto">
                {{-- Tombol cetak --}}
                <button
                    type="button"
                    x-data
                    @click="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-semibold bg-brand text-brand-fg hover:bg-brand-hover active:scale-[0.98] active:opacity-90 transition-all duration-150"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                    {{ __('ui.cv_print', ['default' => 'Cetak / Simpan PDF']) }}
                </button>
            </div>
        </div>
    </div>

    {{-- Kertas CV --}}
    <div class="cv-paper max-w-[210mm] mx-auto my-6 print:my-0 print:max-w-none">
        {{ $slot }}
    </div>

    @stack('scripts')
</body>
</html>
