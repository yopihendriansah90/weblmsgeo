<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Module extends Model
{
    protected $fillable = [
        'course_id',
        'type',
        'title',
        'slug',
        'description',
        'content',
        'estimated_duration',
        'sort_order',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function publishedQuiz(): HasOne
    {
        return $this->hasOne(Quiz::class)->where('status', 'published');
    }
}
