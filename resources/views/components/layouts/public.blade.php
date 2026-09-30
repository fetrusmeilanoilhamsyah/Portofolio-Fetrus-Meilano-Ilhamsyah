@props(['maxWidth' => 'max-w-3xl'])
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
    <meta name="robots" content="index, follow">
    <title>{{ $title ?? ($siteSetting?->name ?? config('app.name', 'Portofolio')) }}</title>

    {{-- Skrip tema: cegah kedipan sebelum Alpine dimuat. Di-handle dengan nonce di tahap 9 --}}
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
    @stack('head')
</head>
<body class="min-h-dvh flex flex-col bg-canvas text-ink">

    {{-- Tautan lewati-ke-konten --}}
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:rounded-md bg-brand text-brand-fg"
    >
        {{ __('ui.skip_to_content') }}
    </a>

    {{-- ═══ MOBILE: bilah atas ═══ --}}
    <header
        class="lg:hidden flex items-center justify-between px-4 py-3 border-b bg-surf border-line"
        x-data="{ open: false }"
    >
        {{-- Nama singkat --}}
        <a
            href="{{ app()->getLocale() === 'en' ? route('en.home') : route('home') }}"
            class="text-sm font-semibold tracking-tight text-ink"
        >
            {{ $siteSetting?->name ?? config('app.name', 'Portofolio') }}
        </a>

        {{-- Tombol buka menu --}}
        <button
            type="button"
            @click="open = true; $nextTick(() => $refs.mobileMenu.focus())"
            class="p-2 rounded-md text-ink-muted"
            :aria-expanded="open"
            aria-controls="mobile-menu"
            aria-label="{{ __('ui.open_menu') }}"
        >
            {{-- Lucide: Menu --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <line x1="4" x2="20" y1="12" y2="12"/>
                <line x1="4" x2="20" y1="6" y2="6"/>
                <line x1="4" x2="20" y1="18" y2="18"/>
            </svg>
        </button>

        {{-- ─── Panel geser mobile ─── --}}
        <div
            id="mobile-menu"
            x-show="open"
            x-cloak
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('ui.aria_main_menu') }}"
            @keydown.escape.window="open = false"
            x-trap.noscroll="open"
            class="fixed inset-0 z-50 flex"
        >
            {{-- Overlay --}}
            <div
                class="absolute inset-0 bg-black/40"
                @click="open = false"
                aria-hidden="true"
            ></div>

            {{-- Panel --}}
            <nav
                x-ref="mobileMenu"
                tabindex="-1"
                class="relative z-10 w-72 flex flex-col h-full overflow-y-auto p-6 focus:outline-none bg-surf"
            >
                <div class="flex items-center justify-between mb-6">
                    <span class="font-semibold text-base text-ink">
                        {{ $siteSetting?->name ?? config('app.name', 'Portofolio') }}
                    </span>
                    <button
                        type="button"
                        @click="open = false"
                        class="p-1.5 rounded-md text-ink-muted hover:text-ink hover:bg-black/5 dark:hover:bg-white/10"
                        aria-label="{{ __('ui.close_menu') }}"
                    >
                        {{-- Lucide: X --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                @include('partials.nav-links', ['mobile' => true, 'closeMenu' => true])

                <div class="mt-auto pt-6 border-t border-line flex items-center gap-3">
                    @include('partials.theme-toggle')
                    @include('partials.lang-toggle')
                </div>
            </nav>
        </div>
    </header>

    <div class="flex flex-1">

        {{-- ═══ DESKTOP: sidebar kiri tetap ═══ --}}
        <aside
            class="hidden lg:flex flex-col w-64 xl:w-72 shrink-0 sticky top-0 h-screen overflow-y-auto layout-sidebar bg-surf border-r border-line"
        >
            <div class="flex flex-col flex-1 p-6 gap-6">

                {{-- Identitas --}}
                <div>
                    <a
                        href="{{ app()->getLocale() === 'en' ? route('en.home') : route('home') }}"
                        class="block font-semibold text-lg leading-tight mb-1 text-ink"
                    >
                        {{ $siteSetting?->name ?? config('app.name', 'Portofolio') }}
                    </a>
                    @if($siteSetting)
                        <p class="text-sm text-ink-muted">
                            {{ $siteSetting->getTranslation('role', app()->getLocale(), false) ?: $siteSetting->getTranslation('role', 'id', false) }}
                        </p>
                        @if($siteSetting->open_to_work)
                            <div class="flex items-center gap-1.5 mt-2">
                                <span
                                    class="inline-block w-2 h-2 rounded-full bg-ok"
                                    aria-hidden="true"
                                ></span>
                                <span class="text-xs text-ink-muted font-medium">
                                    {{ __('ui.open_to_work') }}
                                </span>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Toggle bahasa & tema --}}
                <div class="flex items-center gap-2">
                    @include('partials.theme-toggle')
                    @include('partials.lang-toggle')
                </div>

                {{-- Navigasi --}}
                <nav aria-label="{{ __('ui.aria_main_menu') }}">
                    @include('partials.nav-links', ['mobile' => false])
                </nav>

                {{-- Tombol palet perintah (belum berfungsi) --}}
                <div class="mt-auto hidden"> <!-- TODO tahap 9 -->
                    <button
                        type="button"
                        disabled
                        aria-label="{{ __('ui.command_palette') }}"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-md border cursor-not-allowed text-ink-muted border-line bg-canvas"
                    >
                        {{-- Lucide: Command --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" aria-hidden="true">
                            <path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3"/>
                        </svg>
                        <span>{{ __('ui.command_palette') }}</span>
                        <kbd class="ml-auto text-xs font-mono px-1.5 py-0.5 rounded border border-line text-[0.6875rem]">⌘K</kbd>
                    </button>
                </div>
            </div>
        </aside>

        {{-- ═══ KONTEN UTAMA ═══ --}}
        <main
            id="main-content"
            class="flex-1 min-w-0 px-5 py-8 md:px-8 lg:px-12 xl:px-16"
            tabindex="-1"
        >
            <div class="mx-auto w-full {{ $maxWidth }}">
                {{ $slot }}
            </div>
        </main>

    </div>

    @stack('scripts')
</body>
</html>
