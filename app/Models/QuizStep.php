<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizStep extends Model
{
    protected $fillable = ['quiz_id', 'title', 'type', 'instruction', 'content_payload', 'sort_order', 'answer_mode', 'is_required', 'show_result_after_submit', 'allow_next_after_submit', 'status'];

    protected function casts(): array
    {
        return [
            'content_payload' => 'array',
            'is_required' => 'boolean',
            'show_result_after_submit' => 'boolean',
            'allow_next_after_submit' => 'boolean',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizStepAttempt::class);
    }
}
