<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use App\Models\Module;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ModuleIndex extends Component
{
    public Course $course;
    public array $expandedModules = [];

    #[Layout('layouts.guru')]
    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    public function toggleModule(int $moduleId): void
    {
        if (in_array($moduleId, $this->expandedModules, true)) {
            $this->expandedModules = array_values(array_filter(
                $this->expandedModules,
                fn ($id) => $id !== $moduleId
            ));

            return;
        }

        $this->expandedModules[] = $moduleId;
    }

    public function expandAll(): void
    {
        $this->expandedModules = $this->course->modules()->pluck('id')->all();
    }

    public function collapseAll(): void
    {
        $this->expandedModules = [];
    }

    public function deleteModule(int $moduleId): void
    {
        $module = Module::where('course_id', $this->course->id)->findOrFail($moduleId);
        $module->delete();

        $this->expandedModules = array_values(array_filter(
            $this->expandedModules,
            fn ($id) => $id !== $moduleId
        ));

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

    public function toggleLessonStatus(int $lessonId): void
    {
        $lesson = $this->course->modules()
            ->with('lessons')
            ->get()
            ->flatMap(fn ($module) => $module->lessons)
            ->firstWhere('id', $lessonId);

        abort_unless($lesson, 404);

        $lesson->update([
            'status' => $lesson->status === 'published' ? 'draft' : 'published',
            'published_at' => $lesson->status === 'published' ? null : now(),
            'updated_by' => auth()->id(),
        ]);

        session()->flash('success', 'Status subbab berhasil diperbarui.');
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
