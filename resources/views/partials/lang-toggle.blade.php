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
    class="flex items-center justify-center h-8 px-2 min-w-8 text-xs font-semibold rounded-md border border-line uppercase tracking-wide transition-colors text-ink-muted hover:text-ink hover:bg-ink/5"
>
    {{ $otherLabel }}
</a>
