<?php

namespace Tests\Feature;

use App\Livewire\Guru\QuizReportIndex;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSchoolAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruQuizReportIndexTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guru_only_sees_quiz_reports_from_assigned_schools(): void
    {
        Role::findOrCreate('guru');
        Role::findOrCreate('siswa');

        $assignedSchool = School::create(['name' => 'Sekolah Laporan A', 'level' => 'SMP', 'status' => 'active']);
        $otherSchool = School::create(['name' => 'Sekolah Laporan B', 'level' => 'SMP', 'status' => 'active']);

        $teacherUser = User::create([
            'name' => 'Guru Laporan',
            'username' => 'guru_laporan_hasil',
            'email' => 'guru-laporan@example.test',
            'password' => 'password',
            'status' => 'active',
        ]);
        $teacherUser->assignRole('guru');

        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'status' => 'active',
        ]);

        TeacherSchoolAssignment::create([
            'teacher_id' => $teacher->id,
            'school_id' => $assignedSchool->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        $visibleStudent = $this->student($assignedSchool, 'siswa_report_visible', 'Siswa Report Visible');
        $hiddenStudent = $this->student($otherSchool, 'siswa_report_hidden', 'Siswa Report Hidden');

        $visibleQuiz = $this->quiz('visible-report-quiz');
        $hiddenQuiz = $this->quiz('hidden-report-quiz');

        QuizAttempt::create([
            'quiz_id' => $visibleQuiz->id,
            'student_id' => $visibleStudent->id,
            'started_at' => now()->subHour(),
            'completed_at' => now()->subMinutes(40),
            'auto_score' => 90,
            'essay_score' => 80,
            'final_score' => 85,
            'status' => 'completed',
        ]);

        QuizAttempt::create([
            'quiz_id' => $hiddenQuiz->id,
            'student_id' => $hiddenStudent->id,
            'started_at' => now()->subHour(),
            'completed_at' => now()->subMinutes(30),
            'auto_score' => 70,
            'essay_score' => 70,
            'final_score' => 70,
            'status' => 'completed',
        ]);

        $this->actingAs($teacherUser);

        Livewire::test(QuizReportIndex::class)
            ->assertSee($visibleStudent->user->name)
            ->assertSee($visibleQuiz->title)
            ->assertDontSee($hiddenStudent->user->name)
            ->assertDontSee($hiddenQuiz->title);
    }

    private function student(School $school, string $username, string $name): Student
    {
        $user = User::create([
            'name' => $name,
            'username' => $username,
            'password' => 'password',
            'status' => 'active',
        ]);
        $user->assignRole('siswa');

        return Student::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'class_name' => 'IX A',
            'status' => 'active',
        ]);
    }

    private function quiz(string $slug): Quiz
    {
        $course = Course::create([
            'title' => 'Course '.$slug,
            'slug' => 'course-'.$slug,
            'status' => 'published',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Module '.$slug,
            'slug' => 'module-'.$slug,
            'status' => 'published',
        ]);

        return Quiz::create([
            'module_id' => $module->id,
            'title' => 'Quiz '.$slug,
            'status' => 'published',
        ]);
    }
}
