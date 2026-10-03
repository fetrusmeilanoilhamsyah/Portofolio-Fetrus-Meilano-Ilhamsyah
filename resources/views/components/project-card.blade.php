@props(['project'])

<div class="relative flex flex-col h-full bg-surf rounded-lg border border-line overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-brand/40 group">
    <div class="relative w-full aspect-video bg-canvas border-b border-line overflow-hidden shrink-0">
        @if($project->is_featured)
            <div class="absolute top-3 right-3 z-20 inline-flex items-center gap-1.5 bg-brand text-brand-fg px-2.5 py-1 text-[11px] font-semibold rounded border border-brand-hover shadow-sm">
                <x-svg-icon name="lucide-pin" class="w-3.5 h-3.5" stroke-width="2.5" />
                {{ __('ui.featured', ['default' => 'Unggulan']) }}
            </div>
        @endif
        
        @if($project->cover_image)
            <img 
                src="{{ media_url($project->cover_image) }}" 
                alt="{{ filled($project->cover_alt) ? $project->cover_alt : $project->title }}" 
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                width="400"
                height="225"
                loading="lazy"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <x-svg-icon name="folder" class="w-10 h-10 text-ink-muted/30 transition-transform duration-500 group-hover:scale-110" />
            </div>
        @endif

        {{-- Overlay Hover --}}
        <div class="absolute inset-0 bg-ink/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 flex items-center justify-center">
            <div class="flex items-center gap-2 text-canvas font-semibold tracking-wide transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                <span>{{ app()->getLocale() === 'en' ? 'View Project' : 'Lihat Proyek' }}</span>
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </div>
        </div>
    </div>
    
    <div class="flex flex-col flex-1 p-5 md:p-6">
        <h3 class="text-lg font-bold text-ink group-hover:text-brand-ink transition-colors leading-tight mb-2">
            <a href="{{ localized_route('projects.show', [$project->slug]) }}" class="focus:outline-none before:absolute before:inset-0 before:z-0">
                {{ $project->title }}
            </a>
        </h3>
        
        @if($project->summary)
            <p class="text-sm text-ink-muted mb-5 line-clamp-2 leading-relaxed relative z-20">
                {{ $project->summary }}
            </p>
        @endif
        
        <div class="mt-auto flex flex-wrap items-center gap-2 relative z-20">
            <span class="inline-flex items-center text-[11px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-brand text-brand-fg">
                {{ $project->type->label() }}
            </span>
            @if(is_array($project->stack) && count($project->stack) > 0)
                <div class="flex flex-wrap items-center gap-1.5 ml-1">
                    @foreach(array_slice($project->stack, 0, 3) as $tag)
                        <span class="inline-flex items-center text-[11px] font-medium px-2 py-1 rounded bg-ink/5 text-ink-muted">
                            {{ $tag }}
                        </span>
                    @endforeach
                    @if(count($project->stack) > 3)
                        <span class="inline-flex items-center text-[11px] font-medium px-1 py-1 text-ink-muted">
                            +{{ count($project->stack) - 3 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
