<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.guru')]
class CourseIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public function requestDeleteCourse(int $courseId): void
    {
        $this->dispatch(
            'guru-confirm-delete',
            componentId: $this->getId(),
            action: 'deleteCourse',
            id: $courseId,
            title: 'Hapus materi ini?',
            text: 'Materi dan seluruh bab di dalamnya akan ikut terhapus.',
            confirmButtonText: 'Ya, hapus materi',
        );
    }

    public function deleteCourse(int $courseId): void
    {
        $course = Course::findOrFail($courseId);

        DB::transaction(function () use ($course): void {
            $course->delete();
        });
        $this->dispatch('guru-notify', type: 'success', message: 'Materi berhasil dihapus.');
    }

    public function render()
    {
        $courses = Course::with('media')
            ->withCount(['modules as lessons_count' => fn ($query) => $query->where('type', 'lesson')])
            ->latest()
            ->paginate(9);

        return view('livewire.guru.course-index', [
            'title' => 'Daftar Materi Pembelajaran',
            'courses' => $courses,
        ]);
    }
}
