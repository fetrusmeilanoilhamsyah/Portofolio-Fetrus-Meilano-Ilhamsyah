<div 
    x-data="commandPalette()" 
    @keydown.window.prevent.ctrl.k="toggle()"
    @keydown.window.prevent.cmd.k="toggle()"
    @keydown.escape.window="close()"
    @open-palette.window="toggle()"
    x-cloak
>
    <!-- Overlay -->
    <div 
        x-show="isOpen" 
        x-transition.opacity.duration.200ms
        class="fixed inset-0 bg-stone-900/50 dark:bg-black/60 z-50"
        @click="close()"
    ></div>

    <!-- Modal -->
    <div 
        x-trap.inert.noscroll="isOpen"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('ui.command_palette') }}"
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/4 w-full max-w-xl bg-surf rounded-lg overflow-hidden z-50 border border-line flex flex-col"
        @click.stop
    >
        <!-- Search Input -->
        <div class="relative border-b border-line flex items-center px-4 py-3">
            <i data-lucide="search" class="w-5 h-5 text-ink-muted shrink-0"></i>
            <input 
                x-ref="searchInput"
                type="text" 
                role="combobox"
                aria-expanded="true"
                aria-controls="palette-results"
                :aria-activedescendant="'palette-item-' + selectedIndex"
                x-model="searchQuery"
                @input="search()"
                @keydown.down.prevent="selectNext()"
                @keydown.up.prevent="selectPrevious()"
                @keydown.enter.prevent="navigate()"
                class="w-full bg-transparent border-none focus:ring-0 text-ink placeholder-ink-muted px-4 py-2 outline-none"
                placeholder="{{ __('ui.search_placeholder', ['default' => 'Search pages and projects...']) }}"
                autocomplete="off"
                spellcheck="false"
            >
            <div class="flex gap-1 shrink-0">
                <kbd class="hidden sm:inline-block bg-line/50 rounded px-1.5 py-0.5 text-xs text-ink-muted font-mono font-medium">esc</kbd>
            </div>
        </div>

        <!-- Announce results -->
        <div class="sr-only" aria-live="polite" x-text="results.length + ' ' + '{{ __('ui.palette_results') }}'"></div>

        <!-- Loading State -->
        <div x-show="isLoading" class="p-6 text-center text-ink-muted flex flex-col items-center">
            <i data-lucide="loader-2" class="w-6 h-6 animate-spin mb-2 text-brand-ink"></i>
            <span>{{ __('ui.loading', ['default' => 'Loading...']) }}</span>
        </div>

        <!-- Results -->
        <div id="palette-results" role="listbox" x-show="!isLoading" class="max-h-80 overflow-y-auto overscroll-contain py-2" x-ref="resultsContainer">
            
            <template x-if="results.length > 0">
                <div>
                    <template x-for="(result, index) in results" :key="result.id">
                        <a 
                            role="option"
                            :aria-selected="selectedIndex === index"
                            :href="result.url"
                            class="flex items-center gap-3 px-4 py-3 cursor-pointer group"
                            :class="selectedIndex === index ? 'bg-line/50 text-brand-ink' : 'text-ink hover:bg-line/30'"
                            @mouseenter="selectedIndex = index"
                            @click="navigate()"
                            :id="'palette-item-' + index"
                        >
                            <div class="p-2 rounded-lg bg-surf border border-line group-hover:border-brand/30 shrink-0">
                                <i :data-lucide="result.icon" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate" x-text="result.title"></p>
                                <p class="text-xs text-ink-muted truncate" x-text="result.category"></p>
                            </div>
                            <i data-lucide="corner-down-left" class="w-4 h-4 text-ink-muted opacity-0 group-hover:opacity-100 transition-opacity" :class="selectedIndex === index ? 'opacity-100' : ''"></i>
                        </a>
                    </template>
                </div>
            </template>
            
            <template x-if="results.length === 0 && searchQuery.length > 0">
                <div class="px-6 py-8 text-center text-ink-muted">
                    <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-3 opacity-50"></i>
                    <p>{{ __('ui.no_results', ['default' => 'No results found.']) }}</p>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('commandPalette', () => ({
        isOpen: false,
        isLoading: false,
        searchQuery: '',
        data: [],
        results: [],
        selectedIndex: 0,
        hasFetched: false,

        init() {
            this.$watch('isOpen', value => {
                if (value) {
                    this.searchQuery = '';
                    this.selectedIndex = 0;
                    if (!this.hasFetched) {
                        this.fetchData();
                    } else {
                        this.search();
                    }
                    setTimeout(() => {
                        this.$refs.searchInput.focus();
                    }, 50);
                    // Prevent background scrolling
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        },

        toggle() {
            this.isOpen = !this.isOpen;
        },

        close() {
            this.isOpen = false;
        },

        async fetchData() {
            this.isLoading = true;
            try {
                const url = '{{ route(app()->getLocale() === 'en' ? 'en.command-palette' : 'command-palette') }}';
                const response = await fetch(url);
                this.data = await response.json();
                this.hasFetched = true;
                this.search();
                // Re-initialize lucide icons for newly added DOM elements if lucide is available
                if (window.lucide) {
                    setTimeout(() => window.lucide.createIcons(), 100);
                }
            } catch (error) {
                console.error('Failed to fetch command palette data:', error);
            } finally {
                this.isLoading = false;
            }
        },

        search() {
            if (!this.data) return;
            
            if (this.searchQuery.trim() === '') {
                this.results = this.data;
            } else {
                const query = this.searchQuery.toLowerCase();
                this.results = this.data.filter(item => 
                    item.title.toLowerCase().includes(query) || 
                    item.category.toLowerCase().includes(query)
                );
            }
            this.selectedIndex = 0;
            
            if (window.lucide) {
                setTimeout(() => window.lucide.createIcons(), 50);
            }
        },

        selectNext() {
            if (this.results.length === 0) return;
            this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
            this.scrollToSelected();
        },

        selectPrevious() {
            if (this.results.length === 0) return;
            this.selectedIndex = (this.selectedIndex - 1 + this.results.length) % this.results.length;
            this.scrollToSelected();
        },
        
        scrollToSelected() {
            this.$nextTick(() => {
                const container = this.$refs.resultsContainer;
                const selectedEl = document.getElementById('palette-item-' + this.selectedIndex);
                if (container && selectedEl) {
                    const containerTop = container.scrollTop;
                    const containerBottom = containerTop + container.clientHeight;
                    const elTop = selectedEl.offsetTop - container.offsetTop;
                    const elBottom = elTop + selectedEl.offsetHeight;

                    if (elTop < containerTop) {
                        container.scrollTop = elTop;
                    } else if (elBottom > containerBottom) {
                        container.scrollTop = elBottom - container.clientHeight;
                    }
                }
            });
        },

        navigate() {
            if (this.results.length > 0 && this.results[this.selectedIndex]) {
                window.location.href = this.results[this.selectedIndex].url;
            }
        }
    }));
});
</script>
@endpush
