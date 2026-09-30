<x-layouts.public :title="$siteSetting?->name ?? config('app.name')">
    @if($siteSetting)
        <div class="mb-16">
            <h1 class="text-3xl font-bold tracking-tight text-ink mb-2">{{ $siteSetting->name }}</h1>
            @if($siteSetting->role)
                <p class="text-lg font-medium text-brand mb-6">{{ $siteSetting->role }}</p>
            @endif
            
            @if($siteSetting->intro_home)
                <div class="max-w-2xl text-ink-muted">
                    <x-prose :content="$siteSetting->intro_home" />
                </div>
            @endif
            
            <div class="mt-8 flex gap-4">
                <x-button as="a" href="{{ route('about') }}" variant="secondary">
                    {{ __('ui.page_about') }}
                </x-button>
                <x-button as="a" href="{{ route('projects') }}" variant="primary">
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
                    <a href="{{ route('projects') }}" class="text-sm font-medium text-brand hover:underline">
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
