<?php

namespace App\Filament\Resources\QuizSteps\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;

class QuizStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('quiz_id')->relationship('quiz', 'title')->label('Kuis')->required()->searchable()->preload(),
                TextInput::make('title')->label('Judul Step')->required(),
                Select::make('type')
                    ->options([
                        'essay' => 'Esai',
                        'text_matching' => 'Penjodohan Teks',
                        'table_checklist' => 'Tabel Checklist',
                        'image_text_matching' => 'Penjodohan Gambar-Teks',
                    ])
                    ->required()
                    ->live(),
                Textarea::make('instruction')->label('Instruksi')->columnSpanFull(),
                Hidden::make('content_payload')
                    ->afterStateHydrated(function (?array $state, Set $set): void {
                        static::fillStateFromPayload($state ?? [], $set);
                    })
                    ->dehydrateStateUsing(fn (Get $get): array => static::buildPayload($get)),
                Section::make('Konfigurasi Esai')
                    ->visible(fn (Get $get): bool => $get('type') === 'essay')
                    ->schema([
                        Textarea::make('essay_question')
                            ->label('Pertanyaan Esai')
                            ->rows(4)
                            ->required(fn (Get $get): bool => $get('type') === 'essay')
                            ->dehydrated(false),
                    ]),
                Section::make('Konfigurasi Penjodohan Teks')
                    ->visible(fn (Get $get): bool => $get('type') === 'text_matching')
                    ->schema([
                        Repeater::make('matching_options')
                            ->label('Pilihan Jawaban')
                            ->schema([
                                TextInput::make('key')->label('Kunci Opsi')->required(),
                                TextInput::make('label')->label('Label Opsi')->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->dehydrated(false),
                        Repeater::make('matching_items')
                            ->label('Item Soal')
                            ->schema([
                                TextInput::make('key')->label('Kunci Item')->required(),
                                TextInput::make('label')->label('Label Item')->required(),
                                TextInput::make('correct_option_key')->label('Kunci Jawaban Benar')->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->dehydrated(false),
                    ]),
                Section::make('Konfigurasi Tabel Checklist')
                    ->visible(fn (Get $get): bool => $get('type') === 'table_checklist')
                    ->schema([
                        Repeater::make('table_columns')
                            ->label('Kolom Checklist')
                            ->schema([
                                TextInput::make('id')->label('ID Kolom')->required(),
                                TextInput::make('label')->label('Label Kolom')->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->dehydrated(false),
                        Repeater::make('table_rows')
                            ->label('Baris Soal')
                            ->schema([
                                TextInput::make('id')->label('ID Baris')->required(),
                                TextInput::make('label')->label('Label Baris')->required(),
                                TextInput::make('correct_column_id')->label('ID Kolom Benar')->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->dehydrated(false),
                    ]),
                Section::make('Konfigurasi Penjodohan Gambar-Teks')
                    ->visible(fn (Get $get): bool => $get('type') === 'image_text_matching')
                    ->schema([
                        Repeater::make('image_options')
                            ->label('Pilihan Teks')
                            ->schema([
                                TextInput::make('key')->label('Kunci Opsi')->required(),
                                TextInput::make('label')->label('Label Opsi')->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->dehydrated(false),
                        Repeater::make('image_items')
                            ->label('Item Gambar')
                            ->schema([
                                TextInput::make('key')->label('Kunci Item')->required(),
                                TextInput::make('label')->label('Label Item')->required(),
                                TextInput::make('image_url')->label('URL Gambar')->required()->url(),
                                TextInput::make('alt')->label('Teks Alt')->required(),
                                TextInput::make('correct_option_key')->label('Kunci Jawaban Benar')->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->dehydrated(false),
                    ]),
                TextInput::make('sort_order')->numeric()->default(1)->required(),
                Select::make('answer_mode')
                    ->options([
                        'single_answer_per_row' => 'Satu jawaban per baris',
                        'multiple_answer_per_row' => 'Banyak jawaban per baris',
                    ]),
                Toggle::make('is_required')->default(true),
                Toggle::make('show_result_after_submit')->default(true),
                Toggle::make('allow_next_after_submit')->default(true),
                Select::make('status')->options(['draft' => 'Draf', 'published' => 'Dipublikasikan'])->required()->default('draft'),
            ]);
    }

    private static function fillStateFromPayload(array $payload, Set $set): void
    {
        $set('essay_question', $payload['question'] ?? null);
        $set('matching_options', $payload['options'] ?? []);
        $set('matching_items', collect($payload['items'] ?? [])
            ->map(function (array $item) use ($payload): array {
                $pair = collect($payload['pairs'] ?? [])->firstWhere('item_key', $item['key'] ?? null);

                return [
                    'key' => $item['key'] ?? null,
                    'label' => $item['label'] ?? null,
                    'correct_option_key' => $pair['correct_option_key'] ?? null,
                ];
            })
            ->all());
        $set('table_columns', $payload['columns'] ?? []);
        $set('table_rows', collect($payload['rows'] ?? [])
            ->map(function (array $row) use ($payload): array {
                $correctCell = collect($payload['correct_cells'] ?? [])->firstWhere('row_id', $row['id'] ?? null);

                return [
                    'id' => $row['id'] ?? null,
                    'label' => $row['label'] ?? null,
                    'correct_column_id' => $correctCell['column_id'] ?? null,
                ];
            })
            ->all());
        $set('image_options', $payload['options'] ?? []);
        $set('image_items', collect($payload['items'] ?? [])
            ->map(function (array $item) use ($payload): array {
                $pair = collect($payload['pairs'] ?? [])->firstWhere('item_key', $item['key'] ?? null);

                return [
                    'key' => $item['key'] ?? null,
                    'label' => $item['label'] ?? null,
                    'image_url' => $item['image_url'] ?? null,
                    'alt' => $item['alt'] ?? null,
                    'correct_option_key' => $pair['correct_option_key'] ?? null,
                ];
            })
            ->all());
    }

    private static function buildPayload(Get $get): array
    {
        return match ($get('type')) {
            'essay' => [
                'question' => $get('essay_question'),
            ],
            'text_matching' => [
                'options' => static::normalizeList($get('matching_options')),
                'items' => collect(static::normalizeList($get('matching_items')))
                    ->map(fn (array $item): array => [
                        'key' => $item['key'],
                        'label' => $item['label'],
                    ])
                    ->all(),
                'pairs' => collect(static::normalizeList($get('matching_items')))
                    ->map(fn (array $item): array => [
                        'item_key' => $item['key'],
                        'correct_option_key' => $item['correct_option_key'],
                    ])
                    ->all(),
            ],
            'table_checklist' => [
                'columns' => static::normalizeList($get('table_columns')),
                'rows' => collect(static::normalizeList($get('table_rows')))
                    ->map(fn (array $row): array => [
                        'id' => $row['id'],
                        'label' => $row['label'],
                    ])
                    ->all(),
                'correct_cells' => collect(static::normalizeList($get('table_rows')))
                    ->map(fn (array $row): array => [
                        'row_id' => $row['id'],
                        'column_id' => $row['correct_column_id'],
                    ])
                    ->all(),
            ],
            'image_text_matching' => [
                'options' => static::normalizeList($get('image_options')),
                'items' => collect(static::normalizeList($get('image_items')))
                    ->map(fn (array $item): array => [
                        'key' => $item['key'],
                        'label' => $item['label'],
                        'image_url' => $item['image_url'],
                        'alt' => $item['alt'],
                    ])
                    ->all(),
                'pairs' => collect(static::normalizeList($get('image_items')))
                    ->map(fn (array $item): array => [
                        'item_key' => $item['key'],
                        'correct_option_key' => $item['correct_option_key'],
                    ])
                    ->all(),
            ],
            default => [],
        };
    }

    private static function normalizeList(mixed $items): array
    {
        return collect($items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item) && collect($item)->filter(fn (mixed $value) => filled($value))->isNotEmpty())
            ->values()
            ->all();
    }
}
