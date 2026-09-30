@php
    $locale = app()->getLocale();
    $isMobile = $mobile ?? false;

    $links = [
        ['route_id' => 'home',       'route_en' => 'en.home',       'label' => __('ui.nav_home')],
        ['route_id' => 'about',      'route_en' => 'en.about',      'label' => __('ui.nav_about')],
        ['route_id' => 'experience', 'route_en' => 'en.experience', 'label' => __('ui.nav_experience')],
        ['route_id' => 'projects',   'route_en' => 'en.projects',   'label' => __('ui.nav_projects')],
        ['route_id' => 'social',     'route_en' => 'en.social',     'label' => __('ui.nav_social')],
        ['route_id' => 'contact',    'route_en' => 'en.contact',    'label' => __('ui.nav_contact')],
    ];
@endphp

<ul
    class="{{ $isMobile ? 'flex flex-col gap-1' : 'flex flex-col gap-0.5' }}"
    role="list"
>
    @foreach ($links as $link)
        @php
            $routeName  = $locale === 'en' ? $link['route_en'] : $link['route_id'];
            $url        = route($routeName);
            $isActive   = request()->routeIs($link['route_id']) || request()->routeIs($link['route_en']);
        @endphp
        <li>
            <a
                href="{{ $url }}"
                @if($isMobile && ($closeMenu ?? false)) @click="open = false" @endif
                aria-current="{{ $isActive ? 'page' : 'false' }}"
                class="flex items-center gap-2 px-3 py-2 text-sm rounded-md font-medium transition-colors"
                style="
                    color: {{ $isActive ? 'var(--brand)' : 'var(--ink-muted)' }};
                    background-color: {{ $isActive ? 'color-mix(in srgb, var(--brand) 8%, transparent)' : 'transparent' }};
                "
                onmouseover="if(!this.getAttribute('aria-current') || this.getAttribute('aria-current') === 'false') { this.style.color='var(--ink)'; this.style.backgroundColor='color-mix(in srgb, var(--ink) 5%, transparent)'; }"
                onmouseout="if(!this.getAttribute('aria-current') || this.getAttribute('aria-current') === 'false') { this.style.color='var(--ink-muted)'; this.style.backgroundColor='transparent'; }"
            >
                @if($isActive)
                    {{-- Indikator aktif --}}
                    <span
                        class="w-1 h-4 rounded-full shrink-0"
                        style="background-color: var(--brand);"
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
