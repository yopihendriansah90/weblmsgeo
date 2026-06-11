<?php

namespace App\Filament\Resources\EssayReviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EssayReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('score')->label('Nilai')->numeric()->minValue(0)->maxValue(100)->required(),
                Textarea::make('feedback')->label('Umpan Balik')->columnSpanFull(),
                Select::make('status')->label('Status')->options(['pending_review' => 'Menunggu Penilaian', 'reviewed' => 'Sudah Dinilai'])->required(),
            ]);
    }
}
