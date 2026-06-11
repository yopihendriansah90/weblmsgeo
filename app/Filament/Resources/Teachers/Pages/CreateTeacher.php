<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Teacher;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => Str::lower($data['username']),
                'email' => $data['email'] ?? null,
                'password' => $data['password'],
                'status' => $data['status'],
            ]);
            $user->assignRole('guru');

            return Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => $data['teacher_code'] ?? null,
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'],
            ]);
        });
    }
}
