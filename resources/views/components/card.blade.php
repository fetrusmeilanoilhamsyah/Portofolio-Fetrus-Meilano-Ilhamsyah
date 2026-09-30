@props([
    'padding' => true,
])

<div
    {{ $attributes->class([
        'rounded-lg border',
        'p-5 md:p-6' => $padding,
    ]) }}
    style="background-color: var(--surf); border-color: var(--line);"
>
    {{ $slot }}
</div>
