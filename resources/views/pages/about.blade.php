<x-layouts.public :title="__('ui.page_about') . ' - ' . config('app.name')">
    <x-page-header :title="__('ui.page_about')" :subtitle="__('ui.sub_about')" />

    @if(empty($siteSetting->about_body))
        <x-empty-state icon="user" :message="__('ui.empty_coming_soon')" />
    @else
        <div class="flex flex-col-reverse md:flex-row gap-12 items-start mt-8">
            <div class="flex-1 w-full space-y-12">
                <section>
                    <div
                        class="prose-portfolio"
                        aria-live="polite"
                    >
                        {!! public_text($siteSetting->about_body, true) !!}
                    </div>
                    
                    @if($siteSetting->cv_file)
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <x-button as="a" href="{{ media_url($siteSetting->cv_file) }}" target="_blank" variant="primary" icon="download">
                                {{ __('ui.download_cv') }}
                            </x-button>
                            <a
                                href="{{ app()->getLocale() === 'en' ? route('en.cv') : route('cv') }}"
                                class="text-sm text-brand-ink hover:underline transition-colors"
                            >{{ __('ui.view_cv_web') }}</a>
                        </div>
                    @else
                        <div class="mt-8">
                            <a
                                href="{{ app()->getLocale() === 'en' ? route('en.cv') : route('cv') }}"
                                class="text-sm text-brand-ink hover:underline transition-colors"
                            >{{ __('ui.view_cv_web') }}</a>
                        </div>
                    @endif
                </section>

                @php
                    $workExp = $experiences->where('kind', '!=', App\Enums\ExperienceKind::Pendidikan);
                    $eduExp = $experiences->where('kind', App\Enums\ExperienceKind::Pendidikan);
                @endphp

                @if($workExp->isNotEmpty())
                    <section class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-ink-muted"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <h2 class="text-xl font-bold text-ink">Pengalaman</h2>
                        </div>
                        <div class="space-y-4">
                            @foreach($workExp as $exp)
                                <div class="bg-surf border border-line rounded-lg p-4 sm:p-5 transition-colors hover:border-ink/20" x-data="{ expanded: false }">
                                    <div class="flex gap-4 items-start sm:items-center">
                                        <div class="w-12 h-12 rounded-lg bg-canvas border border-line flex items-center justify-center overflow-hidden shrink-0">
                                            @if($exp->logo)
                                                <img src="{{ media_url($exp->logo) }}" alt="{{ $exp->organization }}" class="w-full h-full object-cover" width="48" height="48" loading="lazy">
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-ink-muted"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-ink text-base truncate">{{ $exp->title }}</h3>
                                            <div class="text-sm font-medium text-ink-muted truncate">
                                                {{ $exp->organization }}
                                                @if($exp->location) &bull; {{ $exp->location }} @endif
                                            </div>
                                            <div class="text-xs text-ink-muted opacity-80 mt-1 flex flex-wrap gap-x-2 gap-y-1 items-center">
                                                <span>{{ $exp->started_at?->translatedFormat('M Y') ?? '' }} - {{ $exp->ended_at ? $exp->ended_at->translatedFormat('M Y') : 'Sekarang' }}</span>
                                                @if($exp->started_at)
                                                    @php
                                                        $diff = $exp->started_at->diffAsCarbonInterval($exp->ended_at ?? now());
                                                    @endphp
                                                    <span>&bull; {{ $diff->y > 0 ? $diff->y . ' thn ' : '' }}{{ $diff->m > 0 ? $diff->m . ' bln' : '' }}</span>
                                                @endif
                                                <span class="px-2 py-0.5 rounded-full bg-canvas-muted border border-line text-[10px] uppercase font-bold">{{ $exp->kind->label() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($exp->description)
                                        <div class="mt-4 pt-4 border-t border-line/50">
                                            <button @click="expanded = !expanded" class="text-sm font-medium text-brand-ink hover:underline flex items-center gap-1 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 transition-transform duration-200" x-bind:class="expanded ? 'rotate-90' : ''"><path d="m9 18 6-6-6-6"/></svg>
                                                <span x-text="expanded ? 'Sembunyikan detail' : 'Tampilkan detail'"></span>
                                            </button>
                                            <div x-show="expanded" x-collapse x-cloak>
                                                <div class="mt-3 text-sm text-ink-muted prose-sm max-w-none">
                                                    {!! Str::markdown($exp->description, ['html_input' => 'strip']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if($eduExp->isNotEmpty())
                    <section class="space-y-4 mt-8">
                        <div class="flex items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-ink-muted"><path d="M21.42 10.922a2 2 0 0 1-.019 3.838L12.83 19.25a2 2 0 0 1-1.665 0l-8.571-4.49a2 2 0 0 1-.019-3.838l8.571-4.363a2 2 0 0 1 1.689 0l8.571 4.363z"/><path d="M7 14v4.032a2 2 0 0 0 1.085 1.777L11.17 21.41a2 2 0 0 0 1.66 0l3.085-1.601A2 2 0 0 0 17 18.032V14"/><path d="M22 10v6"/><path d="M2 10l10-5 10 5-10 5z"/></svg>
                            <h2 class="text-xl font-bold text-ink">Pendidikan</h2>
                        </div>
                        <div class="space-y-4">
                            @foreach($eduExp as $edu)
                                <div class="bg-surf border border-line rounded-lg p-4 sm:p-5 transition-colors hover:border-ink/20" x-data="{ expanded: false }">
                                    <div class="flex gap-4 items-start sm:items-center">
                                        <div class="w-12 h-12 rounded-lg bg-canvas border border-line flex items-center justify-center overflow-hidden shrink-0">
                                            @if($edu->logo)
                                                <img src="{{ media_url($edu->logo) }}" alt="{{ $edu->organization }}" class="w-full h-full object-cover" width="48" height="48" loading="lazy">
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-ink-muted"><path d="M21.42 10.922a2 2 0 0 1-.019 3.838L12.83 19.25a2 2 0 0 1-1.665 0l-8.571-4.49a2 2 0 0 1-.019-3.838l8.571-4.363a2 2 0 0 1 1.689 0l8.571 4.363z"/><path d="M7 14v4.032a2 2 0 0 0 1.085 1.777L11.17 21.41a2 2 0 0 0 1.66 0l3.085-1.601A2 2 0 0 0 17 18.032V14"/><path d="M22 10v6"/><path d="M2 10l10-5 10 5-10 5z"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-ink text-base truncate">{{ $edu->organization }}</h3>
                                            <div class="text-sm font-medium text-ink-muted truncate">
                                                {{ $edu->title }}
                                                @if($edu->location) &bull; {{ $edu->location }} @endif
                                            </div>
                                            <div class="text-xs text-ink-muted opacity-80 mt-1 flex items-center gap-2">
                                                <span>{{ $edu->started_at?->format('Y') ?? '' }} - {{ $edu->ended_at ? $edu->ended_at->format('Y') : 'Sekarang' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($edu->description)
                                        <div class="mt-4 pt-4 border-t border-line/50">
                                            <button @click="expanded = !expanded" class="text-sm font-medium text-brand-ink hover:underline flex items-center gap-1 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 transition-transform duration-200" x-bind:class="expanded ? 'rotate-90' : ''"><path d="m9 18 6-6-6-6"/></svg>
                                                <span x-text="expanded ? 'Sembunyikan detail' : 'Tampilkan detail'"></span>
                                            </button>
                                            <div x-show="expanded" x-collapse x-cloak>
                                                <div class="mt-3 text-sm text-ink-muted prose-sm max-w-none">
                                                    {!! Str::markdown($edu->description, ['html_input' => 'strip']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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



