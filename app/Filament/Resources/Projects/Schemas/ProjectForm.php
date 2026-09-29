<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\MediaKind;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Services\ImageOptimizer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Umum')
                            ->schema([
                                TextInput::make('title.id')
                                    ->label('Judul (ID)')
                                    ->required(),
                                TextInput::make('title.en')
                                    ->label('Judul (EN)'),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Dihasilkan otomatis saat disimpan dari Judul (ID).'),
                                Select::make('type')
                                    ->label('Tipe')
                                    ->options(ProjectType::class)
                                    ->required(),
                                Textarea::make('summary.id')
                                    ->label('Ringkasan (ID)')
                                    ->required()
                                    ->rows(3),
                                Textarea::make('summary.en')
                                    ->label('Ringkasan (EN)')
                                    ->rows(3),
                                FileUpload::make('cover_image')
                                    ->label('Cover')
                                    ->image()
                                    ->maxSize(5120)
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        return app(ImageOptimizer::class)->optimizeAndSave($file, 'covers');
                                    })
                                    ->validationMessages([
                                        'max.file' => 'Ukuran cover maksimal 5 MB.',
                                    ])
                                    ->required(),
                                TextInput::make('cover_alt.id')
                                    ->label('Cover Alt (ID)')
                                    ->required(),
                                TextInput::make('cover_alt.en')
                                    ->label('Cover Alt (EN)'),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Konten')
                            ->schema([
                                MarkdownEditor::make('body.id')
                                    ->label('Konten (ID)')
                                    ->columnSpanFull(),
                                MarkdownEditor::make('body.en')
                                    ->label('Konten (EN)')
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Tautan')
                            ->schema([
                                TextInput::make('telegram_url')->label('Telegram URL')->url(),
                                TextInput::make('site_url')->label('Situs URL')->url(),
                                TextInput::make('demo_url')->label('Demo URL')->url(),
                                TextInput::make('repo_url')->label('Repositori URL')->url(),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Media')
                            ->schema([
                                Repeater::make('media')
                                    ->relationship()
                                    ->label('Galeri Media')
                                    ->schema([
                                        Select::make('kind')
                                            ->label('Jenis Media')
                                            ->options(MediaKind::class)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('path', null)),
                                        FileUpload::make('path')
                                            ->label(fn (Get $get) => $get('kind') === MediaKind::Video->value ? 'File Video' : 'File Gambar')
                                            ->visible(fn (Get $get) => in_array($get('kind'), [MediaKind::Image->value, MediaKind::Video->value]))
                                            ->required(fn (Get $get) => in_array($get('kind'), [MediaKind::Image->value, MediaKind::Video->value]))
                                            ->acceptedFileTypes(fn (Get $get) => $get('kind') === MediaKind::Video->value ? ['video/mp4'] : ['image/*'])
                                            ->maxSize(fn (Get $get) => $get('kind') === MediaKind::Video->value ? 8192 : 5120)
                                            ->validationMessages([
                                                'max.file' => 'Ukuran file melebihi batas (Gambar max 5MB, Video max 8MB).',
                                            ])
                                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, Get $get) {
                                                if ($get('kind') === MediaKind::Image->value) {
                                                    return app(ImageOptimizer::class)->optimizeAndSave($file, 'media');
                                                }

                                                return $file->store('media', 'public');
                                            }),
                                        TextInput::make('url')
                                            ->label('URL Embed')
                                            ->url()
                                            ->visible(fn (Get $get) => $get('kind') === MediaKind::Embed->value)
                                            ->required(fn (Get $get) => $get('kind') === MediaKind::Embed->value),
                                        TextInput::make('alt.id')
                                            ->label('Alt Teks (ID)')
                                            ->required(fn (Get $get) => in_array($get('kind'), [MediaKind::Image->value, MediaKind::Video->value]))
                                            ->visible(fn (Get $get) => in_array($get('kind'), [MediaKind::Image->value, MediaKind::Video->value])),
                                        TextInput::make('alt.en')
                                            ->label('Alt Teks (EN)')
                                            ->visible(fn (Get $get) => in_array($get('kind'), [MediaKind::Image->value, MediaKind::Video->value])),
                                        TextInput::make('caption.id')
                                            ->label('Caption (ID)'),
                                        TextInput::make('caption.en')
                                            ->label('Caption (EN)'),
                                    ])
                                    ->orderColumn('sort_order')
                                    ->columnSpanFull()
                                    ->columns(2),
                            ]),
                        Tabs\Tab::make('Publikasi')
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options(ProjectStatus::class)
                                    ->default(ProjectStatus::Draft->value)
                                    ->required(),
                                DateTimePicker::make('published_at')
                                    ->label('Tanggal Terbit')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Terisi otomatis saat pertama kali diterbitkan.'),
                                Toggle::make('is_featured')
                                    ->label('Jadikan Unggulan')
                                    ->default(false),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
