<?php

namespace App\Services;

use App\Models\EssayReview;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizStep;
use App\Models\QuizStepAttempt;
use App\Models\Student;
use App\Models\StudentLearningActivity;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QuizFlowService
{
    public function __construct(private readonly QuizScoringService $scoringService) {}

    public function startOrContinue(Quiz $quiz, Student $student): QuizAttempt
    {
        return DB::transaction(function () use ($quiz, $student) {
            $attempt = QuizAttempt::firstOrCreate(
                ['quiz_id' => $quiz->id, 'student_id' => $student->id, 'status' => 'in_progress'],
                ['started_at' => now()],
            );

            $steps = $quiz->steps()->get();
            foreach ($steps as $index => $step) {
                $stepAttempt = QuizStepAttempt::firstOrCreate(
                    ['quiz_attempt_id' => $attempt->id, 'quiz_step_id' => $step->id],
                    ['status' => $index === 0 ? 'active' : 'locked', 'started_at' => $index === 0 ? now() : null],
                );

                $presentation = $this->buildPresentationPayload($step);
                if ($presentation) {
                    $existingPayload = $stepAttempt->result_payload ?? [];

                    if (! isset($existingPayload['presentation'])) {
                        $stepAttempt->update([
                            'result_payload' => array_merge($existingPayload, [
                                'presentation' => $presentation,
                            ]),
                        ]);
                    }
                }
            }

            if (! $attempt->current_step_id && $steps->isNotEmpty()) {
                $attempt->update(['current_step_id' => $steps->first()->id]);
            }

            StudentLearningActivity::create([
                'student_id' => $student->id,
                'module_id' => $quiz->module_id,
                'quiz_id' => $quiz->id,
                'activity_type' => 'quiz_started',
                'metadata' => ['quiz_title' => $quiz->title],
                'occurred_at' => now(),
            ]);

            return $attempt->fresh(['quiz.steps', 'stepAttempts.quizStep', 'stepAttempts.answers']);
        });
    }

    public function submitStep(QuizAttempt $attempt, QuizStep $step, array $answer): QuizStepAttempt
    {
        return DB::transaction(function () use ($attempt, $step, $answer) {
            $stepAttempt = $attempt->stepAttempts()->where('quiz_step_id', $step->id)->firstOrFail();

            if ($stepAttempt->status === 'locked') {
                throw new InvalidArgumentException('Step kuis masih terkunci.');
            }

            $stepAttempt->answers()->delete();

            if ($step->type === 'essay') {
                $existingPayload = $stepAttempt->result_payload ?? [];
                QuizAttemptAnswer::create([
                    'quiz_step_attempt_id' => $stepAttempt->id,
                    'question_key' => 'essay',
                    'answer_payload' => $answer,
                    'correct_answer_snapshot' => $step->content_payload,
                ]);

                $stepAttempt->update([
                    'status' => 'pending_review',
                    'submitted_at' => now(),
                    'result_payload' => array_merge($existingPayload, ['message' => 'Jawaban esai telah dikirim dan menunggu penilaian guru.']),
                ]);

                EssayReview::firstOrCreate(
                    ['quiz_step_attempt_id' => $stepAttempt->id],
                    ['status' => 'pending_review'],
                );
            } else {
                $graded = $this->scoringService->grade($step, $answer);
                $existingPayload = $stepAttempt->result_payload ?? [];
                QuizAttemptAnswer::create([
                    'quiz_step_attempt_id' => $stepAttempt->id,
                    'question_key' => $step->type,
                    'answer_payload' => $answer,
                    'correct_answer_snapshot' => $step->content_payload,
                    'is_correct' => $graded['is_correct'],
                    'score_obtained' => $graded['score'],
                ]);

                $stepAttempt->update([
                    'status' => 'auto_graded',
                    'submitted_at' => now(),
                    'score' => $graded['score'],
                    'result_payload' => array_merge($existingPayload, $graded['result_payload']),
                ]);
            }

            $this->unlockNextStep($attempt, $step);
            $this->refreshAttemptScore($attempt);

            return $stepAttempt->fresh(['answers', 'essayReview']);
        });
    }

    public function unlockNextStep(QuizAttempt $attempt, QuizStep $step): void
    {
        $next = $attempt->quiz->steps()
            ->where('sort_order', '>', $step->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            $attempt->stepAttempts()
                ->where('quiz_step_id', $next->id)
                ->where('status', 'locked')
                ->update(['status' => 'active', 'started_at' => now()]);
            $attempt->update(['current_step_id' => $next->id]);

            return;
        }

        $hasPendingEssay = $attempt->stepAttempts()->where('status', 'pending_review')->exists();
        $attempt->update([
            'status' => $hasPendingEssay ? 'pending_review' : 'completed',
            'completed_at' => $hasPendingEssay ? null : now(),
            'current_step_id' => $step->id,
        ]);
    }

    public function refreshAttemptScore(QuizAttempt $attempt): void
    {
        $attempt->load('stepAttempts.quizStep');
        $autoSteps = $attempt->stepAttempts->whereIn('quizStep.type', ['text_matching', 'table_checklist', 'image_text_matching']);
        $essaySteps = $attempt->stepAttempts->where('quizStep.type', 'essay');

        $autoScore = $autoSteps->count() ? round($autoSteps->avg('score'), 2) : null;
        $essayScore = $essaySteps->every(fn (QuizStepAttempt $stepAttempt) => $stepAttempt->status !== 'pending_review')
            ? ($essaySteps->count() ? round($essaySteps->avg('score'), 2) : null)
            : null;

        $finalScore = $essayScore === null
            ? null
            : round(collect([$autoScore, $essayScore])->filter(fn ($score) => $score !== null)->avg(), 2);

        $attempt->update([
            'auto_score' => $autoScore,
            'essay_score' => $essayScore,
            'final_score' => $finalScore,
            'status' => $finalScore === null ? $attempt->status : 'completed',
            'completed_at' => $finalScore === null ? $attempt->completed_at : now(),
        ]);
    }

    private function buildPresentationPayload(QuizStep $step): ?array
    {
        if (! in_array($step->type, ['text_matching', 'image_text_matching'], true)) {
            return null;
        }

        $itemKeys = collect($step->content_payload['items'] ?? [])
            ->pluck('key')
            ->filter()
            ->values()
            ->all();

        if ($itemKeys === []) {
            return null;
        }

        shuffle($itemKeys);

        return [
            'item_order' => $itemKeys,
        ];
    }
}
