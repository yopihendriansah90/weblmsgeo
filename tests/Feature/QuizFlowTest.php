<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Services\EssayReviewService;
use App\Services\QuizFlowService;
use App\Services\QuizScoringService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QuizFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_quiz_flow_grades_auto_steps_and_waits_for_essay_review(): void
    {
        Role::findOrCreate('siswa');
        Role::findOrCreate('guru');
        $student = $this->student();
        $teacher = User::create(['name' => 'Reviewer', 'username' => 'reviewer_test', 'email' => 'reviewer@example.test', 'password' => 'password', 'status' => 'active']);
        $teacher->assignRole('guru');
        $quiz = $this->quiz();
        $flow = app(QuizFlowService::class);

        $attempt = $flow->startOrContinue($quiz, $student);
        $essay = $quiz->steps->firstWhere('type', 'essay');
        $matching = $quiz->steps->firstWhere('type', 'text_matching');

        $flow->submitStep($attempt, $essay, ['essay' => 'SIG membantu analisis lokasi dan pemetaan bencana.']);
        $flow->submitStep($attempt->fresh('quiz.steps', 'stepAttempts'), $matching, [
            'answers' => [
                ['item_key' => 'a', 'selected_option_key' => 'one'],
                ['item_key' => 'b', 'selected_option_key' => 'two'],
            ],
        ]);

        $this->assertSame('pending_review', $attempt->fresh()->status);
        $this->assertEquals(100.00, (float) $attempt->fresh()->auto_score);
        $this->assertNull($attempt->fresh()->final_score);

        $essayAttempt = $attempt->fresh('stepAttempts.quizStep')->stepAttempts->firstWhere('quizStep.type', 'essay');
        app(EssayReviewService::class)->review($essayAttempt, $teacher, 80, 'Baik.');

        $this->assertSame('completed', $attempt->fresh()->status);
        $this->assertEquals(90.00, (float) $attempt->fresh()->final_score);
    }

    public function test_table_checklist_rejects_multiple_answers_per_row(): void
    {
        $step = new QuizStep([
            'type' => 'table_checklist',
            'content_payload' => [
                'rows' => [['id' => 'r1', 'label' => 'Row 1']],
                'correct_cells' => [['row_id' => 'r1', 'column_id' => 'c1']],
            ],
        ]);

        $this->expectException(InvalidArgumentException::class);

        app(QuizScoringService::class)->grade($step, [
            'answers' => [
                ['row_id' => 'r1', 'selected_column_id' => 'c1'],
                ['row_id' => 'r1', 'selected_column_id' => 'c2'],
            ],
        ]);
    }

    private function student(): Student
    {
        $school = School::create(['name' => 'Quiz School', 'level' => 'SMP', 'status' => 'active']);
        $user = User::create(['name' => 'Quiz Student', 'username' => 'quiz_student', 'password' => 'password', 'status' => 'active']);
        $user->assignRole('siswa');

        return Student::create(['user_id' => $user->id, 'school_id' => $school->id, 'status' => 'active']);
    }

    private function quiz(): Quiz
    {
        $course = Course::create(['title' => 'SIG Test', 'slug' => 'sig-test', 'status' => 'published']);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Bab Test', 'slug' => 'bab-test', 'status' => 'published']);
        $lesson = Lesson::create(['module_id' => $module->id, 'title' => 'Materi Test', 'slug' => 'materi-test', 'status' => 'published']);
        $quiz = Quiz::create(['lesson_id' => $lesson->id, 'title' => 'Quiz Test', 'status' => 'published']);

        QuizStep::create([
            'quiz_id' => $quiz->id,
            'title' => 'Essay',
            'type' => 'essay',
            'sort_order' => 1,
            'status' => 'published',
            'content_payload' => ['question' => 'Apa manfaat SIG?'],
        ]);
        QuizStep::create([
            'quiz_id' => $quiz->id,
            'title' => 'Matching',
            'type' => 'text_matching',
            'sort_order' => 2,
            'status' => 'published',
            'content_payload' => [
                'pairs' => [
                    ['item_key' => 'a', 'correct_option_key' => 'one'],
                    ['item_key' => 'b', 'correct_option_key' => 'two'],
                ],
            ],
        ]);

        return $quiz->fresh('steps');
    }
}
