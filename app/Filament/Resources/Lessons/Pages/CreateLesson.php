<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Modules\ModuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;

    protected static bool $canCreateAnother = false;

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill([
            'module_id' => request()->integer('module_id') ?: null,
        ]);

        $this->callHook('afterFill');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ModuleResource::getUrl('edit', ['record' => $this->getRecord()->module_id]);
    }
}
