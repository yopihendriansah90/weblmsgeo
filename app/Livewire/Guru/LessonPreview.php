<?php

namespace App\Livewire\Guru;

use App\Models\Lesson;
use Livewire\Attributes\Layout;
use Livewire\Component;

class LessonPreview extends Component
{
    public Lesson $lesson;

    #[Layout('layouts.guru')]
    public function mount(Lesson $lesson): void
    {
        $this->lesson = $lesson->load('module.course');
    }

    public function render()
    {
        return view('livewire.guru.lesson-preview', [
            'title' => 'Preview Subbab',
        ]);
    }
}
