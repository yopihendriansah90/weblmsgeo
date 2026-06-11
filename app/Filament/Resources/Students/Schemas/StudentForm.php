<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Lengkap')->required()->dehydrated(false),
                TextInput::make('username')->required()->minLength(5)->maxLength(30)->regex('/^[a-zA-Z0-9._-]+$/')->dehydrated(false),
                TextInput::make('email')->email()->dehydrated(false),
                TextInput::make('password')->password()->required(fn (string $operation): bool => $operation === 'create')->dehydrated(false),
                Select::make('school_id')->relationship('school', 'name')->label('Asal Sekolah')->required()->searchable()->preload(),
                TextInput::make('nisn')->label('NIS/NISN'),
                TextInput::make('class_name')->label('Kelas'),
                Select::make('gender')->label('Jenis Kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                DatePicker::make('birth_date')->label('Tanggal Lahir'),
                TextInput::make('phone')->label('Nomor HP'),
                Select::make('status')->options(['active' => 'Aktif', 'inactive' => 'Nonaktif'])->required()->default('active'),
            ]);
    }
}
