<?php

namespace App\Livewire\Student;

use App\Models\Lesson;
use App\Services\LessonProgressService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class LessonShow extends Component
{
    public Lesson $lesson;

    public function mount(Lesson $lesson, LessonProgressService $progressService): void
    {
        abort_unless($lesson->status === 'published', 404);
        $this->lesson = $lesson->load('module.course', 'publishedQuiz', 'media');
        $progressService->open(auth()->user()->student, $lesson);
    }

    public function complete(LessonProgressService $progressService): void
    {
        $progressService->complete(auth()->user()->student, $this->lesson);
        session()->flash('status', 'Materi ditandai selesai.');
    }

    public function render()
    {
        return view('livewire.student.lesson-show');
    }
}
