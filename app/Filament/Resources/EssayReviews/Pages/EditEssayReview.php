<?php

namespace App\Filament\Resources\EssayReviews\Pages;

use App\Filament\Resources\EssayReviews\EssayReviewResource;
use App\Services\EssayReviewService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEssayReview extends EditRecord
{
    protected static string $resource = EssayReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        app(EssayReviewService::class)->review(
            $record->stepAttempt,
            auth()->user(),
            (float) $data['score'],
            $data['feedback'] ?? null,
        );

        return $record->fresh();
    }
}
