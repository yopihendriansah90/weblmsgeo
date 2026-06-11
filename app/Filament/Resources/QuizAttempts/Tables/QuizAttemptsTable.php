<?php

namespace App\Filament\Resources\QuizAttempts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuizAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.user.name')->label('Siswa')->searchable(),
                TextColumn::make('student.school.name')->label('Sekolah')->searchable(),
                TextColumn::make('quiz.title')->label('Kuis')->searchable(),
                TextColumn::make('auto_score')->label('Auto'),
                TextColumn::make('essay_score')->label('Essay'),
                TextColumn::make('final_score')->label('Final'),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['in_progress' => 'In Progress', 'pending_review' => 'Pending Review', 'completed' => 'Completed']),
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
