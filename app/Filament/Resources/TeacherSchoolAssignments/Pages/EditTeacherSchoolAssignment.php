<?php

namespace App\Filament\Resources\TeacherSchoolAssignments\Pages;

use App\Filament\Resources\TeacherSchoolAssignments\TeacherSchoolAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherSchoolAssignment extends EditRecord
{
    protected static string $resource = TeacherSchoolAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
