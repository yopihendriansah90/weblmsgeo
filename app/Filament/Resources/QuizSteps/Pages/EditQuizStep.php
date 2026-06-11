<?php

namespace App\Filament\Resources\QuizSteps\Pages;

use App\Filament\Resources\QuizSteps\QuizStepResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuizStep extends EditRecord
{
    protected static string $resource = QuizStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
