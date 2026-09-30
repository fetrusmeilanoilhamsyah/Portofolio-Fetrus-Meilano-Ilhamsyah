<x-layouts.public :title="__('ui.page_experience') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_experience')" />

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
                            <div class="space-y-8 pl-4 border-l-2 border-line">
                                @foreach($experiences[$kind] as $exp)
                                    <div class="relative pl-6">
                                        {{-- Timeline dot --}}
                                        <div class="absolute w-3 h-3 bg-brand rounded-full -left-[23px] top-1.5 border-4 border-canvas"></div>
                                        
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-1">
                                                    @if($exp->logo)
                                                        <img src="{{ media_url($exp->logo) }}" alt="Logo {{ $exp->organization }}" class="w-8 h-8 rounded object-cover bg-canvas-muted">
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
                                                    <div class="mt-3">
                                                        <x-prose :content="$exp->description" class="text-sm" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-sm text-ink-muted whitespace-nowrap bg-canvas-muted px-2 py-1 rounded">
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
                        <div class="flex flex-wrap gap-2">
                            <button @click="category = ''" class="px-3 py-1.5 text-xs rounded-full border transition-colors" :class="category === '' ? 'bg-ink text-canvas border-ink' : 'bg-canvas text-ink-muted border-line hover:border-ink-muted'">Semua</button>
                            @foreach($certificates->pluck('category')->filter()->unique()->sort() as $cat)
                                <button @click="category = '{{ $cat }}'" class="px-3 py-1.5 text-xs rounded-full border transition-colors" :class="category === '{{ $cat }}' ? 'bg-ink text-canvas border-ink' : 'bg-canvas text-ink-muted border-line hover:border-ink-muted'">{{ $cat }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <template x-for="cert in filteredCertificates" :key="cert.id">
                            <div class="flex flex-col group cursor-pointer" @click="openModal(cert)">
                                <div class="w-full aspect-[4/3] bg-canvas-muted rounded-lg overflow-hidden border border-line mb-3 relative">
                                    <template x-if="cert.image">
                                        <img :src="cert.image" :alt="cert.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </template>
                                    <template x-if="!cert.image">
                                        <div class="w-full h-full flex items-center justify-center text-ink-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        </div>
                                    </template>
                                    <div class="absolute inset-0 bg-ink/0 group-hover:bg-ink/10 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <div class="bg-canvas/90 px-3 py-1.5 rounded-full text-xs font-medium text-ink shadow-sm backdrop-blur-sm transform translate-y-2 group-hover:translate-y-0 transition-all">Lihat detail</div>
                                    </div>
                                </div>
                                <h3 class="font-bold text-sm text-ink group-hover:text-brand-ink transition-colors line-clamp-2" x-text="cert.title"></h3>
                                <p class="text-xs text-ink-muted mt-1" x-text="cert.issuer"></p>
                                <p class="text-xs text-ink-muted mt-0.5" x-text="cert.date"></p>
                            </div>
                        </template>
                    </div>
                    
                    {{-- Empty State Search --}}
                    <div x-show="filteredCertificates.length === 0" style="display: none;" class="py-12 text-center text-ink-muted">
                        Pencarian tidak menemukan hasil.
                    </div>

                    {{-- Modal --}}
                    <div x-show="activeCert !== null" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/50 backdrop-blur-sm" @keydown.escape.window="closeModal()" aria-modal="true" role="dialog" aria-labelledby="modal-title">
                        <div class="bg-canvas border border-line rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden" @click.outside="closeModal()">
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
                                        <img :src="activeCert.image" :alt="activeCert.title" class="max-w-full max-h-[60vh] object-contain rounded">
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
                
                get filteredCertificates() {
                    return this.items.filter(i => {
                        const matchSearch = this.search === '' || i.title.toLowerCase().includes(this.search.toLowerCase()) || i.issuer.toLowerCase().includes(this.search.toLowerCase());
                        const matchCat = this.category === '' || i.category === this.category;
                        return matchSearch && matchCat;
                    });
                },
                
                openModal(cert) {
                    this.activeCert = cert;
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
