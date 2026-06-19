<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = ['module_id', 'title', 'description', 'mode', 'allow_retake', 'max_attempts', 'status', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['allow_retake' => 'boolean'];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(QuizStep::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function canStudentStartAttempt(int $studentId): bool
    {
        if ($this->attempts()
            ->where('student_id', $studentId)
            ->whereIn('status', ['in_progress', 'pending_review'])
            ->exists()) {
            return true;
        }

        $attemptCount = $this->attempts()
            ->where('student_id', $studentId)
            ->count();

        if ($attemptCount === 0) {
            return true;
        }

        if (! $this->allow_retake) {
            return false;
        }

        return $this->max_attempts === null || $attemptCount < $this->max_attempts;
    }
}
