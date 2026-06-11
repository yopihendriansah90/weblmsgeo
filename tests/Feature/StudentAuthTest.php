<?php

namespace Tests\Feature;

use App\Livewire\Student\Login;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_can_login_with_username(): void
    {
        Role::findOrCreate('siswa');
        $school = School::create(['name' => 'Sekolah Test', 'level' => 'SMP', 'status' => 'active']);
        $user = User::create([
            'name' => 'Siswa Test',
            'username' => 'siswa_test',
            'email' => null,
            'password' => 'password',
            'status' => 'active',
        ]);
        $user->assignRole('siswa');
        Student::create(['user_id' => $user->id, 'school_id' => $school->id, 'status' => 'active']);

        Livewire::test(Login::class)
            ->set('username', 'siswa_test')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_inactive_student_cannot_login(): void
    {
        Role::findOrCreate('siswa');
        $school = School::create(['name' => 'Sekolah Nonaktif', 'level' => 'SMP', 'status' => 'active']);
        $user = User::create([
            'name' => 'Siswa Nonaktif',
            'username' => 'siswa_inactive',
            'email' => null,
            'password' => 'password',
            'status' => 'active',
        ]);
        $user->assignRole('siswa');
        Student::create(['user_id' => $user->id, 'school_id' => $school->id, 'status' => 'inactive']);

        Livewire::test(Login::class)
            ->set('username', 'siswa_inactive')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['username']);
    }
}
