<?php

namespace App\Filament\Resources\TeacherSchoolAssignments\Pages;

use App\Filament\Resources\TeacherSchoolAssignments\TeacherSchoolAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeacherSchoolAssignments extends ListRecords
{
    protected static string $resource = TeacherSchoolAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
