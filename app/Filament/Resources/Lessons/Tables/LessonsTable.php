<?php

namespace App\Filament\Resources\Lessons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LessonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module.course.title')->label('Kursus')->searchable(),
                TextColumn::make('module.title')->label('Bab')->searchable(),
                TextColumn::make('title')->label('Subbab')->searchable()->sortable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('module_id')->relationship('module', 'title')->label('Bab')->searchable()->preload(),
                SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
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
