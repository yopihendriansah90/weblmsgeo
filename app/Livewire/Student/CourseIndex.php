<?php

namespace App\Livewire\Student;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class CourseIndex extends Component
{
    public function render()
    {
        return view('livewire.student.course-index', [
            'courses' => Course::where('status', 'published')
                ->with('media')
                ->withCount(['modules as lessons_count' => fn ($query) => $query->where('type', 'lesson')])
                ->get(),
        ]);
    }
}
