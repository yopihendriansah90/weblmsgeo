<?php

namespace App\Filament\Concerns;

trait HasPanelRoleAccess
{
    protected static function currentUserCanAccess(array $roles): bool
    {
        $user = auth()->user();

        return $user !== null && $user->status === 'active' && $user->hasAnyRole($roles);
    }

    protected static function currentUserIsSuperAdmin(): bool
    {
        return static::currentUserCanAccess(['super_admin']);
    }

    protected static function currentUserIsTeacherOrAdmin(): bool
    {
        return static::currentUserCanAccess(['super_admin', 'guru']);
    }

    protected static function currentUserIsTeacherOnly(): bool
    {
        return static::currentUserCanAccess(['guru']);
    }
}
