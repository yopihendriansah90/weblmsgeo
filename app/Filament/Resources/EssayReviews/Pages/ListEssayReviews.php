<?php

namespace App\Filament\Resources\EssayReviews\Pages;

use App\Filament\Resources\EssayReviews\EssayReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEssayReviews extends ListRecords
{
    protected static string $resource = EssayReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
