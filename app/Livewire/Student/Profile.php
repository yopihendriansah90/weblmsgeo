<?php

namespace App\Livewire\Student;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class Profile extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $class_name = '';

    public string $phone = '';

    public string $gender = '';

    public string $birth_date = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $saved = false;

    public function mount(): void
    {
        $student = auth()->user()->student()->with(['user', 'school'])->firstOrFail();

        $this->name = (string) $student->user->name;
        $this->username = (string) $student->user->username;
        $this->email = (string) ($student->user->email ?? '');
        $this->class_name = (string) ($student->class_name ?? '');
        $this->phone = (string) ($student->phone ?? '');
        $this->gender = (string) ($student->gender ?? '');
        $this->birth_date = (string) optional($student->birth_date)->format('Y-m-d');
    }

    public function save(): void
    {
        $student = auth()->user()->student()->with('user')->firstOrFail();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:5', 'max:30', 'regex:/^[a-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($student->user_id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->user_id)],
            'class_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'birth_date' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:5', 'confirmed'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, titik, underscore, dan strip.',
        ]);

        $student->user->update([
            'name' => trim($validated['name']),
            'username' => strtolower(trim($validated['username'])),
            'email' => filled($validated['email']) ? trim($validated['email']) : null,
            ...($validated['password'] ? ['password' => $validated['password']] : []),
        ]);

        $student->update([
            'class_name' => filled($validated['class_name']) ? trim($validated['class_name']) : null,
            'phone' => filled($validated['phone']) ? trim($validated['phone']) : null,
            'gender' => $validated['gender'] ?: null,
            'birth_date' => $validated['birth_date'] ?: null,
        ]);

        $this->password = '';
        $this->password_confirmation = '';
        $this->saved = true;

        $this->dispatch('student-notify', type: 'success', message: 'Perubahan profil berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.student.profile', [
            'student' => auth()->user()->student()->with(['user', 'school'])->firstOrFail(),
        ]);
    }
}
