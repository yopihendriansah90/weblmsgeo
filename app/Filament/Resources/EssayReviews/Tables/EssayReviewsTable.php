<?php

namespace App\Filament\Resources\EssayReviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EssayReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stepAttempt.quizAttempt.student.user.name')->label('Siswa')->searchable(),
                TextColumn::make('stepAttempt.quizAttempt.student.school.name')->label('Sekolah')->searchable(),
                TextColumn::make('stepAttempt.quizStep.title')->label('Essay')->searchable(),
                TextColumn::make('score')->label('Nilai'),
                TextColumn::make('status')->badge(),
                TextColumn::make('reviewer.name')->label('Guru'),
            ])
            ->filters([
                SelectFilter::make('status')->options(['pending_review' => 'Pending', 'reviewed' => 'Reviewed']),
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
