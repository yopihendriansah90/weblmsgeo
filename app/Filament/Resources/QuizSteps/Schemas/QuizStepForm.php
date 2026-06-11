<?php

namespace App\Filament\Resources\QuizSteps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuizStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quiz_id')->relationship('quiz', 'title')->label('Kuis')->required()->searchable()->preload(),
                TextInput::make('title')->label('Judul Step')->required(),
                Select::make('type')->options([
                    'essay' => 'Essay',
                    'text_matching' => 'Penjodohan Teks',
                    'table_checklist' => 'Table Checklist',
                    'image_text_matching' => 'Penjodohan Gambar-Teks',
                ])->required(),
                Textarea::make('instruction')->label('Instruksi')->columnSpanFull(),
                Textarea::make('content_payload')
                    ->label('Payload Soal JSON')
                    ->rows(14)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? json_decode($state, true) : null)
                    ->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->default(1)->required(),
                Select::make('answer_mode')->options(['single_answer_per_row' => 'Single answer per row', 'multiple_answer_per_row' => 'Multiple answer per row']),
                Toggle::make('is_required')->default(true),
                Toggle::make('show_result_after_submit')->default(true),
                Toggle::make('allow_next_after_submit')->default(true),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->required()->default('draft'),
            ]);
    }
}
