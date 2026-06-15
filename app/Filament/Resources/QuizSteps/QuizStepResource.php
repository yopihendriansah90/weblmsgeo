<?php

namespace App\Filament\Resources\QuizSteps;

use App\Filament\Concerns\HasPanelRoleAccess;
use App\Filament\Resources\QuizSteps\Pages\CreateQuizStep;
use App\Filament\Resources\QuizSteps\Pages\EditQuizStep;
use App\Filament\Resources\QuizSteps\Pages\ListQuizSteps;
use App\Filament\Resources\QuizSteps\Schemas\QuizStepForm;
use App\Filament\Resources\QuizSteps\Tables\QuizStepsTable;
use App\Models\QuizStep;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuizStepResource extends Resource
{
    use HasPanelRoleAccess;

    protected static ?string $model = QuizStep::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return QuizStepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuizStepsTable::configure($table);
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
            'index' => ListQuizSteps::route('/'),
            'create' => CreateQuizStep::route('/create'),
            'edit' => EditQuizStep::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::currentUserIsTeacherOnly();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::currentUserIsTeacherOnly();
    }
}
