<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EssayReview extends Model
{
    protected $fillable = ['quiz_step_attempt_id', 'reviewed_by', 'score', 'feedback', 'status', 'reviewed_at'];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function stepAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizStepAttempt::class, 'quiz_step_attempt_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
