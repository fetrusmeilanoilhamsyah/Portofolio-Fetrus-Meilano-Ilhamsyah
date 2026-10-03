@props(['maxWidth' => 'max-w-3xl', 'description' => null, 'image' => null, 'isProject' => false])
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
    <title>{{ $title ?? (public_text($siteSetting?->name) ?? config('app.name', 'Portofolio')) }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon-32x32.png') }}" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @php
        $metaDesc = $description ?? public_text($siteSetting?->intro);
    @endphp
    @if($metaDesc)
        <meta name="description" content="{{ Str::limit(strip_tags($metaDesc), 160) }}">
    @endif

    {{-- Canonical & View Transitions --}}
    
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Hreflang --}}
    @php
        $routeName = request()->route()->getName();
        $isEn = str_starts_with($routeName, 'en.');
        $idRouteName = $isEn ? substr($routeName, 3) : $routeName;
        $enRouteName = 'en.' . $idRouteName;
        $params = request()->route()->parameters();
    @endphp
    @if(Route::has($idRouteName) && Route::has($enRouteName))
        <link rel="alternate" hreflang="id" href="{{ route($idRouteName, $params) }}">
        <link rel="alternate" hreflang="en" href="{{ route($enRouteName, $params) }}">
        <link rel="alternate" hreflang="x-default" href="{{ route($idRouteName, $params) }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $title ?? (public_text($siteSetting?->name) ?? config('app.name', 'Portofolio')) }}">
    @if($metaDesc)
        <meta property="og:description" content="{{ Str::limit(strip_tags($metaDesc), 160) }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="{{ $isProject ? 'article' : 'website' }}">
    @php
        $ogImage = $image ? media_url($image) : ($siteSetting?->og_image ? media_url($siteSetting->og_image) : null);
        if ($ogImage && !str_starts_with($ogImage, 'http://') && !str_starts_with($ogImage, 'https://')) {
            $ogImage = url($ogImage);
        }
    @endphp
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? (public_text($siteSetting?->name) ?? config('app.name', 'Portofolio')) }}">
    @if($metaDesc)
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($metaDesc), 160) }}">
    @endif
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

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
        x-data="{ 
            open: false,
            init() {
                if (!sessionStorage.getItem('mobile_sidebar_seen') && window.innerWidth < 1024) {
                    setTimeout(() => { this.open = true; }, 300);
                    sessionStorage.setItem('mobile_sidebar_seen', '1');
                }
            }
        }"
    >
        {{-- Nama singkat --}}
        <a
            href="{{ app()->getLocale() === 'en' ? route('en.home') : route('home') }}"
            class="text-sm font-bold tracking-tight text-brand-ink"
        >
            {{ public_text($siteSetting?->name) ?? config('app.name', 'Portofolio') }}
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
                class="relative z-10 w-72 flex flex-col h-full overflow-y-auto p-6 gap-6 focus:outline-none bg-surf"
            >
                {{-- Close Button --}}
                <button
                    type="button"
                    @click="open = false"
                    class="absolute top-4 right-4 p-2 rounded-md text-ink-muted hover:text-ink hover:bg-ink/5"
                    aria-label="{{ __('ui.close_menu') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>

                <div class="flex flex-col items-center text-center gap-3">
                    {{-- Identitas --}}
                    @if($siteSetting?->photo)
                        <div class="w-28 h-28 shrink-0 rounded-lg overflow-hidden bg-canvas ring-1 ring-line/50">
                            <img src="{{ media_url($siteSetting->photo) }}" alt="{{ public_text($siteSetting->name) }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-28 h-28 rounded-lg bg-brand/5 text-brand-ink flex items-center justify-center text-xl font-bold shrink-0 ring-1 ring-line/50">
                            {{ strtoupper(substr(public_text($siteSetting?->name) ?? 'PF', 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <span class="block font-bold text-lg leading-tight mb-0.5 text-ink">
                            {{ public_text($siteSetting?->name) ?? config('app.name', 'Portofolio') }}
                        </span>
                        
                        @if($siteSetting?->cv_file)
                            <div class="mt-2 flex justify-center">
                                <a href="{{ media_url($siteSetting->cv_file) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold tracking-wide uppercase rounded-full bg-brand/10 text-brand-ink hover:bg-brand/20 transition-colors focus:outline-none">
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    {{ __('ui.download_cv', ['default' => 'Unduh CV']) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Toggle bahasa & tema --}}
                <div class="flex items-center gap-3">
                    @include('partials.theme-toggle')
                    @include('partials.lang-toggle')
                </div>

                @include('partials.nav-links', ['mobile' => true, 'closeMenu' => true])

                <div class="mt-auto pt-4 border-t border-line flex flex-col gap-3">
                    {{-- Ikon akun --}}
                    @if($sidebarSocialLinks->isNotEmpty())
                        <div class="flex items-center gap-1">
                            @foreach($sidebarSocialLinks as $sLink)
                                <a href="{{ $sLink->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-9 h-9 rounded-md transition-colors text-ink-muted hover:text-ink hover:bg-ink/5" aria-label="{{ public_text($sLink->label) }}" title="{{ public_text($sLink->label) }}">
                                    <x-svg-icon :name="$sLink->icon" class="w-4 h-4" />
                                </a>
                            @endforeach
                        </div>
                    @endif

                </div>
            </nav>
        </div>
    </header>

    <div class="flex flex-1">

        {{-- ═══ DESKTOP: sidebar kiri tetap ═══ --}}
        <aside
            class="hidden lg:flex flex-col w-[250px] shrink-0 sticky top-0 h-screen overflow-y-auto layout-sidebar bg-surf border-r border-line"
        >
            <div class="flex flex-col flex-1 p-5 gap-5">

                {{-- Identitas --}}
                <div class="flex flex-col items-center text-center gap-4">
                    @if($siteSetting?->photo)
                        <div class="w-24 h-24 shrink-0 rounded-lg overflow-hidden bg-canvas ring-1 ring-line/50">
                            <img src="{{ media_url($siteSetting->photo) }}" alt="{{ public_text($siteSetting->name) }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-24 h-24 rounded-lg bg-brand/5 text-brand-ink flex items-center justify-center text-2xl font-bold shrink-0 ring-1 ring-line/50">
                            {{ strtoupper(substr(public_text($siteSetting?->name) ?? 'PF', 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <a
                            href="{{ app()->getLocale() === 'en' ? route('en.home') : route('home') }}"
                            class="block font-bold text-lg leading-tight mb-1 text-brand-ink"
                        >
                            {{ public_text($siteSetting?->name) ?? config('app.name', 'Portofolio') }}
                        </a>
                        
                        @if($siteSetting?->cv_file)
                            <div class="mt-2 flex justify-center">
                                <a href="{{ media_url($siteSetting->cv_file) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold tracking-wide uppercase rounded-full bg-brand/10 text-brand-ink hover:bg-brand/20 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-brand">
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    {{ __('ui.download_cv', ['default' => 'Unduh CV']) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Toggle bahasa & tema --}}
                <div class="flex items-center gap-3">
                    @include('partials.theme-toggle')
                    @include('partials.lang-toggle')
                </div>

                {{-- Navigasi --}}
                <nav aria-label="{{ __('ui.aria_main_menu') }}">
                    @include('partials.nav-links', ['mobile' => false])
                </nav>

                {{-- Bagian bawah sidebar --}}
                <div class="mt-auto pt-4 border-t border-line flex flex-col gap-3">
                    {{-- Ikon akun --}}
                    @if($sidebarSocialLinks->isNotEmpty())
                        <div class="flex items-center gap-1">
                            @foreach($sidebarSocialLinks as $sLink)
                                <a href="{{ $sLink->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-9 h-9 rounded-md transition-colors text-ink-muted hover:text-ink hover:bg-ink/5" aria-label="{{ public_text($sLink->label) }}" title="{{ public_text($sLink->label) }}">
                                    <x-svg-icon :name="$sLink->icon" class="w-4 h-4" />
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Tombol palet perintah --}}
                    <button
                        type="button"
                        @click="$dispatch('open-palette')"
                        aria-label="{{ __('ui.command_palette') }}"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-md border cursor-pointer text-ink-muted border-line bg-canvas hover:bg-ink/5 active:scale-95 transition-all duration-150"
                    >
                        <x-svg-icon name="lucide-command" class="w-3.5 h-3.5" aria-hidden="true" />
                        <span>{{ __('ui.command_palette', ['default' => 'Command Palette']) }}</span>
                        <kbd class="ml-auto text-xs font-mono px-1.5 py-0.5 rounded border border-line text-[0.6875rem]">⌘K</kbd>
                    </button>
                </div>
            </div>
        </aside>

        {{-- ═══ KONTEN UTAMA ═══ --}}
        <main
            id="main-content"
            class="flex-1 flex flex-col min-w-0 px-6 py-8 md:px-8 lg:px-12 xl:px-16"
            tabindex="-1"
        >
            <div class="mx-auto w-full {{ $maxWidth }} flex-1">
                {{ $slot }}
            </div>

            <footer class="mx-auto w-full {{ $maxWidth }} mt-20 pt-8 pb-8 border-t border-line flex flex-col md:flex-row items-center justify-between gap-6 print:hidden">
                
                {{-- KIRI: Hak Cipta --}}
                <div class="flex-1 w-full flex justify-center md:justify-start text-[13px] text-ink-muted order-3 md:order-1">
                    &copy; {{ date('Y') }} {{ public_text($siteSetting?->name) ?? config('app.name') }}
                </div>
                
                {{-- TENGAH: Tautan CV --}}
                <div class="flex-1 w-full flex justify-center text-[13px] order-1 md:order-2">
                    <a href="{{ localized_route('cv') }}" class="font-medium text-ink-muted hover:text-brand-ink transition-colors focus:outline-none focus-visible:underline">
                        {{ __('ui.view_cv_web') }} &rarr;
                    </a>
                </div>

                {{-- KANAN: Sosmed --}}
                <div class="flex-1 w-full flex justify-center md:justify-end order-2 md:order-3">
                    @if($sidebarSocialLinks->isNotEmpty())
                        <div class="flex items-center gap-4">
                            @foreach($sidebarSocialLinks as $sLink)
                                <a href="{{ $sLink->url }}" target="_blank" rel="noopener noreferrer" class="text-ink-muted hover:text-brand-ink transition-colors" aria-label="{{ public_text($sLink->label) }}" title="{{ public_text($sLink->label) }}">
                                    <x-svg-icon :name="$sLink->icon" class="w-4 h-4" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

            </footer>
        </main>

    </div>
    
    <x-command-palette />

    @stack('scripts')
</body>
</html>
