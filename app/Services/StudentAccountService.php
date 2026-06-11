<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentAccountService
{
    public function create(array $userData, array $studentData, string $password): Student
    {
        $username = $this->normalizeUsername($userData['username'] ?? '');

        return DB::transaction(function () use ($userData, $studentData, $password, $username) {
            $user = User::create([
                'name' => $userData['name'],
                'username' => $username,
                'email' => $userData['email'] ?? null,
                'password' => $password,
                'status' => $userData['status'] ?? 'active',
            ]);
            $user->assignRole('siswa');

            return Student::create([
                ...$studentData,
                'user_id' => $user->id,
                'status' => $studentData['status'] ?? 'active',
            ]);
        });
    }

    public function resetPassword(Student $student, string $password): void
    {
        $student->user->forceFill(['password' => $password])->save();
    }

    public function deactivate(Student $student): void
    {
        DB::transaction(function () use ($student) {
            $student->update(['status' => 'inactive']);
            $student->user->update(['status' => 'inactive']);
        });
    }

    public function normalizeUsername(string $username): string
    {
        $username = Str::lower(trim($username));

        if (! preg_match('/^[a-z0-9._-]{5,30}$/', $username)) {
            throw ValidationException::withMessages([
                'username' => 'Username harus 5-30 karakter dan hanya boleh berisi huruf, angka, titik, underscore, atau strip.',
            ]);
        }

        return $username;
    }
}
