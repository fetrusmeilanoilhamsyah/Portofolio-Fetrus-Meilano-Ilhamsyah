@props(['maxWidth' => 'max-w-6xl', 'description' => null, 'image' => null, 'isProject' => false])
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

        {{-- ═══ HEADER NAVIGASI (Desktop & Mobile) ═══ --}}
    <header 
        class="sticky top-0 z-40 bg-surf border-b border-line w-full"
        x-data="{ 
                        init() {
                if (!sessionStorage.getItem('mobile_sidebar_seen') && window.innerWidth < 1024) {
                    setTimeout(() => { $dispatch('open-mobile-menu'); }, 300);
                    sessionStorage.setItem('mobile_sidebar_seen', '1');
                }
            }
        }"
    >
        <div class="max-w-7xl mx-auto px-4 lg:px-8 h-[64px] flex items-center justify-between">
            {{-- KIRI: Identitas / Logo --}}
            <div class="flex items-center gap-3">
                <a href="{{ app()->getLocale() === 'en' ? route('en.home') : route('home') }}" class="flex items-center gap-2 lg:gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand rounded-sm">
                    @if($siteSetting?->photo)
                        <div class="w-8 h-8 shrink-0 rounded-full overflow-hidden bg-canvas ring-1 ring-line/50 transition-transform group-hover:scale-105">
                            <img src="{{ media_url($siteSetting->photo) }}" alt="{{ public_text($siteSetting->name) }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-full bg-brand/5 text-brand-ink flex items-center justify-center text-xs font-bold shrink-0 ring-1 ring-line/50 transition-transform group-hover:scale-105">
                            {{ strtoupper(substr(public_text($siteSetting?->name) ?? 'PF', 0, 2)) }}
                        </div>
                    @endif
                    <span class="font-bold text-[15px] tracking-tight text-ink group-hover:text-brand-ink transition-colors hidden sm:block">
                        {{ public_text($siteSetting?->name) ?? config('app.name', 'Portofolio') }}
                    </span>
                    <span class="font-bold text-[15px] tracking-tight text-ink group-hover:text-brand-ink transition-colors sm:hidden">
                        {{ explode(' ', public_text($siteSetting?->name) ?? config('app.name', 'Portofolio'))[0] }}
                    </span>
                </a>
            </div>

            {{-- TENGAH: Menu Navigasi Desktop --}}
            <nav class="hidden lg:flex items-center" aria-label="{{ __('ui.aria_main_menu') }}">
                @include('partials.nav-links', ['mobile' => false])
            </nav>

            {{-- KANAN: Tools & Mobile Toggle --}}
            <div class="flex items-center gap-1.5 lg:gap-3">
                {{-- Command Palette Desktop --}}
                <button type="button" @click="$dispatch('open-palette')" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-md text-ink-muted hover:text-ink hover:bg-ink/5 transition-colors" aria-label="{{ __('ui.command_palette') }}" title="Search (⌘K)">
                    <x-svg-icon name="lucide-search" class="w-[18px] h-[18px]" stroke-width="2" />
                </button>
                
                {{-- Pembatas --}}
                <div class="hidden lg:block w-px h-4 bg-line mx-1"></div>

                <div class="flex items-center gap-1">
                    @include('partials.theme-toggle')
                    @include('partials.lang-toggle')
                </div>
                
                {{-- Mobile Hamburger --}}
                <button type="button" @click="$dispatch('open-mobile-menu')" class="lg:hidden p-2 rounded-md text-ink-muted hover:bg-ink/5" aria-label="{{ __('ui.open_menu') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
            </div>
        </div>

    </header>

        {{-- MOBILE DRAWER --}}
        <div id="mobile-menu" x-data="{ open: false }" x-show="open" @open-mobile-menu.window="open = true; $nextTick(() => $refs.mobileMenu.focus())" x-cloak role="dialog" aria-modal="true" aria-label="{{ __('ui.aria_main_menu') }}" @keydown.escape.window="open = false" x-trap.noscroll="open" class="fixed inset-0 z-50 flex lg:hidden">
            <div class="absolute inset-0 bg-black/40" @click="open = false" aria-hidden="true"></div>
            <nav x-ref="mobileMenu" tabindex="-1" class="relative z-10 w-72 flex flex-col h-full overflow-y-auto p-6 gap-6 focus:outline-none bg-surf border-r border-line">
                <button type="button" @click="open = false" class="absolute top-4 right-4 p-2 rounded-md text-ink-muted hover:text-ink hover:bg-ink/5" aria-label="{{ __('ui.close_menu') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
                <div class="flex flex-col items-center text-center gap-3">
                    @if($siteSetting?->photo)
                        <div class="w-24 h-24 shrink-0 rounded-full overflow-hidden bg-canvas ring-1 ring-line/50">
                            <img src="{{ media_url($siteSetting->photo) }}" alt="{{ public_text($siteSetting->name) }}" class="w-full h-full object-cover">
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
                @include('partials.nav-links', ['mobile' => true, 'closeMenu' => true])
                <div class="mt-auto pt-4 border-t border-line flex flex-col gap-4">
                    {{-- Tombol palet perintah --}}
                    <button
                        type="button"
                        @click="$dispatch('open-palette'); open = false"
                        aria-label="{{ __('ui.command_palette') }}"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-semibold rounded-full border border-line text-ink bg-canvas-muted hover:bg-line/50 active:scale-[0.98] transition-all duration-150"
                    >
                        <x-svg-icon name="lucide-command" class="w-3.5 h-3.5" aria-hidden="true" />
                        <span>{{ __('ui.command_palette', ['default' => 'Command Palette']) }}</span>
                    </button>

                    @if($sidebarSocialLinks->isNotEmpty())
                        <div class="flex items-center gap-1 justify-center">
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



    {{-- KONTEN UTAMA --}}
    <main id="main-content" class="flex-1 flex flex-col min-w-0 w-full" tabindex="-1">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 flex-1 flex flex-col">
            <div class="mx-auto w-full {{ $maxWidth }} flex-1">
                {{ $slot }}
            </div>
        </div>

        {{-- FOOTER UTAMA --}}
        <footer class="w-full border-t border-line bg-surf py-8 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 print:hidden">
                <div class="flex-1 w-full flex justify-center md:justify-start text-[13px] text-ink-muted order-3 md:order-1">
                    &copy; {{ date('Y') }} {{ public_text($siteSetting?->name) ?? config('app.name') }}
                </div>
                <div class="flex-1 w-full flex justify-center text-[13px] order-1 md:order-2">
                    <a href="{{ localized_route('cv') }}" class="font-medium text-ink-muted hover:text-brand-ink transition-colors focus:outline-none focus-visible:underline">
                        {{ __('ui.view_cv_web') }} &rarr;
                    </a>
                </div>
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
            </div>
        </footer>
    </main>

    <x-command-palette />

    @stack('scripts')
</body>
</html>
