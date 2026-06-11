<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopesTeacherSchoolData
{
    protected static function scopeToTeacherSchools(Builder $query, string $relationPath): Builder
    {
        $user = auth()->user();

        if (! $user?->hasRole('guru') || $user->hasRole('super_admin')) {
            return $query;
        }

        $schoolIds = $user->teacher?->activeAssignments()->pluck('school_id') ?? collect();

        if ($relationPath === '') {
            return $query->whereIn('school_id', $schoolIds);
        }

        return $query->whereHas($relationPath, fn (Builder $relationQuery) => $relationQuery->whereIn('school_id', $schoolIds));
    }
}
