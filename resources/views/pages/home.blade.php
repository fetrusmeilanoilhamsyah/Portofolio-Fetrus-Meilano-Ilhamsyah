<x-layouts.public :title="public_text($siteSetting?->name) ?? config('app.name')" maxWidth="max-w-6xl">
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
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-start">
                    
                    {{-- Kolom Kiri: Teks --}}
                    <div class="lg:col-span-8 lg:pr-8">
                        @if($siteSetting->location)
                            <div class="text-xs font-semibold tracking-wider uppercase text-ink-muted mb-4">
                                {{ $siteSetting->location }}
                            </div>
                        @endif
                        
                        @if(public_text($siteSetting->name))
                            <h1 class="text-3xl md:text-5xl font-bold tracking-tight text-ink mb-6 leading-tight">
                                {{ app()->getLocale() === 'en' ? "Hi, I'm" : "Halo, saya" }} 
                                <span class="text-brand-ink">{{ public_text($siteSetting->name) }}</span>
                            </h1>
                        @endif
                        
                        @if(public_text($siteSetting->role))
                            <p class="text-lg md:text-xl text-ink-muted mb-6 font-medium leading-relaxed max-w-2xl">
                                {{ public_text($siteSetting->role) }}
                            </p>
                        @endif
                        
                        @if($intro)
                            <div class="max-w-2xl text-base text-ink-muted mb-10 leading-relaxed">
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
                        
                        <div class="mt-8">
                            <a href="{{ localized_route('experience') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-ink-muted hover:text-brand-ink transition-colors focus:outline-none focus-visible:underline underline-offset-4">
                                {{ app()->getLocale() === 'en' ? 'View experience' : 'Lihat pengalaman' }}
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Foto Profil --}}
                    @if($siteSetting->photo)
                        <div class="hidden lg:block lg:col-span-4 relative group">
                            <div class="absolute -inset-2 bg-line/20 rounded-3xl transform rotate-3 transition-transform duration-500 group-hover:rotate-6"></div>
                            <div class="relative w-full aspect-[4/5] rounded-2xl overflow-hidden bg-canvas-muted ring-1 ring-line shadow-sm">
                                <img src="{{ media_url($siteSetting->photo) }}" alt="{{ public_text($siteSetting->name) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Keahlian di Beranda --}}
                @if($siteSetting->skills && is_array($siteSetting->skills) && count($siteSetting->skills) > 0)
                    @php
                        $skillData = [];
                        $totalSkills = 0;
                        foreach($siteSetting->skills as $sg) {
                            $items = array_filter(array_map('trim', explode(',', $sg['items'] ?? '')));
                            if(count($items) > 0) {
                                $skillData[] = [
                                    'group' => trim($sg['group']),
                                    'items' => array_values($items),
                                ];
                                $totalSkills += count($items);
                            }
                        }
                    @endphp
                    @if($totalSkills > 0)
                        <section x-data="{ 
                            active: 'Semua',
                            groups: {{ \Illuminate\Support\Js::from($skillData) }}
                        }" class="max-w-4xl pt-4">
                            <div class="flex items-center gap-2 mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-ink-muted"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                <h2 class="text-xl font-bold text-ink">{{ __('ui.skills') }}</h2>
                            </div>
                            
                            {{-- Filter Buttons --}}
                            <div class="flex flex-wrap gap-2 mb-6">
                                <button 
                                    @click="active = 'Semua'" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all duration-200 ease-out active:scale-[0.97] border"
                                    :class="active === 'Semua' ? 'bg-brand text-brand-fg border-brand-hover' : 'bg-canvas text-ink-muted border-line hover:bg-ink/5'"
                                >
                                    Semua 
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold"
                                          :class="active === 'Semua' ? 'bg-black/20 text-brand-fg' : 'bg-ink/10 text-ink-muted'">{{ $totalSkills }}</span>
                                </button>
                                
                                <template x-for="g in groups" :key="g.group">
                                    <button 
                                        @click="active = g.group" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all duration-200 ease-out active:scale-[0.97] border"
                                        :class="active === g.group ? 'bg-brand text-brand-fg border-brand-hover' : 'bg-canvas text-ink-muted border-line hover:bg-ink/5'"
                                    >
                                        <span x-text="g.group"></span>
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold"
                                              :class="active === g.group ? 'bg-black/20 text-brand-fg' : 'bg-ink/10 text-ink-muted'"
                                              x-text="g.items.length"></span>
                                    </button>
                                </template>
                            </div>

                            {{-- Skills Cloud --}}
                            <div class="flex flex-wrap gap-2">
                                <template x-for="g in groups" :key="g.group">
                                    <template x-for="skill in g.items" :key="skill">
                                        <div 
                                            x-show="active === 'Semua' || active === g.group"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-90"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="inline-flex items-center px-3 py-1.5 rounded-full bg-canvas border border-line text-xs font-medium text-ink hover:border-brand/40 hover:-translate-y-0.5 transition-all cursor-default"
                                        >
                                            <span x-text="skill"></span>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </section>
                    @endif
                @endif
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
