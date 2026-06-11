<?php

namespace App\Filament\Resources\Modules\RelationManagers;

use App\Filament\Resources\Lessons\LessonResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $title = 'Daftar Subbab';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Subbab')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('published_at')
                    ->label('Publikasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
            ])
            ->headerActions([
                Action::make('createLesson')
                    ->label('Tambah Subbab')
                    ->icon('heroicon-o-plus')
                    ->url(fn (): string => LessonResource::getUrl('create', ['module_id' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                Action::make('editLesson')
                    ->label('Ubah Materi')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record): string => LessonResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->label('Hapus'),
            ]);
    }
}
