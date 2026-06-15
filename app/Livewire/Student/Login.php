<?php

namespace App\Livewire\Student;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $this->username = Str::lower(trim($this->username));

        $credentials = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->addError('username', 'Username atau password salah.');
            return null;
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            $this->addError('username', 'Akun Anda tidak aktif.');
            return null;
        }

        $user->forceFill(['last_login_at' => now()])->save();
        session()->regenerate();

        // Redirect based on role
        if ($user->hasRole('super_admin')) {
            return redirect()->intended('/admin');
        }

        if ($user->hasRole('guru')) {
            return redirect()->intended('/guru');
        }

        if ($user->hasRole('siswa')) {
            if ($user->student?->status !== 'active') {
                Auth::logout();
                $this->addError('username', 'Akun siswa tidak aktif.');
                return null;
            }
            return redirect()->intended('/siswa');
        }

        // Default redirect
        return redirect()->intended('/');
    }

    public function render()
    {
        return view('livewire.student.login', [
            'title' => 'Login LMS SIG',
            'subtitle' => 'Masuk menggunakan username dan password yang terdaftar.',
        ]);
    }
}
