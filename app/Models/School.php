<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class School extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name', 'code', 'level', 'address', 'city', 'province', 'email', 'phone', 'status'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherSchoolAssignment::class);
    }
}
