<?php

namespace Tests\Feature;

use App\Filament\Resources\Students\StudentResource;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherScopeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_teacher_student_resource_is_scoped_to_assigned_schools(): void
    {
        Role::findOrCreate('guru');
        Role::findOrCreate('siswa');

        $assignedSchool = School::create(['name' => 'Assigned School', 'level' => 'SMP', 'status' => 'active']);
        $otherSchool = School::create(['name' => 'Other School', 'level' => 'SMP', 'status' => 'active']);

        $teacherUser = User::create(['name' => 'Guru Scope', 'username' => 'guru_scope', 'email' => 'guru.scope@example.test', 'password' => 'password', 'status' => 'active']);
        $teacherUser->assignRole('guru');
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'status' => 'active']);
        $teacher->assignments()->create(['school_id' => $assignedSchool->id, 'status' => 'active', 'assigned_at' => now()]);

        $visibleUser = User::create(['name' => 'Visible Student', 'username' => 'visible_student', 'password' => 'password', 'status' => 'active']);
        $hiddenUser = User::create(['name' => 'Hidden Student', 'username' => 'hidden_student', 'password' => 'password', 'status' => 'active']);
        Student::create(['user_id' => $visibleUser->id, 'school_id' => $assignedSchool->id, 'status' => 'active']);
        Student::create(['user_id' => $hiddenUser->id, 'school_id' => $otherSchool->id, 'status' => 'active']);

        $this->actingAs($teacherUser);

        $names = StudentResource::getEloquentQuery()->with('user')->get()->pluck('user.name');

        $this->assertTrue($names->contains('Visible Student'));
        $this->assertFalse($names->contains('Hidden Student'));
    }
}
