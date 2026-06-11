<?php

namespace App\Filament\Resources\SuperAdmins\Pages;

use App\Filament\Resources\SuperAdmins\SuperAdminResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditSuperAdmin extends EditRecord
{
    protected static string $resource = SuperAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => $this->record->getKey() !== auth()->id()),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $payload = [
            'name' => $data['name'],
            'username' => Str::lower($data['username']),
            'email' => $data['email'] ?? null,
            'status' => $data['status'],
        ];

        if (filled($data['password'] ?? null)) {
            $payload['password'] = $data['password'];
        }

        $record->update($payload);
        $record->syncRoles(['super_admin']);

        return $record;
    }
}
