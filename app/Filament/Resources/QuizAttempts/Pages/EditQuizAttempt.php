<?php

namespace App\Filament\Resources\QuizAttempts\Pages;

use App\Filament\Resources\QuizAttempts\QuizAttemptResource;
use Filament\Resources\Pages\EditRecord;

class EditQuizAttempt extends EditRecord
{
    protected static string $resource = QuizAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
