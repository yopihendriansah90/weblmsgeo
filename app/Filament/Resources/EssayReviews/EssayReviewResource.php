<?php

namespace App\Filament\Resources\EssayReviews;

use App\Filament\Resources\EssayReviews\Pages\CreateEssayReview;
use App\Filament\Resources\EssayReviews\Pages\EditEssayReview;
use App\Filament\Resources\EssayReviews\Pages\ListEssayReviews;
use App\Filament\Resources\EssayReviews\Schemas\EssayReviewForm;
use App\Filament\Resources\EssayReviews\Tables\EssayReviewsTable;
use App\Models\EssayReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EssayReviewResource extends Resource
{
    protected static ?string $model = EssayReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EssayReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EssayReviewsTable::configure($table);
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
            $query->whereHas('stepAttempt.quizAttempt.student', fn (Builder $studentQuery) => $studentQuery->whereIn('school_id', $schoolIds));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEssayReviews::route('/'),
            'create' => CreateEssayReview::route('/create'),
            'edit' => EditEssayReview::route('/{record}/edit'),
        ];
    }
}
