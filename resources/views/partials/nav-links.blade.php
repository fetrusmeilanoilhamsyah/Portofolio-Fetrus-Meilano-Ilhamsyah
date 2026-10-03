@php
    $locale = app()->getLocale();
    $isMobile = $mobile ?? false;

    $links = [
        ['route' => 'home',       'label' => __('ui.nav_home'),       'icon' => 'home'],
        ['route' => 'about',      'label' => __('ui.nav_about'),      'icon' => 'user'],
        ['route' => 'experience', 'label' => __('ui.nav_experience'), 'icon' => 'briefcase'],
        ['route' => 'projects',   'label' => __('ui.nav_projects'),   'icon' => 'folder'],
        ['route' => 'social',     'label' => __('ui.nav_social'),     'icon' => 'share-2'],
        ['route' => 'contact',    'label' => __('ui.nav_contact'),    'icon' => 'mail'],
    ];
@endphp

<ul
    class="flex flex-col gap-3"
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
                class="flex items-center gap-3 px-3 h-[40px] text-sm rounded-md font-medium transition-all duration-200 ease-out active:scale-[0.98] active:opacity-80 focus-visible:ring-2 focus-visible:ring-brand focus-visible:outline-none {{ $isActive ? 'text-brand-ink bg-brand/10' : 'text-ink-muted hover:text-ink hover:bg-ink/5' }}"
            >
                <x-svg-icon :name="$link['icon']" class="w-[18px] h-[18px] shrink-0 {{ $isActive ? 'text-brand-ink' : '' }}" stroke-width="1.75" aria-hidden="true" />
                {{ $link['label'] }}
            </a>
        </li>
    @endforeach
</ul>
