@props([
    'content' => '',
])

{{--
    Komponen x-prose: merender string markdown menggunakan CommonMark.

    Keamanan:
    - HTML mentah di dalam markdown dimatikan (html_input: 'strip')
    - Tautan tidak aman ditolak (allow_unsafe_links: false)
    - Output di-escape oleh Blade kecuali konten sudah di-render sebagai HTML
      yang aman via CommonMark — gunakan {!! !!} secara sengaja di sini.
--}}
@php
    $rendered = \Illuminate\Support\Str::markdown($content ?? $markdown ?? '', [
        'html_input'         => 'strip',
        'allow_unsafe_links' => false,
    ]);
@endphp

<div
    {{ $attributes->class(['prose-portfolio']) }}
    aria-live="polite"
>
    {!! $rendered !!}
</div>
