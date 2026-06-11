<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('Judul')->required()->maxLength(255),
                TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
                Select::make('status')->options(['draft' => 'Draf', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan'])->required()->default('draft'),
            ]);
    }
}
