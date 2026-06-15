<?php

namespace App\Filament\Resources\EssayReviews;

use App\Filament\Concerns\HasPanelRoleAccess;
use App\Filament\Concerns\ScopesTeacherSchoolData;
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
    use HasPanelRoleAccess;
    use ScopesTeacherSchoolData;

    protected static ?string $model = EssayReview::class;

    protected static ?string $navigationLabel = 'Penilaian Esai';

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
        return static::scopeToTeacherSchools(parent::getEloquentQuery(), 'stepAttempt.quizAttempt.student');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEssayReviews::route('/'),
            'create' => CreateEssayReview::route('/create'),
            'edit' => EditEssayReview::route('/{record}/edit'),
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
