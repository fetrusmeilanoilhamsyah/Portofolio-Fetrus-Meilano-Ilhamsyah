@props([
    'padding' => true,
])

<div
    {{ $attributes->class([
        'rounded-lg border bg-surf border-line',
        'p-5 md:p-6' => $padding,
    ]) }}
>
    {{ $slot }}
</div>
