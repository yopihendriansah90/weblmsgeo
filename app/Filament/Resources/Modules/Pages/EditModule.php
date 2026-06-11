<?php

namespace App\Filament\Resources\Modules\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Modules\ModuleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModule extends EditRecord
{
    protected static string $resource = ModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToCourse')
                ->label('Kembali ke Materi Pembelajaran')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => CourseResource::getUrl('edit', ['record' => $this->getRecord()->course])),
            DeleteAction::make()
                ->label('Hapus')
                ->successRedirectUrl(fn (): string => CourseResource::getUrl('edit', ['record' => $this->getRecord()->course])),
        ];
    }
}
