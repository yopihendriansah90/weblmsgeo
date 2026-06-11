<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class CourseIndex extends Component
{
    use WithPagination;

    #[Layout('layouts.guru')]
    public function deleteCourse(int $courseId): void
    {
        $course = Course::findOrFail($courseId);

        DB::transaction(function () use ($course): void {
            $course->delete();
        });

        session()->flash('success', 'Materi berhasil dihapus.');
    }

    public function render()
    {
        $courses = Course::latest()->paginate(10);

        return view('livewire.guru.course-index', [
            'title' => 'Daftar Materi Pembelajaran',
            'courses' => $courses,
        ]);
    }
}
