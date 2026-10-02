<x-layouts.public :title="__('ui.page_contact') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_contact')" :subtitle="__('ui.sub_contact')" />

    @if($contacts->isEmpty())
        <x-empty-state :message="__('ui.empty_state')" />
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($contacts as $contact)
                @php
                    $rawUrl = public_text($contact->url);
                    $href = $rawUrl;
                    $displayUrl = $rawUrl;
                    $isUrl = false;
                    
                    if (filter_var($rawUrl, FILTER_VALIDATE_EMAIL)) {
                        $href = 'mailto:' . $rawUrl;
                        $isUrl = true;
                    } elseif (preg_match('/^[0-9\+\-\s]+$/', $rawUrl) && strlen($rawUrl) >= 7) {
                        $href = 'tel:' . preg_replace('/[^0-9\+]/', '', $rawUrl);
                        $isUrl = true;
                    } elseif (filter_var($rawUrl, FILTER_VALIDATE_URL) || Str::startsWith($rawUrl, ['http', 'mailto:', 'tel:'])) {
                        $isUrl = true;
                        $displayUrl = str_replace(['mailto:', 'tel:'], '', $rawUrl);
                    }
                @endphp
                <x-card :padding="false" x-data="{ 
                        copied: false, 
                        copy() { 
                            navigator.clipboard.writeText('{{ $displayUrl }}').then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            });
                        } 
                    }" 
                    class="group flex items-center justify-between p-4 hover:border-brand transition-colors relative">
                    
                    @if($isUrl)
                        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="absolute inset-0 z-0"></a>
                    @endif

                    <div class="flex items-center gap-4 overflow-hidden z-10 pointer-events-none">
                        @if($contact->icon)
                            <div class="text-ink-muted shrink-0 group-hover:text-brand transition-colors">
                                <x-svg-icon :name="$contact->icon" class="w-6 h-6" />
                            </div>
                        @else
                            <div class="text-ink-muted shrink-0 group-hover:text-brand transition-colors">
                                <x-svg-icon name="link" class="w-6 h-6" />
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-medium text-ink group-hover:text-brand transition-colors truncate">{{ public_text($contact->label) }}</h3>
                            <p class="text-sm text-ink-muted truncate">{{ $displayUrl }}</p>
                            @if($contact->note)
                                <p class="text-xs text-brand-ink mt-1 truncate">{{ public_text($contact->note) }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 z-10">
                        @if($isUrl)
                            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="shrink-0 p-2 text-ink-muted hover:text-brand hover:bg-brand/10 rounded-lg transition-colors focus:outline-none" aria-label="Buka Tautan" title="Buka Tautan">
                                <x-svg-icon name="external-link" class="w-5 h-5" />
                            </a>
                        @endif
                        <button type="button" @click.prevent="copy" class="shrink-0 p-2 text-ink-muted hover:text-brand hover:bg-brand/10 rounded-lg transition-colors focus:outline-none" :aria-label="copied ? '{{ __('ui.copied') }}' : '{{ __('ui.btn_copy') }}'" :title="copied ? '{{ __('ui.copied') }}' : '{{ __('ui.btn_copy') }}'">
                            <template x-if="!copied">
                                <x-svg-icon name="copy" class="w-5 h-5" />
                            </template>
                            <template x-if="copied">
                                <x-svg-icon name="check" class="w-5 h-5 text-ok" />
                            </template>
                        </button>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layouts.public>
