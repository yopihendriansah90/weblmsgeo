<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class LearningHistory extends Component
{
    public function render()
    {
        return view('livewire.student.learning-history', [
            'progressItems' => auth()->user()->student->lessonProgress()->with('lesson.module.course')->latest('last_opened_at')->get(),
        ]);
    }
}
