<?php

namespace App\Filament\Resources\TeacherSchoolAssignments\Schemas;

use App\Models\Teacher;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeacherSchoolAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacher_id')->options(fn () => Teacher::with('user')->get()->pluck('user.name', 'id'))->label('Guru')->required()->searchable(),
                Select::make('school_id')->relationship('school', 'name')->label('Sekolah')->required()->searchable()->preload(),
                Select::make('status')->options(['active' => 'Aktif', 'inactive' => 'Nonaktif'])->required()->default('active'),
                Textarea::make('revoke_reason')->label('Alasan pencabutan')->columnSpanFull(),
            ]);
    }
}
