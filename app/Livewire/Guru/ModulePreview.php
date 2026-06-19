<?php

namespace App\Livewire\Guru;

use App\Models\Module;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ModulePreview extends Component
{
    public Module $module;

    public Collection $timelineItems;

    public ?array $nextTimelineItem = null;

    public bool $hasFinalQuiz = false;

    public bool $isFinalQuizAvailable = false;

    public string $backUrl;

    #[Layout('layouts.student-preview')]
    public function mount(Module $module): void
    {
        abort_if($module->isQuiz(), 404);

        $this->loadModule($module);
        $this->timelineItems = $this->buildTimelineItems();
        $this->nextTimelineItem = $this->timelineItems
            ->first(fn (array $item) => $item['href'] && ! $item['is_current'] && $item['status'] === 'available');
        $this->refreshQuizState();
        $this->backUrl = $this->resolveBackUrl();
    }

    public function render()
    {
        return view('livewire.guru.module-preview', [
            'title' => 'Preview Bab: '.$this->module->title,
        ]);
    }

    private function loadModule(Module $module): void
    {
        $this->module = $module->load([
            'course.modules' => fn ($query) => $query
                ->where(function ($subQuery) use ($module): void {
                    $subQuery
                        ->where('status', 'published')
                        ->orWhere('id', $module->id);
                })
                ->with('publishedQuiz')
                ->orderByRaw("CASE WHEN type = 'quiz' THEN 1 ELSE 0 END, sort_order"),
        ]);
    }

    private function buildTimelineItems(): Collection
    {
        $allowNextAvailable = true;

        return $this->module->course->modules->values()->map(function (Module $courseModule, int $index) use (&$allowNextAvailable) {
            $isCurrent = $courseModule->is($this->module);
            $isQuiz = $courseModule->isQuiz();
            $isCompleted = ! $isCurrent && ! $isQuiz && $allowNextAvailable;
            $isAvailable = $isCurrent || ($allowNextAvailable && ! $isCompleted);

            $item = [
                'label' => $isQuiz ? 'Quiz' : 'Bab '.($index + 1),
                'title' => $courseModule->title,
                'status' => $isCurrent ? 'current' : ($isCompleted ? 'completed' : ($isAvailable ? 'available' : 'locked')),
                'icon' => $isQuiz
                    ? ($isAvailable ? 'quiz' : 'lock')
                    : ($isCompleted ? 'check' : ($isCurrent ? 'auto_stories' : ($isAvailable ? 'auto_stories' : 'lock'))),
                'href' => ! $isQuiz && ! $isCurrent
                    ? route('guru.modules.preview', $courseModule)
                    : null,
                'is_current' => $isCurrent,
                'is_completed' => $isCompleted,
            ];

            if ($isCurrent || (! $isCompleted && $allowNextAvailable)) {
                $allowNextAvailable = false;
            }

            return $item;
        });
    }

    private function refreshQuizState(): void
    {
        $quizModule = $this->module->course->modules->first(fn (Module $courseModule) => $courseModule->isQuiz());

        $this->hasFinalQuiz = (bool) $quizModule;
        $this->isFinalQuizAvailable = (bool) $quizModule?->publishedQuiz;
    }

    private function resolveBackUrl(): string
    {
        $backUrl = request()->query('back');

        if (is_string($backUrl) && str_starts_with($backUrl, url('/'))) {
            return $backUrl;
        }

        return route('guru.modules.index', $this->module->course);
    }
}
