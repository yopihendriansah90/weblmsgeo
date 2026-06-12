<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Modules\ModuleResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';

    protected static ?string $title = 'Daftar Bab';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Bab')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draf',
                        'published' => 'Dipublikasikan',
                        'archived' => 'Diarsipkan',
                    ])
                    ->default('draft')
                    ->required(),
            ]);
    }

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
                    ->label('Bab')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quizzes_count')
                    ->counts('quizzes')
                    ->label('Kuis'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Bab'),
            ])
            ->recordActions([
                Action::make('manageLessons')
                    ->label('Kelola Bab')
                    ->icon('heroicon-o-book-open')
                    ->url(fn ($record): string => ModuleResource::getUrl('edit', ['record' => $record])),
                EditAction::make()
                    ->label('Ubah Bab'),
                DeleteAction::make()
                    ->label('Hapus'),
            ]);
    }
}
