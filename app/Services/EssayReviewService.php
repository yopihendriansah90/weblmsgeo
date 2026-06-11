<?php

namespace App\Services;

use App\Models\EssayReview;
use App\Models\QuizStepAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EssayReviewService
{
    public function __construct(private readonly QuizFlowService $quizFlowService) {}

    public function review(QuizStepAttempt $stepAttempt, User $reviewer, float $score, ?string $feedback = null): EssayReview
    {
        return DB::transaction(function () use ($stepAttempt, $reviewer, $score, $feedback) {
            $review = EssayReview::updateOrCreate(
                ['quiz_step_attempt_id' => $stepAttempt->id],
                [
                    'reviewed_by' => $reviewer->id,
                    'score' => $score,
                    'feedback' => $feedback,
                    'status' => 'reviewed',
                    'reviewed_at' => now(),
                ],
            );

            $stepAttempt->update([
                'status' => 'completed',
                'score' => $score,
                'feedback' => $feedback,
                'submitted_at' => $stepAttempt->submitted_at ?? now(),
            ]);

            $this->quizFlowService->refreshAttemptScore($stepAttempt->quizAttempt);

            return $review;
        });
    }
}
