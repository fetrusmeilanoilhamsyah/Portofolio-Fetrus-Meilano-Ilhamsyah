@props(['project'])

<x-card class="relative flex flex-col h-full overflow-hidden transition-all duration-200 ease-out hover:-translate-y-0.5 hover:border-brand/40 active:scale-[0.99] active:opacity-90 group">
    <div class="relative w-full aspect-video bg-canvas border-b border-line flex items-center justify-center overflow-hidden shrink-0">
        @if($project->is_featured)
            <div class="absolute top-3 right-3 z-10 inline-flex items-center gap-1.5 bg-brand text-brand-fg px-2.5 py-1 text-xs font-semibold rounded-md border border-brand-hover">
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
            <x-svg-icon name="folder" class="w-10 h-10 text-ink-muted/30 transition-transform duration-500 group-hover:scale-110" />
        @endif
    </div>
    
    <div class="flex flex-col flex-1 p-5">
        <div class="flex items-start justify-between gap-3 mb-2">
            <h3 class="text-base font-semibold text-ink group-hover:text-brand-ink transition-colors leading-tight">
                <a href="{{ localized_route('projects.show', [$project->slug]) }}" class="focus:outline-none">
                    <span class="absolute inset-0 z-0" aria-hidden="true"></span>
                    {{ $project->title }}
                </a>
            </h3>
        </div>
        
        @if($project->summary)
            <p class="text-sm text-ink-muted mb-4 line-clamp-2 leading-relaxed">
                {{ $project->summary }}
            </p>
        @endif
        
        <div class="mt-auto pt-3 flex flex-wrap items-center gap-2 relative z-10">
            <span class="inline-flex items-center px-2 py-0.5 rounded border border-line bg-canvas text-xs font-medium text-ink-muted">
                {{ $project->type->label() }}
            </span>
            @if($project->started_at)
                <span class="inline-flex items-center text-xs text-ink-muted">
                    <x-svg-icon name="lucide-calendar" class="w-3.5 h-3.5 mr-1" />
                    {{ $project->started_at->translatedFormat('Y') }}
                </span>
            @endif
        </div>
        
        @if($project->tags && count($project->tags) > 0)
            <div class="mt-3 flex flex-wrap gap-1.5 relative z-10 border-t border-line/50 pt-3">
                @foreach(array_slice($project->tags, 0, 4) as $tag)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-ink/5 text-[11px] font-medium text-ink-muted">
                        {{ $tag }}
                    </span>
                @endforeach
                @if(count($project->tags) > 4)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-transparent text-[11px] font-medium text-ink-muted">
                        +{{ count($project->tags) - 4 }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</x-card>
