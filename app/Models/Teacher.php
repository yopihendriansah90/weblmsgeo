<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Teacher extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['user_id', 'teacher_code', 'phone', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherSchoolAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->assignments()->where('status', 'active');
    }
}
