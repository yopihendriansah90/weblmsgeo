<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptAnswer extends Model
{
    protected $fillable = ['quiz_step_attempt_id', 'question_key', 'answer_payload', 'correct_answer_snapshot', 'is_correct', 'score_obtained', 'feedback'];

    protected function casts(): array
    {
        return ['answer_payload' => 'array', 'correct_answer_snapshot' => 'array', 'is_correct' => 'boolean'];
    }

    public function stepAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizStepAttempt::class, 'quiz_step_attempt_id');
    }
}
