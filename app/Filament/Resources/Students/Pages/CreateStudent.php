<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

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
            $user->assignRole('siswa');

            return Student::create([
                'user_id' => $user->id,
                'school_id' => $data['school_id'],
                'nisn' => $data['nisn'] ?? null,
                'class_name' => $data['class_name'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'],
            ]);
        });
    }
}
