<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertificateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('issuer'),
                TextEntry::make('category'),
                TextEntry::make('issued_at')
                    ->date(),
                TextEntry::make('expires_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('credential_url')
                    ->placeholder('-'),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('file')
                    ->placeholder('-'),
                TextEntry::make('alt')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('is_published')
                    ->boolean(),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
