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
            'courses' => Course::where('status', 'published')->with('modules.lessons')->get(),
        ]);
    }
}
