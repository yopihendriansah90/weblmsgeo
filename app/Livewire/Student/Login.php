<?php

namespace App\Livewire\Student;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $username = '';

    public string $password = '';

    public function login()
    {
        $this->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'username' => Str::lower(trim($this->username)),
            'password' => $this->password,
        ];

        if (! Auth::attempt($credentials)) {
            $this->addError('username', 'Username atau password salah.');

            return null;
        }

        $user = Auth::user()->load('student');

        if ($user->status !== 'active' || ! $user->hasRole('siswa') || $user->student?->status !== 'active') {
            Auth::logout();
            $this->addError('username', 'Akun siswa tidak aktif atau tidak memiliki akses portal.');

            return null;
        }

        $user->forceFill(['last_login_at' => now()])->save();
        session()->regenerate();

        return $this->redirectRoute('student.dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.student.login');
    }
}
