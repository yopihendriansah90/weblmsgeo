<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Modules\ModuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus')
                ->successRedirectUrl(fn (): string => ModuleResource::getUrl('edit', ['record' => $this->getRecord()->module_id])),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ModuleResource::getUrl('edit', ['record' => $this->getRecord()->module_id]);
    }
}
