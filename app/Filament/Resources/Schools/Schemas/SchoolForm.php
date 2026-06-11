<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama Sekolah')->required()->maxLength(255),
                TextInput::make('code')->label('Kode/NPSN')->maxLength(255),
                Select::make('level')->options(['SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA', 'SMK' => 'SMK', 'Lainnya' => 'Lainnya'])->required(),
                Textarea::make('address')->label('Alamat')->columnSpanFull(),
                TextInput::make('city')->label('Kota/Kabupaten'),
                TextInput::make('province')->label('Provinsi'),
                TextInput::make('email')->email(),
                TextInput::make('phone')->label('Kontak'),
                Select::make('status')->options(['active' => 'Aktif', 'inactive' => 'Nonaktif'])->required(),
            ]);
    }
}
