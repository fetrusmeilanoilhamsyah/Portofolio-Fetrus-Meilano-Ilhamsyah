<?php

namespace App\Filament\Resources\Links\RelationManagers;

use App\Enums\LinkGroup;
use App\Filament\Concerns\FillsTranslatableAttributes;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LinkHighlightsRelationManager extends RelationManager
{
    protected static string $relationship = 'highlights';

    protected static ?string $title = 'Sorotan Tautan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Indonesia')
                            ->schema([
                                Forms\Components\TextInput::make('title.id')
                                    ->label('Judul (ID)')
                                    ->required(),
                                Forms\Components\Textarea::make('summary.id')
                                    ->label('Ringkasan (ID)')
                                    ->required(),
                            ]),
                        Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('title.en')
                                    ->label('Judul (EN)'),
                                Forms\Components\Textarea::make('summary.en')
                                    ->label('Ringkasan (EN)'),
                            ]),
                        Tab::make('Pengaturan')
                            ->schema([
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->url(),
                                Forms\Components\DatePicker::make('highlighted_at')
                                    ->label('Tanggal Sorotan')
                                    ->required(),
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Terbitkan')
                                    ->default(false),
                                Forms\Components\TextInput::make('sort_order')
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('highlighted_at')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->group === LinkGroup::Saluran)
                    ->mutateFormDataUsing(function (array $data): array {
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        // Mutate record data before filling form for editing
                        // We use the same FillsTranslatableAttributes trait logic, but it's easier to just do it via mutateRecordDataUsing since FillsTranslatableAttributes is a page trait
                        $record = $this->getOwnerRecord()->highlights()->find($data['id']);
                        $data['title'] = $record->getTranslations('title');
                        $data['summary'] = $record->getTranslations('summary');

                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    protected function canCreate(): bool
    {
        return $this->getOwnerRecord()->group === LinkGroup::Saluran->value || $this->getOwnerRecord()->group === LinkGroup::Saluran;
    }
}
