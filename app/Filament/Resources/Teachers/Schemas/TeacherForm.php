<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Lengkap')->required()->dehydrated(false),
                TextInput::make('username')->required()->minLength(5)->maxLength(30)->regex('/^[a-zA-Z0-9._-]+$/')->dehydrated(false),
                TextInput::make('email')->email()->dehydrated(false),
                TextInput::make('password')->password()->required(fn (string $operation): bool => $operation === 'create')->dehydrated(false),
                TextInput::make('teacher_code')->label('NIP/NUPTK/Kode Guru'),
                TextInput::make('phone')->label('Nomor HP'),
                Select::make('status')->options(['active' => 'Aktif', 'inactive' => 'Nonaktif'])->required()->default('active'),
            ]);
    }
}
