<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold">Pintasan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Akses cepat untuk menambah konten</p>
            </div>
            
            <div class="flex gap-4">
                <x-filament::button tag="a" href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('create') }}">
                    Buat Proyek
                </x-filament::button>
                
                <x-filament::button tag="a" href="{{ \App\Filament\Resources\Certificates\CertificateResource::getUrl('create') }}" color="gray">
                    Buat Sertifikat
                </x-filament::button>
                
                <x-filament::button tag="a" href="{{ \App\Filament\Pages\SiteSettings::getUrl() }}" color="success">
                    Pengaturan Situs
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
