<?php

namespace App\Livewire\Student;

use App\Models\Module;
use App\Services\LessonProgressService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class LessonShow extends Component
{
    public Module $module;

    public function mount(Module $module, LessonProgressService $progressService): void
    {
        abort_unless($module->status === 'published', 404);
        abort_unless(! $module->isQuiz(), 404);
        $this->module = $module->load(['course', 'publishedQuiz.steps']);
        $progressService->open(auth()->user()->student, $module);
    }

    public function complete(LessonProgressService $progressService): void
    {
        $progressService->complete(auth()->user()->student, $this->module);
        session()->flash('status', 'Materi ditandai selesai.');
    }

    public function render()
    {
        return view('livewire.student.lesson-show');
    }
}
