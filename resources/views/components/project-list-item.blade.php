@props(['project'])

<div class="group relative flex gap-4 p-3 -mx-3 rounded-lg transition-all duration-200 ease-out hover:bg-ink/5 active:scale-[0.99] active:opacity-90">
    <div class="shrink-0 w-[72px] h-[72px] rounded-md overflow-hidden bg-canvas-muted border border-line flex items-center justify-center">
        @if($project->cover_image)
            <img 
                src="{{ media_url($project->cover_image) }}" 
                alt="{{ filled($project->cover_alt) ? $project->cover_alt : $project->title }}" 
                class="w-full h-full object-cover"
                width="72"
                height="72"
                loading="lazy"
            >
        @else
            <x-svg-icon name="folder" class="w-6 h-6 text-ink-muted/50" />
        @endif
    </div>
    
    <div class="flex flex-col flex-1 justify-center min-w-0">
        <h3 class="text-base font-semibold text-ink mb-1 group-hover:text-brand-ink transition-colors truncate">
            <a href="{{ localized_route('projects.show', [$project->slug]) }}" class="focus:outline-none">
                <span class="absolute inset-0" aria-hidden="true"></span>
                {{ $project->title }}
            </a>
        </h3>
        
        @if($project->summary)
            <p class="text-sm text-ink-muted mb-2 line-clamp-2">
                {{ $project->summary }}
            </p>
        @endif
        
        <div class="flex items-center gap-2 mt-auto">
            <span class="rounded-md bg-ink/5 px-2 py-0.5 text-xs text-ink-muted">{{ $project->type->label() }}</span>
            @if($project->started_at)
                <span class="text-xs text-ink-muted">
                    {{ $project->started_at->translatedFormat('Y') }}
                </span>
            @endif
        </div>
    </div>
</div>
