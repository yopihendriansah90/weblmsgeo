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
                Select::make('teacher_id')
                    ->options(fn () => Teacher::query()
                        ->with('user')
                        ->where('status', 'active')
                        ->get()
                        ->sortBy(fn (Teacher $teacher) => $teacher->user?->name)
                        ->mapWithKeys(fn (Teacher $teacher) => [
                            $teacher->id => trim(($teacher->user?->name ?? 'Tanpa Nama').' - '.($teacher->teacher_code ?? 'Tanpa Kode')),
                        ]))
                    ->label('Guru')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('school_id')->relationship('school', 'name')->label('Sekolah')->required()->searchable()->preload(),
                Select::make('status')->options(['active' => 'Aktif', 'inactive' => 'Nonaktif'])->required()->default('active'),
                Textarea::make('revoke_reason')->label('Alasan pencabutan')->columnSpanFull(),
            ]);
    }
}
