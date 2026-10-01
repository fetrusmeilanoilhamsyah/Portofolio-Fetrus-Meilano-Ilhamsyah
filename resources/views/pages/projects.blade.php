<x-layouts.public :title="__('ui.page_projects') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_projects')" :subtitle="__('ui.sub_projects')" />

    @if($projects->isEmpty())
        <x-empty-state />
    @else
        <div x-data="projectsList({{ Js::from($projects->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'summary' => $p->summary,
            'type_value' => $p->type->value,
            'type_label' => $p->type->label(),
            'url' => localized_route('projects.show', [$p->slug]),
            'cover' => $p->cover_image ? media_url($p->cover_image) : null,
            'cover_alt' => $p->cover_alt ?: $p->title,
            'year' => $p->started_at ? $p->started_at->translatedFormat('Y') : null,
            'stack' => is_array($p->stack) ? $p->stack : [],
            'is_featured' => $p->is_featured,
        ])) }})" class="mt-8">
            
            {{-- Filter & Search --}}
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-muted" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="search" x-model="search" placeholder="Cari proyek..." class="w-full pl-9 pr-4 py-2 bg-canvas border border-line rounded text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand placeholder:text-ink-muted">
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-xs font-semibold text-ink-muted uppercase tracking-wider mr-2">TIPE</span>
                    <button @click="type = ''" class="px-3 py-1.5 text-xs rounded-full border transition-all duration-200 ease-out active:scale-[0.97] active:opacity-90 font-medium" :class="type === '' ? 'bg-brand text-brand-fg border-brand-hover' : 'bg-canvas text-ink-muted border-line hover:bg-ink/5'">Semua</button>
                    @foreach($projects->pluck('type')->unique() as $ptype)
                        <button @click="type = '{{ $ptype->value }}'" class="px-3 py-1.5 text-xs rounded-full border transition-all duration-200 ease-out active:scale-[0.97] active:opacity-90 font-medium" :class="type === '{{ $ptype->value }}' ? 'bg-brand text-brand-fg border-brand-hover' : 'bg-canvas text-ink-muted border-line hover:bg-ink/5'">{{ $ptype->label() }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($projects as $p)
                    <div 
                        x-show="showProject('{{ $p->id }}')" 
                        x-transition.opacity.duration.200ms
                        class="bg-canvas border border-line rounded-lg relative flex flex-col h-full overflow-hidden transition-all duration-200 ease-out hover:-translate-y-0.5 hover:border-brand/40 active:scale-[0.99] active:opacity-90 group"
                    >
                        
                        <div class="relative w-full aspect-video bg-canvas border-b border-line flex items-center justify-center overflow-hidden shrink-0">
                            @if($p->is_featured)
                                <div class="absolute top-3 right-3 z-10 inline-flex items-center gap-1.5 bg-brand text-brand-fg px-2.5 py-1 text-xs font-semibold rounded-md border border-brand-hover">
                                    <x-svg-icon name="lucide-pin" class="w-3.5 h-3.5" stroke-width="2.5" />
                                    {{ __('ui.featured', ['default' => 'Unggulan']) }}
                                </div>
                            @endif

                            @if($p->cover_image)
                                <img src="{{ media_url($p->cover_image) }}" alt="{{ $p->cover_alt ?: $p->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" width="400" height="225">
                            @else
                                <x-svg-icon name="folder" class="w-10 h-10 text-ink-muted/30 transition-transform duration-500 group-hover:scale-110" />
                            @endif
                        </div>
                        
                        <div class="flex flex-col flex-1 p-5">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h3 class="text-base font-semibold text-ink group-hover:text-brand-ink transition-colors leading-tight">
                                    <a href="{{ localized_route('projects.show', [$p->slug]) }}" class="focus:outline-none">
                                        <span class="absolute inset-0 z-0" aria-hidden="true"></span>
                                        {{ $p->title }}
                                    </a>
                                </h3>
                            </div>
                            
                            @if($p->summary)
                                <p class="text-sm text-ink-muted mb-4 line-clamp-2 leading-relaxed">{{ $p->summary }}</p>
                            @endif
                            
                            <div class="mt-auto pt-3 flex flex-wrap items-center gap-2 relative z-10">
                                <span class="inline-flex items-center px-2 py-0.5 rounded border border-line bg-canvas text-xs font-medium text-ink-muted">{{ $p->type->label() }}</span>
                                @if($p->started_at)
                                    <span class="inline-flex items-center text-xs text-ink-muted">
                                        <x-svg-icon name="lucide-calendar" class="w-3.5 h-3.5 mr-1" />
                                        <span>{{ $p->started_at->translatedFormat('Y') }}</span>
                                    </span>
                                @endif
                            </div>
                            
                            @if(is_array($p->stack) && count($p->stack) > 0)
                                <div class="mt-3 flex flex-wrap gap-1.5 relative z-10 border-t border-line/50 pt-3">
                                    @foreach(array_slice($p->stack, 0, 4) as $tech)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-ink/5 text-[11px] font-medium text-ink-muted">{{ $tech }}</span>
                                    @endforeach
                                    @if(count($p->stack) > 4)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-transparent text-[11px] font-medium text-ink-muted">+{{ count($p->stack) - 4 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Empty State Search --}}
            <div x-show="!hasResults" x-cloak class="py-12 text-center text-ink-muted">
                Pencarian tidak menemukan hasil.
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('projectsList', (items) => ({
                    items: items,
                    search: '',
                    type: '',
                    
                    get filteredIds() {
                        return this.items.filter(i => {
                            const matchSearch = this.search === '' || 
                                i.title.toLowerCase().includes(this.search.toLowerCase()) || 
                                (i.summary && i.summary.toLowerCase().includes(this.search.toLowerCase()));
                            const matchType = this.type === '' || i.type_value === this.type;
                            return matchSearch && matchType;
                        }).map(i => String(i.id));
                    },

                    showProject(id) {
                        return this.filteredIds.includes(String(id));
                    },
                    
                    get hasResults() {
                        return this.filteredIds.length > 0;
                    }
                }));
            });
        </script>
        @endpush
    @endif
</x-layouts.public>
