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
                Select::make('lesson_id')->relationship('lesson', 'title')->label('Subbab')->required()->searchable()->preload(),
                TextInput::make('title')->label('Judul Kuis')->required(),
                Textarea::make('description')->label('Deskripsi')->columnSpanFull(),
                Select::make('mode')->options(['practice' => 'Practice', 'final' => 'Final'])->required()->default('practice'),
                Toggle::make('allow_retake')->label('Boleh Retake')->default(false),
                TextInput::make('max_attempts')->label('Maks Attempt')->numeric(),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required()->default('draft'),
            ]);
    }
}
