<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load('user');

        return [
            ...$data,
            'name' => $this->record->user->name,
            'username' => $this->record->user->username,
            'email' => $this->record->user->email,
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $userData = [
            'name' => $data['name'],
            'username' => Str::lower($data['username']),
            'email' => $data['email'] ?? null,
            'status' => $data['status'],
        ];

        if (filled($data['password'] ?? null)) {
            $userData['password'] = $data['password'];
        }

        $record->user->update($userData);

        $record->update(Arr::only($data, ['school_id', 'nisn', 'class_name', 'gender', 'birth_date', 'phone', 'status']));

        return $record;
    }
}
