<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ModuleForm extends Component
{
    public ?Course $course = null;
    public ?Module $module = null;

    public string $title = '';
    public string $slug = '';
    public ?string $description = null;
    public ?string $content = null;
    public ?int $estimated_duration = null;
    public int $sort_order = 1;
    public string $status = 'draft';

    #[Layout('layouts.guru')]
    public function mount(?Course $course = null, ?Module $module = null): void
    {
        $this->course = $course ?? $module?->course;
        $this->module = $module;

        if ($module?->isQuiz()) {
            abort(404);
        }

        if ($module) {
            $this->title = $module->title;
            $this->slug = $module->slug;
            $this->description = $module->description;
            $this->content = $module->content;
            $this->estimated_duration = $module->estimated_duration;
            $this->sort_order = $module->sort_order;
            $this->status = $module->status;
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->module) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        abort_unless($this->course, 404);

        $this->title = trim($this->title);
        $this->slug = Str::slug($this->slug ?: $this->title);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('modules', 'slug')
                    ->where(fn ($query) => $query->where('course_id', $this->course->id))
                    ->ignore($this->module?->id),
            ],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'estimated_duration' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $payload = [
            ...$validated,
            'course_id' => $this->course->id,
            'type' => 'lesson',
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'created_by' => $this->module?->created_by ?? auth()->id(),
            'updated_by' => auth()->id(),
        ];

        if ($this->module) {
            $this->module->update($payload);
        } else {
            $this->module = Module::create($payload);
        }

        session()->flash('success', 'Bab berhasil disimpan.');

        return redirect()->route('guru.modules.index', $this->course);
    }

    public function render()
    {
        return view('livewire.guru.module-form', [
            'title' => $this->module ? 'Edit Bab' : 'Tambah Bab',
        ]);
    }
}
