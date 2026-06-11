<?php

namespace App\Filament\Resources\SuperAdmins\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SuperAdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Lengkap')->required()->maxLength(255),
                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->minLength(5)
                    ->maxLength(30)
                    ->regex('/^[a-zA-Z0-9._-]+$/')
                    ->unique(ignoreRecord: true),
                TextInput::make('email')->label('Email')->email()->nullable()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Aktif', 'inactive' => 'Nonaktif'])
                    ->required()
                    ->default('active'),
            ]);
    }
}
