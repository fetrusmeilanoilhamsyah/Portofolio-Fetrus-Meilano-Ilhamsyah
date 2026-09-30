<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(ProjectType::class),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ProjectStatus::class),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Project $record): string => url('/projects/'.$record->slug))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Terbitkan')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) {
                            $skipped = [];
                            foreach ($records as $record) {
                                // cover_alt disimpan sebagai array json oleh spatie translatable
                                $altId = $record->getTranslation('cover_alt', 'id', false);
                                if (empty($record->cover_image) || empty($altId)) {
                                    $skipped[] = $record->title;
                                } else {
                                    $record->update(['status' => ProjectStatus::Published->value]);
                                }
                            }

                            if (count($skipped) > 0) {
                                Notification::make()
                                    ->warning()
                                    ->title('Beberapa proyek dilewati')
                                    ->body('Proyek berikut tidak diterbitkan karena tidak memiliki cover_image atau cover_alt.id: '.implode(', ', $skipped))
                                    ->send();
                            }
                        }),
                    BulkAction::make('draft')
                        ->label('Jadikan Draft')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn (Collection $records) => $records->each->update(['status' => ProjectStatus::Draft])),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }
}
