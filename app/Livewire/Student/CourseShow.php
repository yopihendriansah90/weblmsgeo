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
        $this->course = $course->load(['modules' => fn ($query) => $query
            ->where(function ($subQuery): void {
                $subQuery->where('type', 'lesson')
                    ->where('status', 'published');
            })
            ->orWhere(function ($subQuery): void {
                $subQuery->where('type', 'quiz')
                    ->where('status', 'published');
            })
            ->orderByRaw("CASE WHEN type = 'quiz' THEN 1 ELSE 0 END, sort_order")]);
    }

    public function render()
    {
        return view('livewire.student.course-show');
    }
}
