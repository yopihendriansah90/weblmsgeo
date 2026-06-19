<?php

namespace Tests\Feature;

use App\Models\Course;
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
use Illuminate\Support\Str;
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

    public function test_completed_quiz_cannot_be_restarted_when_retake_is_disabled(): void
    {
        Role::findOrCreate('siswa');
        $student = $this->student();
        $quiz = $this->autoQuiz(['allow_retake' => false, 'max_attempts' => 1]);
        $flow = app(QuizFlowService::class);

        $firstAttempt = $flow->startOrContinue($quiz, $student);
        $flow->submitStep($firstAttempt, $quiz->steps->first(), [
            'answers' => [
                ['item_key' => 'a', 'selected_option_key' => 'one'],
            ],
        ]);
        $this->assertSame('completed', $firstAttempt->fresh()->status);

        $secondAttempt = $flow->startOrContinue($quiz->fresh('steps'), $student);

        $this->assertSame($firstAttempt->id, $secondAttempt->id);
        $this->assertSame('completed', $secondAttempt->status);
        $this->assertSame(1, $quiz->attempts()->where('student_id', $student->id)->count());
    }

    public function test_retake_respects_max_attempts(): void
    {
        Role::findOrCreate('siswa');
        $student = $this->student();
        $quiz = $this->autoQuiz(['allow_retake' => true, 'max_attempts' => 2]);
        $flow = app(QuizFlowService::class);

        $firstAttempt = $flow->startOrContinue($quiz, $student);
        $flow->submitStep($firstAttempt, $quiz->steps->first(), [
            'answers' => [
                ['item_key' => 'a', 'selected_option_key' => 'one'],
            ],
        ]);
        $this->assertSame('completed', $firstAttempt->fresh()->status);

        $secondAttempt = $flow->startOrContinue($quiz->fresh('steps'), $student);
        $flow->submitStep($secondAttempt, $quiz->fresh('steps')->steps->first(), [
            'answers' => [
                ['item_key' => 'a', 'selected_option_key' => 'one'],
            ],
        ]);
        $this->assertSame('completed', $secondAttempt->fresh()->status);

        $this->assertSame(2, $quiz->fresh()->max_attempts);
        $this->assertSame(2, $quiz->attempts()->where('student_id', $student->id)->count());

        $thirdAttempt = $flow->startOrContinue($quiz->fresh('steps'), $student);

        $this->assertNotSame($firstAttempt->id, $secondAttempt->id);
        $this->assertSame($secondAttempt->id, $thirdAttempt->id);
        $this->assertSame(2, $quiz->attempts()->where('student_id', $student->id)->count());
    }

    private function student(): Student
    {
        $suffix = Str::random(8);
        $school = School::create(['name' => 'Quiz School '.$suffix, 'level' => 'SMP', 'status' => 'active']);
        $user = User::create(['name' => 'Quiz Student', 'username' => 'quiz_student_'.$suffix, 'password' => 'password', 'status' => 'active']);
        $user->assignRole('siswa');

        return Student::create(['user_id' => $user->id, 'school_id' => $school->id, 'status' => 'active']);
    }

    private function quiz(): Quiz
    {
        $course = Course::create(['title' => 'SIG Test', 'slug' => 'sig-test', 'status' => 'published']);
        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Quiz Materi Test',
            'slug' => 'quiz-materi-test',
            'status' => 'published',
        ]);
        $quiz = Quiz::create(['module_id' => $module->id, 'title' => 'Quiz Test', 'status' => 'published']);

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

    private function autoQuiz(array $attributes = []): Quiz
    {
        $suffix = Str::random(8);
        $course = Course::create(['title' => 'Auto Quiz Course', 'slug' => 'auto-quiz-course-'.$suffix, 'status' => 'published']);
        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Auto Quiz Module',
            'slug' => 'auto-quiz-module-'.$suffix,
            'status' => 'published',
        ]);
        $quiz = Quiz::create([
            'module_id' => $module->id,
            'title' => 'Auto Quiz',
            'status' => 'published',
            ...$attributes,
        ]);

        QuizStep::create([
            'quiz_id' => $quiz->id,
            'title' => 'Matching',
            'type' => 'text_matching',
            'sort_order' => 1,
            'status' => 'published',
            'content_payload' => [
                'items' => [
                    ['key' => 'a', 'label' => 'Data spasial'],
                ],
                'options' => [
                    ['key' => 'one', 'label' => 'Data lokasi'],
                ],
                'pairs' => [
                    ['item_key' => 'a', 'correct_option_key' => 'one'],
                ],
            ],
        ]);

        return $quiz->fresh('steps');
    }
}
