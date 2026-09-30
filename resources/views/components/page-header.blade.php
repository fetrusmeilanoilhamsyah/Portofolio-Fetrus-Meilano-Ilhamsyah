@props([
    'title',
    'subtitle' => null,
])

<header class="mb-8">
    <h1
        class="text-2xl md:text-3xl font-semibold tracking-tight"
        style="color: var(--ink);"
    >
        {{ $title }}
    </h1>

    @if ($subtitle)
        <p
            class="mt-2 text-base"
            style="color: var(--ink-muted);"
        >
            {{ $subtitle }}
        </p>
    @endif

    {{-- Garis solid tipis di bawah --}}
    <div
        class="mt-4 h-px"
        style="background-color: var(--line);"
        aria-hidden="true"
    ></div>
</header>
