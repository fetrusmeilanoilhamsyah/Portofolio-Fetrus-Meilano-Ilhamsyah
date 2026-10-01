<?php

namespace App\Filament\Pages;

use App\Events\ContentChanged;
use App\Models\SiteSetting;
use App\Services\ImageOptimizer;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.site-settings';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengaturan Situs';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Pengaturan Situs';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Pengaturan';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $setting = SiteSetting::current();

        $data = $setting->attributesToArray();
        foreach (['role', 'intro_home', 'about_body', 'open_to_work_note'] as $attribute) {
            $data[$attribute] = $setting->getTranslations($attribute);
        }

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Pengaturan')
                    ->tabs([
                        Tabs\Tab::make('Profil & Home')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required(),
                                TextInput::make('role.id')
                                    ->label('Peran (ID)')
                                    ->required(),
                                TextInput::make('role.en')
                                    ->label('Peran (EN)'),
                                MarkdownEditor::make('intro_home.id')
                                    ->label('Intro Home (ID)')
                                    ->required(),
                                MarkdownEditor::make('intro_home.en')
                                    ->label('Intro Home (EN)'),
                                TextInput::make('location')
                                    ->label('Lokasi (Singkat)'),
                            ]),
                        Tabs\Tab::make('Tentang & CV')
                            ->schema([
                                MarkdownEditor::make('about_body.id')
                                    ->label('Isi About (ID)')
                                    ->required(),
                                MarkdownEditor::make('about_body.en')
                                    ->label('Isi About (EN)'),
                                FileUpload::make('photo')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'])
                                    ->maxSize(5120)
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        return app(ImageOptimizer::class)->optimizeAndSave($file, 'site');
                                    }),
                                FileUpload::make('cv_file')
                                    ->label('File CV (PDF)')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('cv')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(5120),
                            ]),
                        Tabs\Tab::make('Keahlian & Status')
                            ->schema([
                                Repeater::make('skills')
                                    ->label('Daftar Keahlian')
                                    ->schema([
                                        TextInput::make('group')
                                            ->label('Nama Kelompok (Contoh: Frontend, Backend)')
                                            ->required(),
                                        TextInput::make('items')
                                            ->label('Keahlian (pisahkan dengan koma)')
                                            ->required(),
                                    ])
                                    ->collapsible(),
                                Toggle::make('open_to_work')
                                    ->label('Terbuka untuk Kerja'),
                                TextInput::make('open_to_work_note.id')
                                    ->label('Catatan Terbuka untuk Kerja (ID)'),
                                TextInput::make('open_to_work_note.en')
                                    ->label('Catatan Terbuka untuk Kerja (EN)'),
                                FileUpload::make('og_image')
                                    ->label('Gambar OG (Share Image)')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'])
                                    ->maxSize(5120)
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        return app(ImageOptimizer::class)->optimizeAndSave($file, 'site');
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = SiteSetting::current();

        // Handle translations properly
        foreach (['role', 'intro_home', 'about_body', 'open_to_work_note'] as $attribute) {
            if (isset($data[$attribute])) {
                $setting->setTranslations($attribute, $data[$attribute]);
                unset($data[$attribute]);
            }
        }

        $setting->fill($data);
        $setting->save();

        // No need to dispatch ContentChanged manually because SiteSetting uses DispatchesContentChanged trait

        Notification::make()
            ->title('Berhasil disimpan')
            ->success()
            ->send();
    }
}
