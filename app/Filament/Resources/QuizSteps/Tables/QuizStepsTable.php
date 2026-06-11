<?php

namespace App\Filament\Resources\QuizSteps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuizStepsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quiz.title')->label('Kuis')->searchable(),
                TextColumn::make('title')->label('Step')->searchable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('quiz_id')->relationship('quiz', 'title')->label('Kuis')->searchable()->preload(),
                SelectFilter::make('type')->options([
                    'essay' => 'Essay',
                    'text_matching' => 'Penjodohan Teks',
                    'table_checklist' => 'Table Checklist',
                    'image_text_matching' => 'Penjodohan Gambar-Teks',
                ]),
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
