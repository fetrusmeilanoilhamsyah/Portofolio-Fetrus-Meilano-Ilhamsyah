<x-layouts.public :title="__('ui.page_social') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_social')" />

    @if($accounts->isEmpty() && $channels->isEmpty())
        <x-empty-state :message="__('ui.empty_state')" />
    @else
        <div class="space-y-12">
            @if($accounts->isNotEmpty())
                <section>
                    <h2 class="text-xl font-semibold text-ink mb-6">{{ __('ui.section_accounts') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($accounts as $account)
                            <a href="{{ $account->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 p-4 rounded-lg border border-line hover:border-brand hover:bg-ink/5 transition-all group">
                                @if($account->icon)
                                    <div class="text-ink-muted group-hover:text-brand-ink transition-colors">
                                        <x-svg-icon :name="$account->icon" class="w-6 h-6" />
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-medium text-ink group-hover:text-brand-hover transition-colors">{{ public_text($account->label) }}</h3>
                                    @if($account->note)
                                        <p class="text-sm text-ink-muted">{{ public_text($account->note) }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($channels->isNotEmpty())
                <section>
                    <h2 class="text-xl font-semibold text-ink mb-6">{{ __('ui.section_channels') }}</h2>
                    <div class="space-y-8">
                        @foreach($channels as $channel)
                            <x-card :padding="false" class="overflow-hidden">
                                <div class="p-6 border-b border-line flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        @if($channel->icon)
                                            <div class="text-brand-ink">
                                                <x-svg-icon :name="$channel->icon" class="w-8 h-8" />
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-lg font-semibold text-ink">{{ public_text($channel->label) }}</h3>
                                            @if($channel->note)
                                                <p class="text-sm text-ink-muted mt-1">{{ public_text($channel->note) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <x-button :href="$channel->url" target="_blank" rel="noopener noreferrer" variant="primary">
                                        {{ __('ui.btn_join') }}
                                        <x-svg-icon name="external-link" class="w-4 h-4 ml-2" />
                                    </x-button>
                                </div>
                                
                                @if($channel->highlights->isNotEmpty())
                                    <div class="divide-y divide-line">
                                        @foreach($channel->highlights as $highlight)
                                            <div class="p-6 hover:bg-ink/5 transition-colors">
                                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                                    <h4 class="font-medium text-ink">
                                                        @if($highlight->url)
                                                            <a href="{{ $highlight->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-brand-hover transition-colors">
                                                                {{ public_text($highlight->title) }}
                                                            </a>
                                                        @else
                                                            {{ public_text($highlight->title) }}
                                                        @endif
                                                    </h4>
                                                    <span class="text-xs text-ink-muted whitespace-nowrap">
                                                        {{ $highlight->highlighted_at?->translatedFormat('d M Y') }}
                                                    </span>
                                                </div>
                                                @if($highlight->summary)
                                                    <div class="text-sm text-ink-muted">
                                                        {{ public_text($highlight->summary) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </x-card>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    @endif
</x-layouts.public>
