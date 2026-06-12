<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('title')->label('Materi Pembelajaran')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('lessons_count')
                    ->label('Bab')
                    ->getStateUsing(fn ($record) => $record->modules()->where('type', 'lesson')->count()),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(['draft' => 'Draf', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
