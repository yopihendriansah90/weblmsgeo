<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('module_id')->relationship('module', 'title')->label('Bab')->required()->searchable()->preload(),
                TextInput::make('title')->label('Judul Subbab')->required(),
                TextInput::make('slug')->required(),
                Textarea::make('summary')->label('Ringkasan')->columnSpanFull(),
                RichEditor::make('content')
                    ->label('Isi Materi')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('lesson-content')
                    ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->fileAttachmentsMaxSize(5120)
                    ->columnSpanFull(),
                TextInput::make('estimated_duration')->label('Estimasi Menit')->numeric(),
                TextInput::make('sort_order')->numeric()->default(1)->required(),
                Toggle::make('is_required')->label('Wajib')->default(true),
                Select::make('status')->options(['draft' => 'Draf', 'published' => 'Dipublikasikan', 'archived' => 'Diarsipkan'])->required()->default('draft'),
                DateTimePicker::make('published_at')->label('Waktu Publikasi'),
            ]);
    }
}
