<x-layouts.public :title="__('ui.page_experience') . ' — ' . config('app.name')" maxWidth="max-w-6xl">
    <x-page-header :title="__('ui.page_experience')" :subtitle="__('ui.sub_experience')" />

    <div x-data="tabs()" class="mt-8">
        {{-- Tablist --}}
        <div role="tablist" class="flex flex-wrap gap-2 mb-8 border-b border-line pb-px" @keydown.right.prevent="nextTab()" @keydown.left.prevent="prevTab()">
            <button 
                id="tab-pengalaman" 
                role="tab" 
                :aria-selected="tab === 'pengalaman'" 
                aria-controls="panel-pengalaman" 
                @click="updateTab('pengalaman')" 
                :tabindex="tab === 'pengalaman' ? 0 : -1"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2 outline-none focus-visible:bg-canvas-muted rounded-t"
                :class="tab === 'pengalaman' ? 'border-brand text-brand-ink' : 'border-transparent text-ink-muted hover:text-ink hover:border-line'"
            >
                {{ __('ui.tab_experience') }}
            </button>
            <button 
                id="tab-sertifikat" 
                role="tab" 
                :aria-selected="tab === 'sertifikat'" 
                aria-controls="panel-sertifikat" 
                @click="updateTab('sertifikat')" 
                :tabindex="tab === 'sertifikat' ? 0 : -1"
                class="px-4 py-2 text-sm font-medium transition-colors border-b-2 outline-none focus-visible:bg-canvas-muted rounded-t"
                :class="tab === 'sertifikat' ? 'border-brand text-brand-ink' : 'border-transparent text-ink-muted hover:text-ink hover:border-line'"
            >
                {{ __('ui.tab_certificates') }}
            </button>
        </div>

        {{-- Panel Pengalaman --}}
        <div 
            id="panel-pengalaman" 
            role="tabpanel" 
            aria-labelledby="tab-pengalaman" 
            x-show="tab === 'pengalaman'"
            class="space-y-12"
            x-transition.opacity.duration.200ms
        >
            @if($experiences->isEmpty())
                <x-empty-state />
            @else
                @foreach(['kerja', 'magang', 'organisasi', 'pendidikan'] as $kind)
                    @if(isset($experiences[$kind]) && $experiences[$kind]->isNotEmpty())
                        <section>
                            <h2 class="text-xl font-bold mb-6 text-ink pb-2 border-b border-line">
                                {{ ucfirst($kind) }}
                            </h2>
                            <div class="space-y-0 mt-4">
                                @foreach($experiences[$kind] as $exp)
                                    <div class="py-8 first:pt-0 last:pb-0 border-b border-line last:border-0">
                                        
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-1">
                                                    @if($exp->logo)
                                                        <img src="{{ media_url($exp->logo) }}" alt="Logo {{ $exp->organization }}" class="w-8 h-8 rounded object-cover bg-canvas-muted" width="32" height="32" loading="lazy">
                                                    @endif
                                                    <h3 class="font-bold text-lg text-ink">{{ $exp->title }}</h3>
                                                </div>
                                                <div class="text-ink-muted text-sm flex flex-wrap items-center gap-2 mb-2">
                                                    <span class="font-medium text-ink">{{ $exp->organization }}</span>
                                                    @if($exp->location)
                                                        <span>•</span>
                                                        <span>{{ $exp->location }}</span>
                                                    @endif
                                                </div>
                                                
                                                @if($exp->description)
                                                    <div class="mt-4 prose-sm text-ink-muted">
                                                        <x-prose :content="$exp->description" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-sm text-ink-muted whitespace-nowrap bg-canvas-muted px-2.5 py-1 rounded-sm border border-line">
                                                {{ $exp->started_at->translatedFormat('M Y') }} — 
                                                {{ $exp->ended_at ? $exp->ended_at->translatedFormat('M Y') : __('ui.present') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            @endif
        </div>

        {{-- Panel Sertifikat --}}
        <div 
            id="panel-sertifikat" 
            role="tabpanel" 
            aria-labelledby="tab-sertifikat" 
            x-show="tab === 'sertifikat'"
            x-cloak
            x-transition.opacity.duration.200ms
        >
            @if($certificates->isEmpty())
                <x-empty-state />
            @else
                <div x-data="certificates({{ Js::from($certificates->map(fn($c) => ['id' => $c->id, 'title' => $c->title, 'issuer' => $c->issuer, 'category' => $c->category, 'date' => $c->issued_at->translatedFormat('M Y'), 'image' => $c->image ? media_url($c->image) : null, 'url' => $c->credential_url, 'pdf' => $c->file ? media_url($c->file) : null])) }})">
                    
                    {{-- Filter & Search --}}
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <div class="relative flex-1 max-w-sm">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-muted" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <input type="search" x-model="search" placeholder="Cari sertifikat..." class="w-full pl-9 pr-4 py-2 bg-canvas border border-line rounded text-sm focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand placeholder:text-ink-muted">
                        </div>
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="text-xs font-semibold text-ink-muted uppercase tracking-wider mr-2">KATEGORI</span>
                            <button @click="category = ''" class="px-3 py-1.5 text-xs rounded-full border transition-all duration-200 ease-out active:scale-[0.97] active:opacity-90 font-medium" :class="category === '' ? 'bg-brand text-brand-fg border-brand-hover' : 'bg-canvas text-ink-muted border-line hover:bg-ink/5'">Semua</button>
                            @foreach($certificates->pluck('category')->filter()->unique()->sort() as $cat)
                                <button @click="category = '{{ $cat }}'" class="px-3 py-1.5 text-xs rounded-full border transition-all duration-200 ease-out active:scale-[0.97] active:opacity-90 font-medium" :class="category === '{{ $cat }}' ? 'bg-brand text-brand-fg border-brand-hover' : 'bg-canvas text-ink-muted border-line hover:bg-ink/5'">{{ $cat }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($certificates as $cert)
                            <div 
                                x-show="showCert('{{ $cert->id }}')" 
                                x-transition.opacity.duration.200ms
                                class="bg-canvas border border-line rounded-lg relative flex flex-col h-full overflow-hidden transition-all duration-200 ease-out hover:-translate-y-0.5 hover:border-brand/40 active:scale-[0.99] active:opacity-90 group cursor-pointer" 
                                @click="openModalById('{{ $cert->id }}')"
                            >
                                <div class="relative w-full aspect-[4/3] bg-canvas border-b border-line flex items-center justify-center overflow-hidden shrink-0">
                                    @if($cert->image)
                                        <img src="{{ media_url($cert->image) }}" alt="{{ $cert->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" width="400" height="300" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-ink-muted/30">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 transition-transform duration-500 group-hover:scale-110" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-ink/0 group-hover:bg-ink/5 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <div class="bg-canvas/95 px-3 py-1.5 rounded-md text-xs font-medium text-ink border border-line transform translate-y-2 group-hover:translate-y-0 transition-all duration-200">Lihat detail</div>
                                    </div>
                                </div>
                                <div class="flex flex-col p-4 flex-1">
                                    <h3 class="font-semibold text-sm text-ink group-hover:text-brand-ink transition-colors line-clamp-2 leading-tight">{{ $cert->title }}</h3>
                                    <div class="mt-auto pt-3">
                                        <p class="text-[13px] text-ink-muted mb-1">{{ $cert->issuer }}</p>
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs text-ink-muted">{{ $cert->issued_at ? $cert->issued_at->translatedFormat('F Y') : '' }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded border border-line bg-canvas text-[10px] font-medium text-ink-muted uppercase tracking-wider">{{ $cert->category }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Empty State Search --}}
                    <div x-show="!hasResults" x-cloak class="py-12 text-center text-ink-muted">
                        Pencarian tidak menemukan hasil.
                    </div>

                    {{-- Modal --}}
                    <div x-trap.inert.noscroll="activeCert !== null" x-show="activeCert !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/60" @keydown.escape.window="closeModal()" aria-modal="true" role="dialog" aria-labelledby="modal-title">
                        <div class="bg-canvas border border-line rounded-lg w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden" @click.outside="closeModal()">
                            {{-- Modal Header --}}
                            <div class="flex items-start justify-between p-4 border-b border-line">
                                <div class="pr-4">
                                    <h2 id="modal-title" class="font-bold text-lg text-ink" x-text="activeCert?.title"></h2>
                                    <p class="text-sm text-ink-muted" x-text="activeCert?.issuer + ' • ' + activeCert?.date"></p>
                                </div>
                                <button @click="closeModal()" class="p-1 text-ink-muted hover:text-ink hover:bg-canvas-muted rounded" aria-label="Tutup">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                            </div>
                            
                            {{-- Modal Body --}}
                            <div class="flex-1 overflow-auto p-4 flex flex-col md:flex-row gap-6">
                                <div class="flex-1 bg-canvas-muted rounded-lg border border-line flex items-center justify-center p-4 min-h-[300px]">
                                    <template x-if="activeCert?.image">
                                        <img :src="activeCert.image" :alt="activeCert.title" class="max-w-full max-h-[60vh] object-contain rounded" width="800" height="600" loading="lazy">
                                    </template>
                                    <template x-if="!activeCert?.image">
                                        <div class="text-ink-muted flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                            <span class="text-sm">Tidak ada gambar</span>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="w-full md:w-64 shrink-0 flex flex-col gap-3">
                                    <template x-if="activeCert?.url">
                                        <a :href="activeCert.url" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-brand text-white text-sm font-medium rounded hover:bg-brand-hover transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h6"/><path d="m21 3-9 9"/><path d="M15 3h6v6"/></svg>
                                            Verifikasi
                                        </a>
                                    </template>
                                    <template x-if="activeCert?.pdf">
                                        <a :href="activeCert.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-canvas text-ink text-sm font-medium rounded border border-line hover:bg-canvas-muted transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            Unduh PDF
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tabs', () => ({
                tab: new URLSearchParams(window.location.search).get('tab') || 'pengalaman',
                init() {
                    this.$watch('tab', (val) => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('tab', val);
                        window.history.pushState({}, '', url);
                    });
                    
                    window.addEventListener('popstate', () => {
                        this.tab = new URLSearchParams(window.location.search).get('tab') || 'pengalaman';
                    });
                },
                updateTab(t) {
                    this.tab = t;
                },
                nextTab() {
                    this.tab = this.tab === 'pengalaman' ? 'sertifikat' : 'pengalaman';
                    this.$nextTick(() => document.getElementById(`tab-${this.tab}`).focus());
                },
                prevTab() {
                    this.tab = this.tab === 'sertifikat' ? 'pengalaman' : 'sertifikat';
                    this.$nextTick(() => document.getElementById(`tab-${this.tab}`).focus());
                }
            }));

            Alpine.data('certificates', (items) => ({
                items: items,
                search: '',
                category: '',
                activeCert: null,
                previousFocus: null,
                
                get filteredIds() {
                    return this.items.filter(i => {
                        const matchSearch = this.search === '' || i.title.toLowerCase().includes(this.search.toLowerCase()) || i.issuer.toLowerCase().includes(this.search.toLowerCase());
                        const matchCat = this.category === '' || i.category === this.category;
                        return matchSearch && matchCat;
                    }).map(i => String(i.id));
                },

                showCert(id) {
                    return this.filteredIds.includes(String(id));
                },

                get hasResults() {
                    return this.filteredIds.length > 0;
                },
                
                openModalById(id) {
                    this.activeCert = this.items.find(i => String(i.id) === String(id));
                    this.previousFocus = document.activeElement;
                    document.body.style.overflow = 'hidden';
                },
                
                closeModal() {
                    if (this.activeCert === null) return;
                    this.activeCert = null;
                    document.body.style.overflow = '';
                    if (this.previousFocus) {
                        this.$nextTick(() => this.previousFocus.focus());
                    }
                }
            }));
        });
    </script>
    @endpush
</x-layouts.public>
