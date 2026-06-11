<?php

namespace App\Filament\Resources\Teachers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                         TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('user.name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('user.username')->label('Username')->searchable(),
                TextColumn::make('teacher_code')->label('Kode Guru')->searchable(),
                TextColumn::make('phone')->label('HP'),
                TextColumn::make('active_assignments_count')->counts('activeAssignments')->label('Sekolah'),
                TextColumn::make('status')->badge(),
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
