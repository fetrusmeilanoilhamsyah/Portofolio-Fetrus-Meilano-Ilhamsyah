<x-layouts.public :title="__('ui.page_projects') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_projects')" />

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
        ])) }})" class="mt-8">
            
            {{-- Filter & Search --}}
            <div class="flex flex-col sm:flex-row gap-4 mb-8">
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-muted" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="search" x-model="search" placeholder="Cari proyek..." class="w-full pl-9 pr-4 py-2 bg-canvas border border-line rounded text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand placeholder:text-ink-muted">
                </div>
                <div class="flex flex-wrap gap-2">
                    <button @click="type = ''" class="px-3 py-1.5 text-xs rounded-full border transition-colors" :class="type === '' ? 'bg-ink text-canvas border-ink' : 'bg-canvas text-ink-muted border-line hover:border-ink-muted'">Semua</button>
                    @foreach($projects->pluck('type')->unique() as $ptype)
                        <button @click="type = '{{ $ptype->value }}'" class="px-3 py-1.5 text-xs rounded-full border transition-colors" :class="type === '{{ $ptype->value }}' ? 'bg-ink text-canvas border-ink' : 'bg-canvas text-ink-muted border-line hover:border-ink-muted'">{{ $ptype->label() }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <template x-for="p in filteredProjects" :key="p.id">
                    <div class="bg-canvas border border-line rounded-lg relative flex flex-col h-full overflow-hidden transition-colors hover:border-ink-muted group">
                        <template x-if="p.cover">
                            <div class="relative w-full aspect-video bg-canvas-muted overflow-hidden border-b border-line">
                                <img :src="p.cover" :alt="p.cover_alt" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" width="400" height="225">
                            </div>
                        </template>
                        
                        <div class="flex flex-col flex-1 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center rounded-md bg-canvas-muted px-2 py-1 text-xs font-medium text-ink border border-line" x-text="p.type_label"></span>
                                <template x-if="p.year">
                                    <span class="text-xs text-ink-muted" x-text="p.year"></span>
                                </template>
                            </div>
                            
                            <h3 class="text-lg font-bold text-ink mb-2 group-hover:text-brand-ink transition-colors">
                                <a :href="p.url" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <span x-text="p.title"></span>
                                </a>
                            </h3>
                            
                            <template x-if="p.summary">
                                <p class="text-sm text-ink-muted mb-4 line-clamp-3" x-text="p.summary"></p>
                            </template>
                            
                            <div class="mt-auto pt-4 flex flex-wrap gap-1.5" x-show="p.stack.length > 0">
                                <template x-for="(tech, index) in p.stack.slice(0, 4)" :key="index">
                                    <span class="text-xs text-ink-muted bg-canvas-muted px-2 py-0.5 rounded border border-line" x-text="tech"></span>
                                </template>
                                <template x-if="p.stack.length > 4">
                                    <span class="text-xs text-ink-muted bg-canvas-muted px-2 py-0.5 rounded border border-line" x-text="'+' + (p.stack.length - 4)"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            {{-- Empty State Search --}}
            <div x-show="filteredProjects.length === 0" style="display: none;" class="py-12 text-center text-ink-muted">
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
                    
                    get filteredProjects() {
                        return this.items.filter(i => {
                            const matchSearch = this.search === '' || 
                                i.title.toLowerCase().includes(this.search.toLowerCase()) || 
                                (i.summary && i.summary.toLowerCase().includes(this.search.toLowerCase()));
                            const matchType = this.type === '' || i.type_value === this.type;
                            return matchSearch && matchType;
                        });
                    }
                }));
            });
        </script>
        @endpush
    @endif
</x-layouts.public>
