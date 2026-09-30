@props([])

<span
    {{ $attributes->class([
        'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-md border text-ink-muted bg-black/5 dark:bg-white/10 border-line',
    ]) }}
>
    {{ $slot }}
</span>
