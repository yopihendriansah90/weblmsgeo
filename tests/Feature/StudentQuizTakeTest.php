<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentQuizTakeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_quiz_page_renders_without_parse_error(): void
    {
        Role::findOrCreate('siswa');
        $student = $this->student();
        $quiz = $this->quiz();

        $this->actingAs($student->user);

        $this->get(route('student.quizzes.take', $quiz))
            ->assertOk()
            ->assertSee('Kirim Jawaban');
    }

    private function student(): Student
    {
        $school = School::create(['name' => 'Quiz School', 'level' => 'SMP', 'status' => 'active']);
        $user = User::create(['name' => 'Quiz Student', 'username' => 'quiz_student_page', 'password' => 'password', 'status' => 'active']);
        $user->assignRole('siswa');

        return Student::create(['user_id' => $user->id, 'school_id' => $school->id, 'status' => 'active']);
    }

    private function quiz(): Quiz
    {
        $course = Course::create([
            'title' => 'Quiz Page Course',
            'slug' => 'quiz-page-course',
            'status' => 'published',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Quiz Page Module',
            'slug' => 'quiz-page-module',
            'status' => 'published',
        ]);

        $quiz = Quiz::create([
            'module_id' => $module->id,
            'title' => 'Quiz Page',
            'status' => 'published',
        ]);

        QuizStep::create([
            'quiz_id' => $quiz->id,
            'title' => 'Essay',
            'type' => 'essay',
            'sort_order' => 1,
            'status' => 'published',
            'content_payload' => ['question' => 'Apa itu SIG?'],
        ]);

        return $quiz->fresh('steps');
    }
}
