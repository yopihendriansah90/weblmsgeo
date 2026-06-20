<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
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

    public function test_learning_progress_only_counts_published_lesson_modules(): void
    {
        $student = $this->student();
        $lessonTotalBefore = Module::where('status', 'published')
            ->where('type', 'lesson')
            ->count();

        $course = Course::create([
            'title' => 'Course Progress',
            'slug' => 'course-progress',
            'status' => 'published',
        ]);

        $completedLesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Selesai',
            'slug' => 'bab-selesai',
            'status' => 'published',
        ]);

        Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Belum Selesai',
            'slug' => 'bab-belum-selesai',
            'status' => 'published',
        ]);

        Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Quiz Materi',
            'slug' => 'quiz-materi-progress',
            'status' => 'published',
        ]);

        LessonProgress::create([
            'student_id' => $student->id,
            'module_id' => $completedLesson->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $summary = app(StudentDashboardService::class)->summary($student);
        $expectedProgress = (int) round((1 / ($lessonTotalBefore + 2)) * 100);
        $progressIfQuizWasCounted = (int) round((1 / ($lessonTotalBefore + 3)) * 100);

        $this->assertSame($expectedProgress, $summary['progress_percentage']);
        $this->assertNotSame($progressIfQuizWasCounted, $summary['progress_percentage']);
    }

    public function test_dashboard_hides_course_quiz_until_all_lessons_are_completed(): void
    {
        $student = $this->student();
        $course = Course::create([
            'title' => 'Course Dashboard Lock',
            'slug' => 'course-dashboard-lock',
            'status' => 'published',
        ]);

        $firstLesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Pertama',
            'slug' => 'bab-dashboard-pertama',
            'status' => 'published',
        ]);

        Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Kedua',
            'slug' => 'bab-dashboard-kedua',
            'status' => 'published',
        ]);

        $quizModule = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Quiz Dashboard',
            'slug' => 'quiz-dashboard-lock',
            'status' => 'published',
        ]);

        Quiz::create([
            'module_id' => $quizModule->id,
            'title' => 'Quiz Dashboard Lock',
            'status' => 'published',
        ]);

        LessonProgress::create([
            'student_id' => $student->id,
            'module_id' => $firstLesson->id,
            'status' => 'completed',
            'last_opened_at' => now(),
            'completed_at' => now(),
        ]);

        $summary = app(StudentDashboardService::class)->summary($student);

        $this->assertNull($summary['last_module_quiz']);
        $this->assertFalse($summary['available_quizzes']->pluck('title')->contains('Quiz Dashboard Lock'));
    }

    private function student(): Student
    {
        $school = School::create(['name' => 'Student School', 'level' => 'SMP', 'status' => 'active']);
        $user = User::create([
            'name' => 'Student One',
            'username' => 'student_one_'.Str::lower(Str::random(6)),
            'password' => 'password',
            'status' => 'active',
        ]);

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
