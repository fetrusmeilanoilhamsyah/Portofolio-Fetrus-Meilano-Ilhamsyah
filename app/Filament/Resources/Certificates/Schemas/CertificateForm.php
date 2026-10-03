<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Models\Certificate;
use App\Services\ImageOptimizer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Sertifikat')
                                    ->required(),
                                TextInput::make('issuer')
                                    ->label('Penerbit')
                                    ->required(),
                                TextInput::make('category')
                                    ->label('Kategori')
                                    ->required()
                                    ->datalist(fn () => Certificate::query()->distinct()->pluck('category')->toArray()),
                                DatePicker::make('issued_at')
                                    ->label('Tanggal Terbit')
                                    ->required(),
                                DatePicker::make('expires_at')
                                    ->label('Tanggal Kadaluarsa')
                                    ->helperText('Kosongkan jika tidak ada kadaluarsa'),
                                TextInput::make('credential_url')
                                    ->label('URL Kredensial')
                                    ->url(),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Media')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Gambar Sertifikat')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'])
                                    ->maxSize(5120)
                                    ->required()
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        return app(ImageOptimizer::class)->optimizeAndSave($file, 'certificates');
                                    }),
                                TextInput::make('alt.id')
                                    ->label('Teks Alternatif (ID)')
                                    ->required(),
                                TextInput::make('alt.en')
                                    ->label('Teks Alternatif (EN)'),
                                FileUpload::make('file')
                                    ->label('File PDF (Opsional)')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('certificates')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(5120),
                            ]),
                        Tabs\Tab::make('Publikasi')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Terbitkan')
                                    ->default(false),
                                Toggle::make('show_on_cv')
                                    ->label('Tampil di CV')
                                    ->default(true)
                                    ->helperText('Tampilkan sertifikat ini di halaman CV cetak.'),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
