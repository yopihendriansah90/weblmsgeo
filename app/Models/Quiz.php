<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = ['lesson_id', 'title', 'description', 'mode', 'allow_retake', 'max_attempts', 'status', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['allow_retake' => 'boolean'];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(QuizStep::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
