@props([
    'variant' => 'primary',  // 'primary' | 'secondary'
    'href'    => null,
    'type'    => 'button',
    'disabled' => false,
])

@php
    $tag = $href ? 'a' : 'button';

    $baseClasses = 'inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 ease-out active:scale-[0.97] active:opacity-90 focus-visible:outline-none';
    
    $primaryClasses = 'bg-brand text-brand-fg hover:bg-brand-hover';
    $secondaryClasses = 'bg-transparent text-ink border border-line hover:bg-ink/5 hover:border-ink/30';
    
    $variantClasses = $variant === 'primary' ? $primaryClasses : $secondaryClasses;
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    @if($tag === 'button') type="{{ $type }}" @endif
    @if($disabled) disabled aria-disabled="true" @endif
    {{ $attributes->class([
        $baseClasses,
        $variantClasses,
        'opacity-50 cursor-not-allowed pointer-events-none' => $disabled,
    ]) }}
>
    {{ $slot }}
</{{ $tag }}>
