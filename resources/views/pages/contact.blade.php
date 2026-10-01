<x-layouts.public :title="__('ui.page_contact') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_contact')" />

    @if($contacts->isEmpty())
        <x-empty-state :message="__('ui.empty_state')" />
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($contacts as $contact)
                <x-card :padding="false" x-data="{ 
                        copied: false, 
                        copy() { 
                            navigator.clipboard.writeText('{{ $contact->url }}').then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            });
                        } 
                    }" 
                    class="flex items-center justify-between p-4 hover:border-brand transition-colors cursor-pointer"
                    @click="copy">
                    
                    <div class="flex items-center gap-4 overflow-hidden">
                        @if($contact->icon)
                            <div class="text-ink-muted shrink-0">
                                <x-svg-icon :name="$contact->icon" class="w-6 h-6" />
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-medium text-ink truncate">{{ public_text($contact->label) }}</h3>
                            <p class="text-sm text-ink-muted truncate">{{ public_text($contact->url) }}</p>
                            @if($contact->note)
                                <p class="text-xs text-brand-ink mt-1 truncate">{{ public_text($contact->note) }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <button type="button" class="shrink-0 ml-4 p-2 text-ink-muted hover:text-brand-hover hover:bg-ink/5 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand" :aria-label="copied ? '{{ __('ui.copied') }}' : '{{ __('ui.btn_copy') }}'" :title="copied ? '{{ __('ui.copied') }}' : '{{ __('ui.btn_copy') }}'">
                        <template x-if="!copied">
                            <x-svg-icon name="copy" class="w-5 h-5" />
                        </template>
                        <template x-if="copied">
                            <x-svg-icon name="check" class="w-5 h-5 text-ok" />
                        </template>
                    </button>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layouts.public>
