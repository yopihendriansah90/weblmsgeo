<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use App\Models\Module;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ModuleIndex extends Component
{
    public Course $course;

    #[Layout('layouts.guru')]
    public function deleteModule(int $moduleId): void
    {
        $module = Module::where('course_id', $this->course->id)->findOrFail($moduleId);
        $module->delete();

        session()->flash('success', 'Bab berhasil dihapus.');
    }

    public function deleteLesson(int $lessonId): void
    {
        $lesson = $this->course->modules()
            ->with('lessons')
            ->get()
            ->flatMap(fn ($module) => $module->lessons)
            ->firstWhere('id', $lessonId);

        abort_unless($lesson, 404);

        $lesson->delete();

        session()->flash('success', 'Subbab berhasil dihapus.');
    }

    public function render()
    {
        $modules = $this->course->modules()
            ->with(['lessons' => fn ($query) => $query->orderBy('sort_order')])
            ->withCount('lessons')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.guru.module-index', [
            'title' => 'Bab: '.$this->course->title,
            'modules' => $modules,
        ]);
    }
}
