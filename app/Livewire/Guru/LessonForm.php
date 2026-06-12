<?php

namespace App\Livewire\Guru;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class LessonForm extends Component
{
    public ?Module $module = null;
    public ?Lesson $lesson = null;

    public string $title = '';
    public string $slug = '';
    public ?string $summary = null;
    public ?string $content = null;
    public ?int $estimated_duration = null;
    public int $sort_order = 1;
    public bool $is_required = true;
    public string $status = 'draft';

    #[Layout('layouts.guru')]
    public function mount(?Module $module = null, ?Lesson $lesson = null): void
    {
        $this->module = $module ?? $lesson?->module;
        $this->lesson = $lesson;

        if ($lesson) {
            $this->lesson = $lesson->load('module.course');
            $this->title = $lesson->title;
            $this->slug = $lesson->slug;
            $this->summary = $lesson->summary;
            $this->content = $lesson->content;
            $this->estimated_duration = $lesson->estimated_duration;
            $this->sort_order = $lesson->sort_order;
            $this->is_required = $lesson->is_required;
            $this->status = $lesson->status;
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->lesson) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        abort_unless($this->module, 404);

        $this->title = trim($this->title);
        $this->slug = Str::slug($this->slug ?: $this->title);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lessons', 'slug')
                    ->where(fn ($query) => $query->where('module_id', $this->module->id))
                    ->ignore($this->lesson?->id),
            ],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'estimated_duration' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_required' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $payload = [
            ...$validated,
            'module_id' => $this->module->id,
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'created_by' => $this->lesson?->created_by ?? auth()->id(),
            'updated_by' => auth()->id(),
        ];

        if ($this->lesson) {
            $this->lesson->update($payload);
        } else {
            $this->lesson = Lesson::create($payload);
        }

        session()->flash('success', 'Subbab berhasil disimpan.');

        return redirect()->route('guru.modules.index', $this->module->course);
    }

    public function render()
    {
        return view('livewire.guru.lesson-form', [
            'title' => $this->lesson ? 'Edit Subbab' : 'Tambah Subbab',
        ]);
    }
}
