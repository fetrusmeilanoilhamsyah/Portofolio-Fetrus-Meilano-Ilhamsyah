@props([])

<span
    {{ $attributes->class([
        'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-md border',
    ]) }}
    style="
        color: var(--ink-muted);
        background-color: color-mix(in srgb, var(--ink) 5%, transparent);
        border-color: var(--line);
    "
>
    {{ $slot }}
</span>
