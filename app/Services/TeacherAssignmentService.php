<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\TeacherSchoolAssignment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeacherAssignmentService
{
    public function assign(Teacher $teacher, int $schoolId, User $assignedBy): TeacherSchoolAssignment
    {
        return DB::transaction(function () use ($teacher, $schoolId, $assignedBy) {
            $assignment = TeacherSchoolAssignment::updateOrCreate(
                ['teacher_id' => $teacher->id, 'school_id' => $schoolId],
                [
                    'assigned_by' => $assignedBy->id,
                    'status' => 'active',
                    'assigned_at' => now(),
                    'revoked_at' => null,
                    'revoke_reason' => null,
                ],
            );

            activity('teacher_assignment')
                ->performedOn($assignment)
                ->causedBy($assignedBy)
                ->withProperties(['teacher_id' => $teacher->id, 'school_id' => $schoolId])
                ->log('teacher_assigned_to_school');

            return $assignment;
        });
    }

    public function revoke(TeacherSchoolAssignment $assignment, ?User $revokedBy = null, ?string $reason = null): void
    {
        $assignment->update([
            'status' => 'inactive',
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ]);

        activity('teacher_assignment')
            ->performedOn($assignment)
            ->causedBy($revokedBy)
            ->withProperties(['reason' => $reason])
            ->log('teacher_school_assignment_revoked');
    }

    public function accessibleSchoolIds(User $user): Collection
    {
        if ($user->hasRole('super_admin')) {
            return collect();
        }

        return $user->teacher?->activeAssignments()->pluck('school_id') ?? collect();
    }
}
