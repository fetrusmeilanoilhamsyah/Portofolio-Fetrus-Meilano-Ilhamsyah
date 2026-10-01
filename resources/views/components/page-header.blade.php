@props([
    'title',
    'subtitle' => null,
])

<header class="mb-10">
    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-ink">
        {{ $title }}
    </h1>

    @if ($subtitle)
        <p class="mt-2 text-base text-ink-muted">
            {{ $subtitle }}
        </p>
    @endif

    {{-- Garis solid tipis di bawah --}}
    <div class="mt-4 h-px bg-line" aria-hidden="true"></div>
</header>
