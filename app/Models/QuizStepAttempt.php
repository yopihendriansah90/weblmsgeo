<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuizStepAttempt extends Model
{
    protected $fillable = ['quiz_attempt_id', 'quiz_step_id', 'started_at', 'submitted_at', 'status', 'score', 'result_payload', 'feedback'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'submitted_at' => 'datetime', 'result_payload' => 'array'];
    }

    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    public function quizStep(): BelongsTo
    {
        return $this->belongsTo(QuizStep::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function essayReview(): HasOne
    {
        return $this->hasOne(EssayReview::class);
    }
}
