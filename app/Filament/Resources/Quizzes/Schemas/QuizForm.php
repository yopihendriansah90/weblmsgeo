<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('module_id')->relationship('module', 'title')->label('Bab')->required()->searchable()->preload(),
                TextInput::make('title')->label('Judul Kuis')->required(),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
                Select::make('mode')->label('Mode')->options(['practice' => 'Latihan', 'final' => 'Final'])->required()->default('practice'),
                Toggle::make('allow_retake')->label('Boleh Ulang')->default(false),
                TextInput::make('max_attempts')->label('Maksimal Percobaan')->numeric(),
                Select::make('status')->options(['draft' => 'Draf', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan'])->required()->default('draft'),
            ]);
    }
}
