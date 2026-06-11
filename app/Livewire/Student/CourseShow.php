<?php

namespace App\Livewire\Student;

use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class CourseShow extends Component
{
    public Course $course;

    public function mount(Course $course): void
    {
        abort_unless($course->status === 'published', 404);
        $this->course = $course->load(['modules' => fn ($query) => $query->where('status', 'published'), 'modules.lessons' => fn ($query) => $query->where('status', 'published')]);
    }

    public function render()
    {
        return view('livewire.student.course-show');
    }
}
