<?php

namespace App\Livewire\Guru;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CourseForm extends Component
{
    public ?Course $course = null;

    public string $title = '';
    public string $slug = '';
    public ?string $description = null;
    public string $status = 'draft';

    #[Layout('layouts.guru')]
    public function mount(?Course $course = null): void
    {
        $this->course = $course;

        if ($course) {
            $this->title = $course->title;
            $this->slug = $course->slug;
            $this->description = $course->description;
            $this->status = $course->status;
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->course) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->title = trim($this->title);
        $this->slug = Str::slug($this->slug ?: $this->title);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'slug')->ignore($this->course?->id),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $payload = [
            ...$validated,
            'created_by' => $this->course?->created_by ?? auth()->id(),
            'updated_by' => auth()->id(),
        ];

        if ($this->course) {
            $this->course->update($payload);
        } else {
            $this->course = Course::create($payload);
        }

        session()->flash('success', 'Materi berhasil disimpan.');

        return redirect()->route('guru.courses.index');
    }

    public function render()
    {
        return view('livewire.guru.course-form', [
            'title' => $this->course ? 'Edit Materi' : 'Tambah Materi',
        ]);
    }
}
