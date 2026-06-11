<?php

namespace App\Filament\Resources\SuperAdmins\Pages;

use App\Filament\Resources\SuperAdmins\SuperAdminResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateSuperAdmin extends CreateRecord
{
    protected static string $resource = SuperAdminResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'username' => Str::lower($data['username']),
            'email' => $data['email'] ?? null,
            'password' => $data['password'],
            'status' => $data['status'],
        ]);

        $user->syncRoles(['super_admin']);

        return $user;
    }
}
