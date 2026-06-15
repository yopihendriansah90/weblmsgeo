<?php

namespace Tests\Feature;

use App\Livewire\Student\Profile;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_can_update_profile(): void
    {
        Role::findOrCreate('siswa');

        $school = School::create(['name' => 'Sekolah Profil', 'level' => 'SMP', 'status' => 'active']);
        $user = User::create([
            'name' => 'Siswa Lama',
            'username' => 'siswa_lama',
            'email' => null,
            'password' => 'password',
            'status' => 'active',
        ]);
        $user->assignRole('siswa');

        $student = Student::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'class_name' => 'VIII A',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('name', 'Siswa Baru')
            ->set('username', 'siswa_baru')
            ->set('email', 'siswa@example.test')
            ->set('class_name', 'IX B')
            ->set('phone', '08123456789')
            ->set('gender', 'P')
            ->set('birth_date', '2010-01-10')
            ->call('save')
            ->assertSet('saved', true)
            ->assertDispatched('student-notify', type: 'success', message: 'Perubahan profil berhasil disimpan.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Siswa Baru',
            'username' => 'siswa_baru',
            'email' => 'siswa@example.test',
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'class_name' => 'IX B',
            'phone' => '08123456789',
            'gender' => 'P',
        ]);
    }
}
