<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class Profile extends Component
{
    public function render()
    {
        return view('livewire.student.profile', [
            'student' => auth()->user()->student()->with(['user', 'school'])->firstOrFail(),
        ]);
    }
}
