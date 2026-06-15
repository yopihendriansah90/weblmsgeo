<?php

namespace App\Filament\Resources\TeacherSchoolAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeacherSchoolAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('teacher.user.name')->label('Guru')->searchable(),
                TextColumn::make('teacher.teacher_code')->label('Kode Guru')->toggleable(),
                TextColumn::make('school.name')->label('Sekolah')->searchable(),
                TextColumn::make('assigner.name')->label('Diassign Oleh'),
                TextColumn::make('status')->badge(),
                TextColumn::make('assigned_at')->label('Tanggal Assign')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['active' => 'Aktif', 'inactive' => 'Nonaktif']),
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
