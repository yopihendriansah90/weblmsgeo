<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['title', 'slug', 'description', 'status', 'created_by', 'updated_by'];

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)
            ->orderByRaw("CASE WHEN type = 'quiz' THEN 1 ELSE 0 END, sort_order");
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course_covers')->singleFile();
    }

    public function coverImage(): MorphOne
    {
        return $this->media()->one()->where('collection_name', 'course_covers');
    }

    public function coverUrl(): ?string
    {
        $media = $this->getFirstMedia('course_covers');

        if (! $media) {
            return null;
        }

        return url('storage/'.$media->getPathRelativeToRoot());
    }
}
