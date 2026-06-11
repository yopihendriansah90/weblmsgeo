<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'student_id', 'current_step_id', 'started_at', 'completed_at', 'auto_score', 'essay_score', 'final_score', 'status'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(QuizStep::class, 'current_step_id');
    }

    public function stepAttempts(): HasMany
    {
        return $this->hasMany(QuizStepAttempt::class);
    }
}
