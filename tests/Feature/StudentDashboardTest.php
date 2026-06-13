<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_summary_filters_quizzes_and_uses_latest_completed_score(): void
    {
        $student = $this->student();

        $publishedQuiz = $this->publishedQuiz('Quiz Terbit', 'sig-published');
        $this->draftQuiz('Quiz Tidak Terbit', 'sig-draft');

        QuizAttempt::create([
            'quiz_id' => $publishedQuiz->id,
            'student_id' => $student->id,
            'started_at' => now()->subDay(),
            'completed_at' => now()->subDay(),
            'auto_score' => 100,
            'essay_score' => 80,
            'final_score' => 90,
            'status' => 'completed',
        ]);

        QuizAttempt::create([
            'quiz_id' => $publishedQuiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'status' => 'pending_review',
        ]);

        $summary = app(StudentDashboardService::class)->summary($student);
        $quizTitles = $summary['available_quizzes']->pluck('title');

        $this->assertSame(90.0, (float) $summary['latest_score']);
        $this->assertTrue($quizTitles->contains('Quiz Terbit'));
        $this->assertFalse($quizTitles->contains('Quiz Tidak Terbit'));
    }

    private function student(): Student
    {
        $school = School::create(['name' => 'Student School', 'level' => 'SMP', 'status' => 'active']);
        $user = User::create(['name' => 'Student One', 'username' => 'student_one', 'password' => 'password', 'status' => 'active']);

        return Student::create(['user_id' => $user->id, 'school_id' => $school->id, 'status' => 'active']);
    }

    private function publishedQuiz(string $title, string $slug): Quiz
    {
        $course = Course::create([
            'title' => $title.' Course',
            'slug' => $slug.'-course',
            'status' => 'published',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => $title.' Module',
            'slug' => $slug.'-module',
            'status' => 'published',
        ]);

        return Quiz::create([
            'module_id' => $module->id,
            'title' => $title,
            'status' => 'published',
        ]);
    }

    private function draftQuiz(string $title, string $slug): Quiz
    {
        $course = Course::create([
            'title' => $title.' Course',
            'slug' => $slug.'-course',
            'status' => 'draft',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => $title.' Module',
            'slug' => $slug.'-module',
            'status' => 'published',
        ]);

        return Quiz::create([
            'module_id' => $module->id,
            'title' => $title,
            'status' => 'published',
        ]);
    }
}
