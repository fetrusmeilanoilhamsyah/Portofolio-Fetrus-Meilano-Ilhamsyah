<x-layouts.public :title="__('ui.page_contact') . ' — ' . config('app.name')">
    <x-page-header :title="__('ui.page_contact')" />

    @if($contacts->isEmpty())
        <x-empty-state :message="__('ui.empty_state')" />
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($contacts as $contact)
                <div x-data="{ 
                        copied: false, 
                        copy() { 
                            navigator.clipboard.writeText('{{ $contact->url }}').then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            });
                        } 
                    }" 
                    class="flex items-center justify-between p-4 bg-white dark:bg-primary-900/50 border border-primary-200 dark:border-primary-800 rounded-xl hover:border-accent-300 dark:hover:border-accent-700 transition-colors">
                    
                    <div class="flex items-center gap-4 overflow-hidden">
                        @if($contact->icon)
                            <div class="text-primary-500 shrink-0">
                                @svg($contact->icon, 'w-6 h-6')
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="font-medium text-primary-900 dark:text-primary-100 truncate">{{ $contact->label }}</h3>
                            <p class="text-sm text-primary-600 dark:text-primary-400 truncate">{{ $contact->url }}</p>
                            @if($contact->note)
                                <p class="text-xs text-primary-500 mt-1 truncate">{{ $contact->note }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <button @click="copy" class="shrink-0 ml-4 p-2 text-primary-500 hover:text-accent-600 dark:hover:text-accent-400 hover:bg-primary-50 dark:hover:bg-primary-800 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-accent-500" :aria-label="copied ? '{{ __('ui.copied') }}' : '{{ __('ui.btn_copy') }}'" :title="copied ? '{{ __('ui.copied') }}' : '{{ __('ui.btn_copy') }}'">
                        <template x-if="!copied">
                            @svg('lucide-copy', 'w-5 h-5')
                        </template>
                        <template x-if="copied">
                            @svg('lucide-check', 'w-5 h-5 text-green-500')
                        </template>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.public>
