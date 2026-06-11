<?php

namespace App\Filament\Resources\SuperAdmins\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuperAdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('username')->label('Username')->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->placeholder('-'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('last_login_at')->label('Login Terakhir')->dateTime()->placeholder('-')->sortable(),
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
