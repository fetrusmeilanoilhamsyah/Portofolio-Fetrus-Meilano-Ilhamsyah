<x-layouts.public :title="$project->title . ' — ' . __('ui.page_projects') . ' — ' . config('app.name')" :description="$project->summary" :image="$project->cover_image" :isProject="true">
    <article class="max-w-3xl mx-auto">
        {{-- Header --}}
        <header class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <x-tag>{{ $project->type->label() }}</x-tag>
                @if($project->started_at)
                    <span class="text-sm text-ink-muted">
                        {{ $project->started_at->translatedFormat('M Y') }}
                        @if($project->ended_at)
                            — {{ $project->ended_at->translatedFormat('M Y') }}
                        @elseif($project->status->value === 'ongoing')
                            — {{ __('ui.present') }}
                        @endif
                    </span>
                @endif
            </div>
            
            <h1 class="text-3xl sm:text-4xl font-bold text-ink mb-4">{{ $project->title }}</h1>
            
            @if($project->summary)
                <p class="text-lg text-ink-muted mb-6">{{ $project->summary }}</p>
            @endif

            @if(is_array($project->stack) && count($project->stack) > 0)
                <div class="flex flex-wrap gap-2 mb-8">
                    @foreach($project->stack as $tech)
                        <span class="px-3 py-1 bg-canvas-muted border border-line rounded-full text-xs font-medium text-ink-muted">
                            {{ $tech }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Links --}}
            <div class="flex flex-wrap gap-3">
                @if($project->telegram_url)
                    <a href="{{ $project->telegram_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-medium rounded hover:bg-brand-hover transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        Coba Bot
                    </a>
                @endif
                @if($project->site_url)
                    <a href="{{ $project->site_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-ink text-canvas text-sm font-medium rounded hover:bg-ink-muted transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                        Kunjungi Situs
                    </a>
                @endif
                @if($project->demo_url)
                    <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-canvas text-ink text-sm font-medium rounded border border-line hover:bg-canvas-muted transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                        Demo
                    </a>
                @endif
                @if($project->repo_url)
                    <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-canvas text-ink text-sm font-medium rounded border border-line hover:bg-canvas-muted transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                        Lihat Kode
                    </a>
                @endif
            </div>
        </header>

        {{-- Gallery --}}
        @if($project->media->isNotEmpty())
            <div class="mb-12 space-y-6">
                @foreach($project->media as $media)
                    <figure class="rounded-lg overflow-hidden border border-line bg-canvas-muted">
                        @if($media->kind->value === 'image')
                            <div class="w-full aspect-video">
                                <img 
                                    src="{{ media_url($media->path) }}" 
                                    alt="{{ $media->alt ?: $project->title }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                    width="1280"
                                    height="720"
                                >
                            </div>
                        @elseif($media->kind->value === 'video')
                            <div class="w-full aspect-video bg-black relative" x-data="videoObserver()">
                                <video 
                                    x-ref="video"
                                    src="{{ media_url($media->path) }}"
                                    class="w-full h-full object-contain"
                                    loop muted playsinline preload="none"
                                    @if($media->poster) poster="{{ media_url($media->poster) }}" @endif
                                ></video>
                            </div>
                        @elseif($media->kind->value === 'embed')
                            <div class="w-full aspect-video bg-canvas relative" x-data="{ loaded: false }">
                                <template x-if="loaded">
                                    <iframe src="{{ $media->url }}" class="w-full h-full border-0" allowfullscreen allow="autoplay; encrypted-media; picture-in-picture" sandbox="allow-scripts allow-same-origin allow-presentation allow-popups" referrerpolicy="strict-origin-when-cross-origin" loading="lazy" title="{{ __('ui.video_player') ?? 'Video Player' }}"></iframe>
                                </template>
                                <button type="button" x-show="!loaded" class="absolute inset-0 w-full flex flex-col items-center justify-center cursor-pointer hover:bg-canvas-muted transition-colors group" @click="loaded = true" aria-label="{{ __('ui.load_interactive_media') ?? 'Muat Media Interaktif' }}">
                                    <div class="w-16 h-16 bg-brand text-white rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                                    </div>
                                    <span class="mt-4 text-sm font-medium text-ink">{{ __('ui.load_interactive_media') ?? 'Muat Media Interaktif' }}</span>
                                </button>
                            </div>
                        @endif
                        
                        @if($media->caption)
                            <figcaption class="px-4 py-3 text-sm text-center text-ink-muted border-t border-line bg-canvas">
                                {{ $media->caption }}
                            </figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @endif

        {{-- Body Content --}}
        @if($project->body)
            <div class="mb-16">
                <x-prose :content="$project->body" />
            </div>
        @endif

        {{-- Pagination --}}
        <nav class="border-t border-line mt-12 pt-8 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            @if($prevProject)
                <a href="{{ localized_route('projects.show', [$prevProject->slug]) }}" class="flex-1 p-4 rounded-lg border border-line hover:border-brand hover:bg-canvas-muted transition-all group flex flex-col items-start text-left">
                    <span class="text-xs text-ink-muted mb-1 font-medium tracking-wider uppercase">Proyek Sebelumnya</span>
                    <span class="font-bold text-ink group-hover:text-brand-ink">{{ $prevProject->title }}</span>
                </a>
            @else
                <div class="flex-1"></div>
            @endif

            @if($nextProject)
                <a href="{{ localized_route('projects.show', [$nextProject->slug]) }}" class="flex-1 p-4 rounded-lg border border-line hover:border-brand hover:bg-canvas-muted transition-all group flex flex-col items-end text-right">
                    <span class="text-xs text-ink-muted mb-1 font-medium tracking-wider uppercase">Proyek Berikutnya</span>
                    <span class="font-bold text-ink group-hover:text-brand-ink">{{ $nextProject->title }}</span>
                </a>
            @else
                <div class="flex-1"></div>
            @endif
        </nav>
    </article>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('videoObserver', () => ({
                observer: null,
                init() {
                    this.observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                this.$refs.video.play().catch(() => {});
                            } else {
                                this.$refs.video.pause();
                            }
                        });
                    }, { threshold: 0.1 });
                    this.observer.observe(this.$el);
                },
                destroy() {
                    if (this.observer) this.observer.disconnect();
                }
            }));
        });
    </script>
    @endpush
</x-layouts.public>
