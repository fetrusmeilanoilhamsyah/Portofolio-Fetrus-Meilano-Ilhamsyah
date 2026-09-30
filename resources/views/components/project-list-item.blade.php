@props(['project'])

<div class="group relative flex flex-col sm:flex-row gap-4 sm:gap-6 p-4 -mx-4 rounded-lg transition-colors hover:bg-ink/5">
    @if($project->cover_image)
        <div class="shrink-0 w-full sm:w-48 aspect-video sm:aspect-[4/3] rounded-md overflow-hidden bg-canvas-muted border border-line">
            <img 
                src="{{ media_url($project->cover_image) }}" 
                alt="{{ filled($project->cover_alt) ? $project->cover_alt : $project->title }}" 
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
            >
        </div>
    @endif
    
    <div class="flex flex-col flex-1 justify-center">
        <div class="flex items-center gap-2 mb-2">
            <x-tag>{{ $project->type->label() }}</x-tag>
            @if($project->started_at)
                <span class="text-xs text-ink-muted">
                    {{ $project->started_at->translatedFormat('F Y') }}
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
            <p class="text-sm text-ink-muted mb-3 line-clamp-2">
                {{ $project->summary }}
            </p>
        @endif
        
        <div class="flex flex-wrap gap-1.5 mt-auto">
            @if(is_array($project->stack))
                @foreach(array_slice($project->stack, 0, 5) as $tech)
                    <span class="text-xs text-ink-muted bg-canvas-muted px-2 py-0.5 rounded border border-line">
                        {{ $tech }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>
</div>
