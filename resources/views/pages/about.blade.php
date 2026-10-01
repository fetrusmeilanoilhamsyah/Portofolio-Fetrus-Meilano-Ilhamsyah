<x-layouts.public :title="__('ui.page_about') . ' — ' . (public_text($siteSetting?->name) ?? config('app.name'))">
    <x-page-header :title="__('ui.page_about')" />

    @if(!$siteSetting || (empty(public_text($siteSetting->about_body)) && empty($siteSetting->photo) && $education->isEmpty() && empty($siteSetting->skills)))
        <x-empty-state :message="__('ui.empty_coming_soon')" />
    @else
        <div class="flex flex-col md:flex-row gap-10 lg:gap-16 mb-16">
            {{-- Bagian kiri: Konten utama --}}
            <div class="flex-1 min-w-0">
                @if(public_text($siteSetting->about_body))
                    <div class="text-ink-muted mb-10">
                        <x-prose :content="public_text($siteSetting->about_body)" />
                    </div>
                @endif
                
                @if($siteSetting->cv_file)
                    <div class="mb-10">
                        <x-button as="a" href="{{ media_url($siteSetting->cv_file) }}" target="_blank" rel="noopener noreferrer" variant="primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            {{ __('ui.download_cv') }}
                        </x-button>
                    </div>
                @endif

                @if($education->isNotEmpty())
                    <section class="mb-10">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-ink">{{ __('ui.education') }}</h2>
                            <a href="{{ localized_route('experience') }}" class="text-sm font-medium text-brand-ink hover:underline">
                                {{ __('ui.btn_view_all') }} &rarr;
                            </a>
                        </div>
                        <div class="space-y-6">
                            @foreach($education as $edu)
                                <div class="flex gap-4 group">
                                    <div class="mt-1">
                                        <div class="w-10 h-10 rounded bg-canvas-muted border border-line flex items-center justify-center overflow-hidden shrink-0">
                                            @if($edu->logo)
                                                <img src="{{ media_url($edu->logo) }}" alt="{{ $edu->organization }}" class="w-full h-full object-cover" width="40" height="40" loading="lazy">
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-ink-muted"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-ink">{{ $edu->title }}</h3>
                                        <div class="text-sm font-medium text-ink-muted mb-1">{{ $edu->organization }}</div>
                                        <div class="text-xs text-ink-muted opacity-80">
                                            {{ $edu->started_at?->translatedFormat('Y') }} 
                                            @if($edu->started_at && $edu->ended_at)
                                                &mdash; {{ $edu->ended_at->translatedFormat('Y') }}
                                            @elseif($edu->started_at)
                                                &mdash; {{ __('ui.present') ?? 'Sekarang' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Bagian kanan: Foto dan Keahlian --}}
            <aside class="w-full md:w-64 lg:w-72 shrink-0 space-y-10">
                @if($siteSetting->photo)
                    <div class="rounded-xl overflow-hidden bg-canvas-muted border border-line aspect-square max-w-[240px] md:max-w-none mx-auto md:mx-0">
                        <img 
                            src="{{ media_url($siteSetting->photo) }}" 
                            alt="{{ public_text($siteSetting->name) ?? config('app.name') }}" 
                            class="w-full h-full object-cover"
                            width="400"
                            height="400"
                            loading="lazy"
                        >
                    </div>
                @endif

                @if($siteSetting->skills && is_array($siteSetting->skills) && count($siteSetting->skills) > 0)
                    <section>
                        <h2 class="text-xl font-bold text-ink mb-6">{{ __('ui.skills') }}</h2>
                        <div class="space-y-6">
                            @foreach($siteSetting->skills as $skillGroup)
                                <div>
                                    <h3 class="text-sm font-bold text-ink mb-3 uppercase tracking-wider">{{ $skillGroup['group'] }}</h3>
                                    <ul class="flex flex-wrap gap-2">
                                        @php
                                            $items = array_map('trim', explode(',', $skillGroup['items'] ?? ''));
                                        @endphp
                                        @foreach(array_filter($items) as $item)
                                            <li class="text-sm px-2.5 py-1 rounded-md bg-canvas-muted border border-line text-ink-muted">
                                                {{ $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    @endif
</x-layouts.public>
