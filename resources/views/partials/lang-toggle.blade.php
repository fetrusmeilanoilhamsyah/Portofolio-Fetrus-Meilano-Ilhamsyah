{{--
    Toggle bahasa Indonesia / Inggris.
    Mengarahkan ke halaman yang sama dalam bahasa yang dipilih.
--}}
@php
    $locale = app()->getLocale();
    $otherLocale = $locale === 'id' ? 'en' : 'id';
    $otherLabel = strtoupper($otherLocale);
    $otherLongLabel = $locale === 'id' ? __('ui.lang_en') : __('ui.lang_id');
    $otherUrl = \App\Support\LocaleSwitcher::switchUrl(request());
@endphp

<a
    href="{{ $otherUrl }}"
    lang="{{ $otherLocale }}"
    aria-label="{{ __('ui.toggle_lang') }}: {{ $otherLongLabel }}"
    title="{{ __('ui.toggle_lang') }}: {{ $otherLongLabel }}"
    x-data="{ switching: false }"
    @click.prevent="switching = true; setTimeout(() => window.location.href = '{{ $otherUrl }}', 250)"
    class="relative flex items-center w-16 h-8 rounded-full bg-canvas-muted p-1 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand"
>
    {{-- Sliding active pill --}}
    <div 
        class="absolute left-1 top-1 bottom-1 w-6 bg-brand rounded-full transition-transform duration-300 ease-out"
        :class="switching ? '{{ $locale === 'en' ? 'translate-x-0' : 'translate-x-7' }}' : '{{ $locale === 'en' ? 'translate-x-7' : 'translate-x-0' }}'"
    ></div>
    
    {{-- ID --}}
    <div class="relative z-10 flex-1 flex items-center justify-center transition-colors duration-200 text-[10px] font-extrabold"
         :class="(switching ? {{ $locale === 'en' ? 'true' : 'false' }} : {{ $locale === 'id' ? 'true' : 'false' }}) ? 'text-brand-fg' : 'text-ink-muted'">
        ID
    </div>
    
    {{-- EN --}}
    <div class="relative z-10 flex-1 flex items-center justify-center transition-colors duration-200 text-[10px] font-extrabold"
         :class="(switching ? {{ $locale === 'id' ? 'true' : 'false' }} : {{ $locale === 'en' ? 'true' : 'false' }}) ? 'text-brand-fg' : 'text-ink-muted'">
        EN
    </div>
</a>
