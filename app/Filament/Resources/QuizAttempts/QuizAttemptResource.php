<?php

namespace App\Filament\Resources\QuizAttempts;

use App\Filament\Concerns\HasPanelRoleAccess;
use App\Filament\Concerns\ScopesTeacherSchoolData;
use App\Filament\Resources\QuizAttempts\Pages\CreateQuizAttempt;
use App\Filament\Resources\QuizAttempts\Pages\EditQuizAttempt;
use App\Filament\Resources\QuizAttempts\Pages\ListQuizAttempts;
use App\Filament\Resources\QuizAttempts\Schemas\QuizAttemptForm;
use App\Filament\Resources\QuizAttempts\Tables\QuizAttemptsTable;
use App\Models\QuizAttempt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuizAttemptResource extends Resource
{
    use HasPanelRoleAccess;
    use ScopesTeacherSchoolData;

    protected static ?string $model = QuizAttempt::class;

    protected static ?string $navigationLabel = 'Hasil Kuis';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return QuizAttemptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuizAttemptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeToTeacherSchools(parent::getEloquentQuery(), 'student');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuizAttempts::route('/'),
            'create' => CreateQuizAttempt::route('/create'),
            'edit' => EditQuizAttempt::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::currentUserIsTeacherOnly();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::currentUserIsTeacherOnly();
    }
}
