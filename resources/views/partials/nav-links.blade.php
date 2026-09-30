@php
    $locale = app()->getLocale();
    $isMobile = $mobile ?? false;

    $links = [
        ['route' => 'home',       'label' => __('ui.nav_home')],
        ['route' => 'about',      'label' => __('ui.nav_about')],
        ['route' => 'experience', 'label' => __('ui.nav_experience')],
        ['route' => 'projects',   'label' => __('ui.nav_projects')],
        ['route' => 'social',     'label' => __('ui.nav_social')],
        ['route' => 'contact',    'label' => __('ui.nav_contact')],
    ];
@endphp

<ul
    class="{{ $isMobile ? 'flex flex-col gap-1' : 'flex flex-col gap-0.5' }}"
    role="list"
>
    @foreach ($links as $link)
        @php
            $url        = localized_route($link['route']);
            $isActive   = request()->routeIs($link['route']) || request()->routeIs('en.' . $link['route']);
        @endphp
        <li>
            <a
                href="{{ $url }}"
                @if($isMobile && ($closeMenu ?? false)) @click="open = false" @endif
                aria-current="{{ $isActive ? 'page' : 'false' }}"
                class="flex items-center gap-2 px-3 py-2 text-sm rounded-md font-medium transition-colors {{ $isActive ? 'text-brand-ink bg-brand/10' : 'text-ink-muted hover:text-ink hover:bg-ink/5' }}"
            >
                @if($isActive)
                    {{-- Indikator aktif --}}
                    <span
                        class="w-1 h-4 rounded-full shrink-0 bg-brand"
                        aria-hidden="true"
                    ></span>
                @else
                    <span class="w-1 h-4 shrink-0" aria-hidden="true"></span>
                @endif
                {{ $link['label'] }}
            </a>
        </li>
    @endforeach
</ul>
