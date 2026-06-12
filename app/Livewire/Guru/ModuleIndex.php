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
    public string $search = '';

    #[Layout('layouts.guru')]
    public function mount(Course $course): void
    {
        $this->course = $course;

        $this->expandedModules = session()->get($this->expandedSessionKey(), []);
    }

    public function toggleModule(int $moduleId): void
    {
        if (in_array($moduleId, $this->expandedModules, true)) {
            $this->expandedModules = array_values(array_filter(
                $this->expandedModules,
                fn ($id) => $id !== $moduleId
            ));

            $this->persistExpandedModules();

            return;
        }

        $this->expandedModules[] = $moduleId;
        $this->persistExpandedModules();
    }

    public function expandAll(): void
    {
        $this->expandedModules = $this->course->modules()->pluck('id')->all();
        $this->persistExpandedModules();
    }

    public function collapseAll(): void
    {
        $this->expandedModules = [];
        $this->persistExpandedModules();
    }

    public function toggleAllModules(): void
    {
        $moduleIds = $this->course->modules()->pluck('id')->all();

        if (! empty($moduleIds) && empty(array_diff($moduleIds, $this->expandedModules))) {
            $this->collapseAll();

            return;
        }

        $this->expandAll();
    }

    protected function persistExpandedModules(): void
    {
        session()->put($this->expandedSessionKey(), $this->expandedModules);
    }

    protected function expandedSessionKey(): string
    {
        return 'guru.module-index.expanded.'.$this->course->id;
    }

    public function requestDeleteModule(int $moduleId): void
    {
        $this->dispatch(
            'guru-confirm-delete',
            componentId: $this->getId(),
            action: 'deleteModule',
            id: $moduleId,
            title: 'Hapus bab ini?',
            text: 'Seluruh subbab di dalam bab ini juga akan ikut terhapus.',
            confirmButtonText: 'Ya, hapus bab',
        );
    }

    public function requestDeleteLesson(int $lessonId): void
    {
        $this->dispatch(
            'guru-confirm-delete',
            componentId: $this->getId(),
            action: 'deleteLesson',
            id: $lessonId,
            title: 'Hapus subbab ini?',
            text: 'Subbab yang dihapus tidak bisa dikembalikan.',
            confirmButtonText: 'Ya, hapus subbab',
        );
    }

    public function deleteModule(int $moduleId): void
    {
        $module = Module::where('course_id', $this->course->id)->findOrFail($moduleId);
        $module->delete();

        $this->expandedModules = array_values(array_filter(
            $this->expandedModules,
            fn ($id) => $id !== $moduleId
        ));

        $this->dispatch('guru-notify', type: 'success', message: 'Bab berhasil dihapus.');
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

        $this->dispatch('guru-notify', type: 'success', message: 'Subbab berhasil dihapus.');
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

        $this->dispatch('guru-notify', type: 'success', message: 'Status subbab berhasil diperbarui.');
    }

    public function render()
    {
        $search = trim($this->search);

        $modules = $this->course->modules()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->with(['lessons' => fn ($query) => $query->orderBy('sort_order')])
            ->withCount('lessons')
            ->orderBy('sort_order')
            ->get();
        $allModulesExpanded = $modules->isNotEmpty()
            && $modules->pluck('id')->diff($this->expandedModules)->isEmpty();

        return view('livewire.guru.module-index', [
            'title' => 'Bab: '.$this->course->title,
            'modules' => $modules,
            'allModulesExpanded' => $allModulesExpanded,
        ]);
    }
}
