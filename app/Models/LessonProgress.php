<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $fillable = ['student_id', 'lesson_id', 'status', 'last_opened_at', 'completed_at', 'duration_seconds'];

    protected function casts(): array
    {
        return ['last_opened_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
