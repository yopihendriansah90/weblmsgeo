<?php

namespace App\Filament\Resources\Modules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')->relationship('course', 'title')->label('Kursus')->required()->searchable()->preload(),
                TextInput::make('title')->label('Judul Bab')->required(),
                TextInput::make('slug')->required()->maxLength(255),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->default(1)->required(),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required()->default('draft'),
            ]);
    }
}
