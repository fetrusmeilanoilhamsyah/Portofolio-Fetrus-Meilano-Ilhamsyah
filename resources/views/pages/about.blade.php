<x-layouts.public :title="__('ui.page_about') . ' — ' . (public_text($siteSetting?->name) ?? config('app.name'))">
    <x-page-header :title="__('ui.page_about')" :subtitle="__('ui.sub_about')" />

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
                    <div class="rounded-lg overflow-hidden bg-canvas-muted border border-line aspect-square max-w-[240px] md:max-w-none mx-auto md:mx-0">
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
                    <section x-data="{ 
                        active: 'Semua',
                        groups: {{ Js::from($skillData) }}
                    }">
                        <h2 class="text-xl font-bold text-ink mb-6">{{ __('ui.skills') }}</h2>
                        
                        {{-- Filter Buttons --}}
                        <div class="flex flex-wrap gap-2 mb-6">
                            <button 
                                @click="active = 'Semua'" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all duration-200 ease-out active:scale-[0.97] active:opacity-90 border"
                                :class="active === 'Semua' ? 'bg-brand text-brand-fg border-brand-hover' : 'bg-canvas text-ink-muted border-line hover:bg-ink/5'"
                            >
                                Semua 
                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold"
                                      :class="active === 'Semua' ? 'bg-black/20 text-brand-fg' : 'bg-ink/10 text-ink-muted'">{{ $totalSkills }}</span>
                            </button>
                            
                            <template x-for="g in groups" :key="g.group">
                                <button 
                                    @click="active = g.group" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-semibold transition-all duration-200 ease-out active:scale-[0.97] active:opacity-90 border"
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
            </aside>
        </div>
    @endif
</x-layouts.public>
