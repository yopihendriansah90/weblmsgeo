<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSchoolAssignment extends Model
{
    protected $fillable = ['teacher_id', 'school_id', 'assigned_by', 'status', 'assigned_at', 'revoked_at', 'revoke_reason'];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
