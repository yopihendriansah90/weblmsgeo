<?php

namespace App\Livewire\Guru;

use App\Models\Module;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ModulePreview extends Component
{
    public Module $module;

    #[Layout('layouts.guru')]
    public function mount(Module $module): void
    {
        abort_if($module->isQuiz(), 404);

        $this->module = $module->load('course');
    }

    public function render()
    {
        return view('livewire.guru.module-preview', [
            'title' => 'Preview Bab: '.$this->module->title,
        ]);
    }
}
