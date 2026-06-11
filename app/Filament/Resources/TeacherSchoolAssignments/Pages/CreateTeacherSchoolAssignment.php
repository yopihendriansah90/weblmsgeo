<?php

namespace App\Filament\Resources\TeacherSchoolAssignments\Pages;

use App\Filament\Resources\TeacherSchoolAssignments\TeacherSchoolAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacherSchoolAssignment extends CreateRecord
{
    protected static string $resource = TeacherSchoolAssignmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['assigned_by'] = auth()->id();
        $data['assigned_at'] = now();

        return $data;
    }
}
