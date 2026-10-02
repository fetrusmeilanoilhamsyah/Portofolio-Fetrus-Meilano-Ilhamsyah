<x-layouts.public :title="public_text($siteSetting?->name) ?? config('app.name')">
    @push('head')
        @if($siteSetting)
            @php
                $jsonLd = [
                    "@context" => "https://schema.org",
                    "@type" => "Person",
                    "name" => public_text($siteSetting->name),
                    "url" => url('/'),
                ];
                if ($siteSetting->photo) {
                    $jsonLd["image"] = media_url($siteSetting->photo);
                }
                if ($siteSetting->role) {
                    $jsonLd["jobTitle"] = public_text($siteSetting->role);
                }
                $jsonLd["sameAs"] = $socialLinks ?? [];
            @endphp
            <script type="application/ld+json">
                {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES) !!}
            </script>
        @endif
    @endpush
    @php
        $intro = $siteSetting ? public_text($siteSetting->intro_home) : null;
        if ($intro && str_contains($intro, '[ISI:')) {
            $intro = null;
        }
        $isEmpty = !$siteSetting || (empty(public_text($siteSetting->name)) && empty(public_text($siteSetting->role)) && empty($intro));
    @endphp

    @if($isEmpty && $featuredProjects->isEmpty() && $recentProjects->isEmpty())
        <x-empty-state :message="__('ui.empty_coming_soon')" />
    @else
        <div class="space-y-16">
            {{-- Hero Block --}}
            @if($siteSetting && (!empty(public_text($siteSetting->name)) || !empty(public_text($siteSetting->role)) || !empty($intro)))
                <div class="flex flex-col items-start">
                    <div class="w-full">
                        {{-- Photo for mobile only (desktop has it in sidebar) --}}
                        @if($siteSetting->photo)
                            <div class="lg:hidden mb-6 flex justify-start">
                                <div class="w-24 h-24 shrink-0 rounded-lg overflow-hidden bg-canvas border border-line">
                                    <img src="{{ media_url($siteSetting->photo) }}" alt="{{ public_text($siteSetting->name) }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endif

                        @if($siteSetting->location)
                            <div class="text-xs font-semibold tracking-wider uppercase text-ink-muted mb-3">
                                {{ $siteSetting->location }}
                            </div>
                        @endif
                        
                        @if(public_text($siteSetting->name))
                            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-ink mb-4 leading-tight">
                                {{ public_text($siteSetting->name) }}
                            </h1>
                        @endif
                        
                        @if(public_text($siteSetting->role))
                            <p class="text-lg md:text-xl text-ink-muted mb-6 font-medium leading-relaxed max-w-2xl">
                                {{ public_text($siteSetting->role) }}
                            </p>
                        @endif
                        
                        @if($intro)
                            <div class="max-w-prose text-ink-muted mb-8 leading-relaxed">
                                <x-prose :content="$intro" />
                            </div>
                        @endif
                        
                        <div class="flex flex-wrap items-center gap-4">
                            <x-button as="a" href="{{ localized_route('projects') }}" variant="primary">
                                {{ __('ui.page_projects') }}
                            </x-button>
                            <x-button as="a" href="{{ localized_route('about') }}" variant="primary">
                                {{ __('ui.page_about') }}
                            </x-button>
                            <x-button as="a" href="{{ localized_route('contact') }}" variant="primary">
                                {{ __('ui.page_contact') }}
                            </x-button>
                        </div>
                        <div class="mt-6">
                            <a href="{{ localized_route('experience') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-ink-muted hover:text-brand-ink transition-colors focus:outline-none focus-visible:underline underline-offset-4">
                                {{ app()->getLocale() === 'en' ? 'View experience' : 'Lihat pengalaman' }}
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Proyek Unggulan --}}
            @if($featuredProjects->isNotEmpty())
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-ink">{{ __('ui.featured_projects') }}</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($featuredProjects as $project)
                            <x-project-card :project="$project" />
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Proyek Terbaru --}}
            @if($recentProjects->isNotEmpty())
                <section>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-ink">{{ __('ui.recent_projects') }}</h2>
                        <a href="{{ localized_route('projects') }}" class="text-sm font-medium text-brand-ink hover:underline">
                            {{ __('ui.view_all_projects') }} &rarr;
                        </a>
                    </div>
                    <div class="flex flex-col gap-4">
                        @foreach($recentProjects as $project)
                            <x-project-list-item :project="$project" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    @endif
</x-layouts.public>
