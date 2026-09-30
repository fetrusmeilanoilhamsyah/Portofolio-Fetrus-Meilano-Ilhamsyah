<?php

namespace App\Filament\Resources\Links\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LinkInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('group')
                    ->badge(),
                TextEntry::make('label'),
                TextEntry::make('url'),
                TextEntry::make('icon'),
                TextEntry::make('note')
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
