<?php

namespace App\Filament\Resources\TeacherSchoolAssignments;

use App\Filament\Concerns\HasPanelRoleAccess;
use App\Filament\Resources\TeacherSchoolAssignments\Pages\CreateTeacherSchoolAssignment;
use App\Filament\Resources\TeacherSchoolAssignments\Pages\EditTeacherSchoolAssignment;
use App\Filament\Resources\TeacherSchoolAssignments\Pages\ListTeacherSchoolAssignments;
use App\Filament\Resources\TeacherSchoolAssignments\Schemas\TeacherSchoolAssignmentForm;
use App\Filament\Resources\TeacherSchoolAssignments\Tables\TeacherSchoolAssignmentsTable;
use App\Models\TeacherSchoolAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TeacherSchoolAssignmentResource extends Resource
{
    use HasPanelRoleAccess;

    protected static ?string $model = TeacherSchoolAssignment::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan Website';

    protected static ?string $navigationLabel = 'Assign Guru ke Sekolah';

    protected static ?string $modelLabel = 'assign guru ke sekolah';

    protected static ?string $pluralModelLabel = 'assign guru ke sekolah';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TeacherSchoolAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeacherSchoolAssignmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeacherSchoolAssignments::route('/'),
            'create' => CreateTeacherSchoolAssignment::route('/create'),
            'edit' => EditTeacherSchoolAssignment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::currentUserIsSuperAdmin();
    }
}
