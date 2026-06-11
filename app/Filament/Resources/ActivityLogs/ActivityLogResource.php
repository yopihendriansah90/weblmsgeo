<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Concerns\HasPanelRoleAccess;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    use HasPanelRoleAccess;

    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Log Aktivitas';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')->label('Kategori')->badge(),
                TextColumn::make('description')->label('Aktivitas')->searchable(),
                TextColumn::make('causer.name')->label('Pengguna')->placeholder('-'),
                TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return static::currentUserIsSuperAdmin();
    }
}
