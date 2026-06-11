<?php

namespace App\Filament\Resources\SuperAdmins;

use App\Filament\Concerns\HasPanelRoleAccess;
use App\Filament\Resources\SuperAdmins\Pages\CreateSuperAdmin;
use App\Filament\Resources\SuperAdmins\Pages\EditSuperAdmin;
use App\Filament\Resources\SuperAdmins\Pages\ListSuperAdmins;
use App\Filament\Resources\SuperAdmins\Schemas\SuperAdminForm;
use App\Filament\Resources\SuperAdmins\Tables\SuperAdminsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuperAdminResource extends Resource
{
    use HasPanelRoleAccess;

    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Akun Super Admin';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function form(Schema $schema): Schema
    {
        return SuperAdminForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuperAdminsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role('super_admin');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuperAdmins::route('/'),
            'create' => CreateSuperAdmin::route('/create'),
            'edit' => EditSuperAdmin::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::currentUserIsSuperAdmin();
    }
}
