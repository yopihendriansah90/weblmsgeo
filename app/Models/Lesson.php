<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lesson extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['module_id', 'title', 'slug', 'summary', 'content', 'estimated_duration', 'sort_order', 'is_required', 'status', 'published_at', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['is_required' => 'boolean', 'published_at' => 'datetime'];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
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
