<?php

namespace App\Filament\Resources\QuizAttempts;

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
    protected static ?string $model = QuizAttempt::class;

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
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->hasRole('guru') && ! $user->hasRole('super_admin')) {
            $schoolIds = $user->teacher?->activeAssignments()->pluck('school_id') ?? collect();
            $query->whereHas('student', fn (Builder $studentQuery) => $studentQuery->whereIn('school_id', $schoolIds));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuizAttempts::route('/'),
            'create' => CreateQuizAttempt::route('/create'),
            'edit' => EditQuizAttempt::route('/{record}/edit'),
        ];
    }
}
