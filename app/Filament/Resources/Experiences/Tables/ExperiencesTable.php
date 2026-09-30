<?php

namespace App\Filament\Resources\Experiences\Tables;

use App\Enums\ExperienceKind;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Posisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organization')
                    ->label('Organisasi')
                    ->searchable(),
                TextColumn::make('kind')
                    ->label('Jenis')
                    ->badge()
                    ->sortable(),
                ImageColumn::make('logo')
                    ->label('Logo'),
                TextColumn::make('started_at')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('ended_at')
                    ->label('Selesai')
                    ->date()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('M d, Y') : 'Sekarang'),
                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kind')
                    ->label('Jenis')
                    ->options(ExperienceKind::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }
}
