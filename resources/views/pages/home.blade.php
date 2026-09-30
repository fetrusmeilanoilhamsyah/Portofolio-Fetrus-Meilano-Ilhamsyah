<x-layouts.public :title="public_text($siteSetting?->name) ?? config('app.name')">
    @if($siteSetting)
        <div class="mb-16">
            @if(public_text($siteSetting->name))
                <h1 class="text-3xl font-bold tracking-tight text-ink mb-2">{{ public_text($siteSetting->name) }}</h1>
            @endif
            @if(public_text($siteSetting->role))
                <p class="text-lg font-medium text-brand-ink mb-6">{{ public_text($siteSetting->role) }}</p>
            @endif
            
            @if(public_text($siteSetting->intro_home))
                <div class="max-w-2xl text-ink-muted">
                    <x-prose :content="public_text($siteSetting->intro_home)" />
                </div>
            @endif
            
            <div class="mt-8 flex flex-wrap gap-4">
                <x-button as="a" href="{{ localized_route('about') }}" variant="secondary">
                    {{ __('ui.page_about') }}
                </x-button>
                <x-button as="a" href="{{ localized_route('experience') }}" variant="secondary">
                    {{ __('ui.experience') ?? 'Pengalaman' }}
                </x-button>
                <x-button as="a" href="{{ localized_route('projects') }}" variant="primary">
                    {{ __('ui.page_projects') }}
                </x-button>
            </div>
        </div>
    @endif

    @if($featuredProjects->isEmpty() && $recentProjects->isEmpty())
        @if(!$siteSetting)
            <x-page-header :title="__('ui.page_home')" />
        @endif
        <x-empty-state :message="__('ui.empty_coming_soon')" />
    @else
        @if($featuredProjects->isNotEmpty())
            <section class="mb-16">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-ink">{{ __('ui.featured_projects') }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredProjects as $project)
                        <x-project-card :project="$project" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($recentProjects->isNotEmpty())
            <section class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-ink">{{ __('ui.recent_projects') }}</h2>
                    <a href="{{ localized_route('projects') }}" class="text-sm font-medium text-brand-ink hover:underline">
                        {{ __('ui.view_all_projects') }} &rarr;
                    </a>
                </div>
                <div class="space-y-2">
                    @foreach($recentProjects as $project)
                        <x-project-list-item :project="$project" />
                    @endforeach
                </div>
            </section>
        @endif
    @endif
</x-layouts.public>
