<?php

namespace App\Filament\Resources\Lessons;

use App\Filament\Concerns\HasPanelRoleAccess;
use App\Filament\Resources\Lessons\Pages\CreateLesson;
use App\Filament\Resources\Lessons\Pages\EditLesson;
use App\Filament\Resources\Lessons\Pages\ListLessons;
use App\Filament\Resources\Lessons\Schemas\LessonForm;
use App\Filament\Resources\Lessons\Tables\LessonsTable;
use App\Models\Lesson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LessonResource extends Resource
{
    use HasPanelRoleAccess;

    protected static ?string $model = Lesson::class;

    protected static ?string $navigationLabel = 'Subbab';

    protected static ?string $modelLabel = 'subbab';

    protected static ?string $pluralModelLabel = 'subbab';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LessonsTable::configure($table);
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
            'index' => ListLessons::route('/'),
            'create' => CreateLesson::route('/create'),
            'edit' => EditLesson::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::currentUserIsTeacherOrAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
