@props([
    'markdown' => '',
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
    use League\CommonMark\CommonMarkConverter;
    use League\CommonMark\Environment\Environment;
    use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
    use League\CommonMark\Extension\GithubFlavoredMarkdown\GithubFlavoredMarkdownExtension;

    $environment = new Environment([
        'html_input'         => 'strip',       // hapus HTML mentah
        'allow_unsafe_links' => false,          // tolak javascript: dan data: URLs
        'max_nesting_level'  => 25,
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new GithubFlavoredMarkdownExtension());

    $converter  = new CommonMarkConverter(environment: $environment);
    $rendered   = $converter->convert($markdown)->getContent();
@endphp

<div
    {{ $attributes->class(['prose-portfolio']) }}
    aria-live="polite"
>
    {!! $rendered !!}
</div>
