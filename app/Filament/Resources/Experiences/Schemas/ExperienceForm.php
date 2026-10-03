<?php

namespace App\Filament\Resources\Experiences\Schemas;

use App\Enums\ExperienceKind;
use App\Services\ImageOptimizer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                Select::make('kind')
                                    ->label('Jenis')
                                    ->options(ExperienceKind::class)
                                    ->required(),
                                TextInput::make('organization')
                                    ->label('Organisasi / Perusahaan')
                                    ->required(),
                                TextInput::make('location')
                                    ->label('Lokasi'),
                                FileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'])
                                    ->maxSize(5120)
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        return app(ImageOptimizer::class)->optimizeAndSave($file, 'experiences');
                                    }),
                                DatePicker::make('started_at')
                                    ->label('Tanggal Mulai')
                                    ->required(),
                                DatePicker::make('ended_at')
                                    ->label('Tanggal Selesai')
                                    ->helperText('Biarkan kosong jika masih berlangsung'),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Bahasa Indonesia')
                            ->schema([
                                TextInput::make('title.id')
                                    ->label('Posisi / Gelar (ID)')
                                    ->required(),
                                MarkdownEditor::make('description.id')
                                    ->label('Deskripsi (ID)')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Bahasa Inggris')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Posisi / Gelar (EN)'),
                                MarkdownEditor::make('description.en')
                                    ->label('Deskripsi (EN)')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Publikasi')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Terbitkan')
                                    ->default(false),
                                Toggle::make('show_on_cv')
                                    ->label('Tampil di CV')
                                    ->default(true)
                                    ->helperText('Tampilkan pengalaman ini di halaman CV cetak.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
