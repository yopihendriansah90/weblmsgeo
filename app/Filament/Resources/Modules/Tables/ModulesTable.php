<?php

namespace App\Filament\Resources\Modules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')->label('Kursus')->searchable(),
                TextColumn::make('title')->label('Bab')->searchable()->sortable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('course_id')->relationship('course', 'title')->label('Kursus')->searchable()->preload(),
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
