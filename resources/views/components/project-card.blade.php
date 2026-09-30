@props(['project'])

<x-card class="relative flex flex-col h-full overflow-hidden transition-colors hover:border-ink-muted group">
    @if($project->cover_image)
        <div class="relative w-full aspect-video bg-canvas-muted overflow-hidden border-b border-line">
            <img 
                src="{{ media_url($project->cover_image) }}" 
                alt="{{ filled($project->cover_alt) ? $project->cover_alt : $project->title }}" 
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
            >
        </div>
    @endif
    
    <div class="flex flex-col flex-1 p-5">
        <div class="flex items-center gap-2 mb-3">
            <x-tag>{{ $project->type->label() }}</x-tag>
            @if($project->started_at)
                <span class="text-xs text-ink-muted">
                    {{ $project->started_at->translatedFormat('Y') }}
                </span>
            @endif
        </div>
        
        <h3 class="text-lg font-bold text-ink mb-2 group-hover:text-brand-ink transition-colors">
            <a href="{{ localized_route('projects.show', [$project->slug]) }}" class="focus:outline-none">
                <span class="absolute inset-0" aria-hidden="true"></span>
                {{ $project->title }}
            </a>
        </h3>
        
        @if($project->summary)
            <p class="text-sm text-ink-muted mb-4 line-clamp-3">
                {{ $project->summary }}
            </p>
        @endif
        
        <div class="mt-auto pt-4 flex flex-wrap gap-1.5">
            @if(is_array($project->stack))
                @foreach(array_slice($project->stack, 0, 4) as $tech)
                    <span class="text-xs text-ink-muted bg-canvas-muted px-2 py-0.5 rounded border border-line">
                        {{ $tech }}
                    </span>
                @endforeach
                @if(count($project->stack) > 4)
                    <span class="text-xs text-ink-muted bg-canvas-muted px-2 py-0.5 rounded border border-line">
                        +{{ count($project->stack) - 4 }}
                    </span>
                @endif
            @endif
        </div>
    </div>
</x-card>
