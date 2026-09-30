@props([
    'variant' => 'primary',  // 'primary' | 'secondary'
    'href'    => null,
    'type'    => 'button',
    'disabled' => false,
])

@php
    $tag = $href ? 'a' : 'button';

    $baseStyle = 'inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-colors focus-visible:outline-none';

    $primaryInlineStyle = 'background-color: var(--brand); color: var(--brand-fg);';
    $primaryHoverScript = "this.style.backgroundColor='var(--brand-hover)'";
    $primaryOutScript   = "this.style.backgroundColor='var(--brand)'";

    $secondaryInlineStyle = 'background-color: transparent; color: var(--ink); border: 1px solid var(--line);';
    $secondaryHoverScript = "this.style.backgroundColor='color-mix(in srgb, var(--ink) 6%, transparent)'; this.style.borderColor='color-mix(in srgb, var(--ink) 30%, transparent)';";
    $secondaryOutScript   = "this.style.backgroundColor='transparent'; this.style.borderColor='var(--line)';";

    $inlineStyle  = $variant === 'primary' ? $primaryInlineStyle : $secondaryInlineStyle;
    $onMouseOver  = ! $disabled ? ($variant === 'primary' ? $primaryHoverScript : $secondaryHoverScript) : '';
    $onMouseOut   = ! $disabled ? ($variant === 'primary' ? $primaryOutScript : $secondaryOutScript) : '';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    @if($tag === 'button') type="{{ $type }}" @endif
    @if($disabled) disabled aria-disabled="true" @endif
    {{ $attributes->class([
        $baseStyle,
        'opacity-50 cursor-not-allowed' => $disabled,
    ]) }}
    style="{{ $inlineStyle }}{{ $disabled ? 'cursor:not-allowed; opacity:0.5;' : '' }}"
    @if(! $disabled)
        onmouseover="{{ $onMouseOver }}"
        onmouseout="{{ $onMouseOut }}"
    @endif
>
    {{ $slot }}
</{{ $tag }}>
