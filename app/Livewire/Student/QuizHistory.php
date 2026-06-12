<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class QuizHistory extends Component
{
    public function render()
    {
        return view('livewire.student.quiz-history', [
            'attempts' => auth()->user()->student->quizAttempts()->with('quiz.module.course')->latest()->get(),
        ]);
    }
}
