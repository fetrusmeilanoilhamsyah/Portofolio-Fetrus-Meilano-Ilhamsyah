<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class=""
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('ui.error_500_title') }} — {{ config('app.name') }}</title>
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (saved === 'dark' || (saved === null && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh flex flex-col items-center justify-center px-4 bg-canvas text-ink">
    <div class="text-center max-w-md">
        {{-- text-brand diizinkan di sini karena teks 500 berukuran 7xl (besar), sehingga kontras 3:1 (AA Large) sudah cukup --}}
        <p class="text-7xl font-bold mb-4 text-brand">500</p>
        <h1 class="text-xl font-semibold mb-2 text-ink">
            {{ __('ui.error_500_title') }}
        </h1>
        <p class="text-sm mb-8 text-ink-muted">
            {{ __('ui.error_500_body') }}
        </p>
        <a
            href="{{ app()->getLocale() === 'en' ? route('en.home') : route('home') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md bg-brand text-brand-fg hover:bg-brand-hover"
        >
            {{ __('ui.back_home') }}
        </a>
    </div>
</body>
</html>
