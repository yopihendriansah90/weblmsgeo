<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\Student;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    #[Layout('layouts.guru')]
    public function render()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if ($user->hasRole('super_admin') && !$teacher) {
            return view('livewire.guru.dashboard', [
                'title' => 'Dashboard Guru (Admin View)',
                'studentCount' => \App\Models\Student::count(),
                'schoolCount' => \App\Models\School::count(),
                'pendingEssays' => \App\Models\QuizAttempt::where('status', 'pending_review')->count(),
                'courseCount' => \App\Models\Course::count(),
            ]);
        }

        if (!$teacher) {
            abort(403, 'Profil guru tidak ditemukan.');
        }
        
        // Get assigned school IDs
        $assignedSchoolIds = $teacher->activeAssignments()->pluck('school_id');

        // Count students in assigned schools
        $studentCount = Student::whereIn('school_id', $assignedSchoolIds)->count();
        
        // Count assigned schools
        $schoolCount = $assignedSchoolIds->count();
        
        // Count pending essays for students in assigned schools
        $pendingEssays = QuizAttempt::whereIn('student_id', function ($query) use ($assignedSchoolIds) {
            $query->select('id')->from('students')->whereIn('school_id', $assignedSchoolIds);
        })->where('status', 'pending_review')->count();
        
        // Count total courses (shared for now, or could be filtered)
        $courseCount = Course::count();

        return view('livewire.guru.dashboard', [
            'title' => 'Dashboard Guru',
            'studentCount' => $studentCount,
            'schoolCount' => $schoolCount,
            'pendingEssays' => $pendingEssays,
            'courseCount' => $courseCount,
        ]);
    }
}
