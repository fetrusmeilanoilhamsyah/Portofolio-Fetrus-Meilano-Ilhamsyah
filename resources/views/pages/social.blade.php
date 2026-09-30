<x-layouts.public :title="__('ui.page_social') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_social')" />

    @if($accounts->isEmpty() && $channels->isEmpty())
        <x-empty-state :message="__('ui.empty_state')" />
    @else
        <div class="space-y-12">
            @if($accounts->isNotEmpty())
                <section>
                    <h2 class="text-xl font-semibold text-primary-900 dark:text-primary-50 mb-6">{{ __('ui.section_accounts') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($accounts as $account)
                            <a href="{{ $account->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-4 rounded-lg border border-primary-200 dark:border-primary-800 hover:border-accent-500 dark:hover:border-accent-500 hover:shadow-sm transition-all group">
                                @if($account->icon)
                                    <div class="text-primary-500 group-hover:text-accent-500 transition-colors">
                                        <x-svg-icon :name="$account->icon" class="w-6 h-6" />
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-medium text-primary-900 dark:text-primary-100 group-hover:text-accent-600 dark:group-hover:text-accent-400 transition-colors">{{ $account->label }}</h3>
                                    @if($account->note)
                                        <p class="text-sm text-primary-600 dark:text-primary-400">{{ $account->note }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($channels->isNotEmpty())
                <section>
                    <h2 class="text-xl font-semibold text-primary-900 dark:text-primary-50 mb-6">{{ __('ui.section_channels') }}</h2>
                    <div class="space-y-8">
                        @foreach($channels as $channel)
                            <div class="bg-white dark:bg-primary-900/50 rounded-xl border border-primary-200 dark:border-primary-800 overflow-hidden">
                                <div class="p-6 border-b border-primary-100 dark:border-primary-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        @if($channel->icon)
                                            <div class="text-primary-500">
                                                <x-svg-icon :name="$channel->icon" class="w-8 h-8" />
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-lg font-semibold text-primary-900 dark:text-primary-50">{{ $channel->label }}</h3>
                                            @if($channel->note)
                                                <p class="text-sm text-primary-600 dark:text-primary-400 mt-1">{{ $channel->note }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ $channel->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-accent-600 hover:bg-accent-700 dark:bg-accent-500 dark:hover:bg-accent-600 rounded-lg transition-colors whitespace-nowrap">
                                        {{ __('ui.btn_join') }}
                                        <x-svg-icon name="external-link" class="w-4 h-4 ml-2" />
                                    </a>
                                </div>
                                
                                @if($channel->highlights->isNotEmpty())
                                    <div class="divide-y divide-primary-100 dark:divide-primary-800">
                                        @foreach($channel->highlights as $highlight)
                                            <div class="p-6 hover:bg-primary-50 dark:hover:bg-primary-800/30 transition-colors">
                                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                                    <h4 class="font-medium text-primary-900 dark:text-primary-100">
                                                        @if($highlight->url)
                                                            <a href="{{ $highlight->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-accent-600 dark:hover:text-accent-400 transition-colors">
                                                                {{ $highlight->title }}
                                                            </a>
                                                        @else
                                                            {{ $highlight->title }}
                                                        @endif
                                                    </h4>
                                                    <span class="text-xs text-primary-500 dark:text-primary-400 whitespace-nowrap">
                                                        {{ $highlight->highlighted_at?->translatedFormat('d M Y') }}
                                                    </span>
                                                </div>
                                                @if($highlight->summary)
                                                    <div class="text-sm text-primary-700 dark:text-primary-300">
                                                        {{ $highlight->summary }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    @endif
</x-layouts.public>
