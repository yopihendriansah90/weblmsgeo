<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
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
                TextColumn::make('school.name')->label('Sekolah')->searchable()->sortable(),
                TextColumn::make('class_name')->label('Kelas')->searchable(),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('school_id')->relationship('school', 'name')->label('Sekolah')->searchable()->preload(),
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
