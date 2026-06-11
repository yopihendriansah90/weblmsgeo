<?php

namespace App\Filament\Resources\QuizSteps\Pages;

use App\Filament\Resources\QuizSteps\QuizStepResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuizSteps extends ListRecords
{
    protected static string $resource = QuizStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
