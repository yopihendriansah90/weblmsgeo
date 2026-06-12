<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use App\Models\Module;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ModuleIndex extends Component
{
    public Course $course;
    public string $search = '';

    #[Layout('layouts.guru')]
    public function mount(Course $course): void
    {
        $this->course = $course;
    }

    public function requestDeleteModule(int $moduleId): void
    {
        $this->dispatch(
            'guru-confirm-delete',
            componentId: $this->getId(),
            action: 'deleteModule',
            id: $moduleId,
            title: 'Hapus bab ini?',
            text: 'Seluruh isi bab pembahasan ini akan ikut terhapus.',
            confirmButtonText: 'Ya, hapus bab',
        );
    }

    public function deleteModule(int $moduleId): void
    {
        $module = Module::where('course_id', $this->course->id)->findOrFail($moduleId);
        abort_unless(! $module->isQuiz(), 403);
        $module->delete();

        $this->dispatch('guru-notify', type: 'success', message: 'Bab berhasil dihapus.');
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
            ->with(['quizzes' => fn ($query) => $query->latest()])
            ->withCount('quizzes')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.guru.module-index', [
            'title' => 'Item Materi: '.$this->course->title,
            'modules' => $modules,
        ]);
    }
}
