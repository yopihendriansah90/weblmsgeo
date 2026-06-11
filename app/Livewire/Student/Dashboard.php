<?php

namespace App\Livewire\Student;

use App\Services\StudentDashboardService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class Dashboard extends Component
{
    public function render(StudentDashboardService $dashboardService)
    {
        $student = auth()->user()->student()->with(['school', 'user'])->firstOrFail();

        return view('livewire.student.dashboard', [
            'student' => $student,
            'summary' => $dashboardService->summary($student),
        ]);
    }
}
