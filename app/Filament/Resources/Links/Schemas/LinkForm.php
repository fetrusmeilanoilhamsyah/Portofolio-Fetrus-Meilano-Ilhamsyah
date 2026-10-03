<?php

namespace App\Filament\Resources\Links\Schemas;

use App\Enums\LinkGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class LinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                Select::make('group')
                                    ->label('Grup')
                                    ->options(LinkGroup::class)
                                    ->required(),
                                TextInput::make('label')
                                    ->label('Label')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required(),
                                Select::make('icon')
                                    ->label('Ikon')
                                    ->options([
                                        'github' => 'GitHub',
                                        'linkedin' => 'LinkedIn',
                                        'twitter' => 'Twitter/X',
                                        'youtube' => 'YouTube',
                                        'instagram' => 'Instagram',
                                        'tiktok' => 'TikTok',
                                        'facebook' => 'Facebook',
                                        'mail' => 'Email',
                                        'phone' => 'Telepon',
                                        'map-pin' => 'Lokasi',
                                        'globe' => 'Situs Web',
                                        'file-text' => 'Dokumen',
                                        'link' => 'Tautan',
                                        'message-circle' => 'Pesan',
                                        'whatsapp' => 'WhatsApp',
                                    ])
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Catatan (ID)')
                            ->schema([
                                Textarea::make('note.id')
                                    ->label('Catatan (ID)')
                                    ->helperText('Contoh: disclaimer, jam kerja, dll'),
                            ]),
                        Tabs\Tab::make('Catatan (EN)')
                            ->schema([
                                Textarea::make('note.en')
                                    ->label('Catatan (EN)'),
                            ]),
                        Tabs\Tab::make('Publikasi')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Terbitkan')
                                    ->default(false),
                                Toggle::make('show_on_cv')
                                    ->label('Tampil di CV')
                                    ->default(false)
                                    ->helperText('Tampilkan tautan ini di header CV. Hanya bermakna untuk grup Kontak dan Akun.'),
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
