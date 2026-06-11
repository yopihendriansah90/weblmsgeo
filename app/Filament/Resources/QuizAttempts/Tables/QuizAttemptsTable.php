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
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('student.user.name')->label('Siswa')->searchable(),
                TextColumn::make('student.school.name')->label('Sekolah')->searchable(),
                TextColumn::make('quiz.title')->label('Kuis')->searchable(),
                TextColumn::make('auto_score')->label('Otomatis'),
                TextColumn::make('essay_score')->label('Esai'),
                TextColumn::make('final_score')->label('Nilai Akhir'),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(['in_progress' => 'Sedang Dikerjakan', 'pending_review' => 'Menunggu Penilaian', 'completed' => 'Selesai']),
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
