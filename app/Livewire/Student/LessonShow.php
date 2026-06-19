<?php

namespace App\Livewire\Student;

use App\Models\Module;
use App\Services\LessonProgressService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class LessonShow extends Component
{
    public Module $module;
    public Collection $timelineItems;
    public ?array $nextTimelineItem = null;
    public bool $hasFinalQuiz = false;
    public bool $isFinalQuizAvailable = false;

    public function mount(Module $module, LessonProgressService $progressService): void
    {
        abort_unless($module->status === 'published', 404);
        abort_unless(! $module->isQuiz(), 404);

        $student = auth()->user()->student;

        $this->loadModule($module);
        $progressService->open($student, $module);
        $this->refreshTimeline($student);
        $this->refreshQuizState();
    }

    public function complete(LessonProgressService $progressService): void
    {
        $student = auth()->user()->student;

        $progressService->complete($student, $this->module);
        session()->flash('status', 'Materi ditandai selesai.');
        $this->loadModule($this->module->fresh());
        $this->refreshTimeline($student);
        $this->refreshQuizState();

        if ($this->nextTimelineItem && filled($this->nextTimelineItem['href'] ?? null)) {
            $this->redirect($this->nextTimelineItem['href'], navigate: false);

            return;
        }
    }

    public function render()
    {
        return view('livewire.student.lesson-show');
    }

    private function buildTimelineItems($student): Collection
    {
        $lessonProgressByModule = $student->lessonProgress
            ->keyBy('module_id');

        $quizAttemptsByModule = $student->quizAttempts
            ->filter(fn ($attempt) => $attempt->quiz?->module)
            ->groupBy(fn ($attempt) => $attempt->quiz->module->id);

        $allowNextAvailable = true;

        return $this->module->course->modules->values()->map(function (Module $courseModule, int $index) use (&$allowNextAvailable, $lessonProgressByModule, $quizAttemptsByModule, $student) {
            $isCurrent = $courseModule->is($this->module);
            $progress = $lessonProgressByModule->get($courseModule->id);
            $attempts = $quizAttemptsByModule->get($courseModule->id, collect());
            $isCompleted = $courseModule->isQuiz()
                ? $attempts->contains(fn ($attempt) => $attempt->status === 'completed')
                : $progress?->status === 'completed';
            $isAvailable = $allowNextAvailable && ! $isCompleted;
            $publishedQuiz = $courseModule->isQuiz() ? $courseModule->publishedQuiz : null;
            $canOpenQuiz = $publishedQuiz?->canStudentStartAttempt($student->id) ?? false;

            $item = [
                'label' => $courseModule->isQuiz() ? 'Quiz' : 'Bab '.($index + 1),
                'title' => $courseModule->title,
                'status' => $isCompleted ? 'completed' : ($isCurrent ? 'current' : ($isAvailable ? 'available' : 'locked')),
                'icon' => $courseModule->isQuiz()
                    ? ($isCompleted ? 'check' : ($isAvailable ? 'quiz' : 'lock'))
                    : ($isCompleted ? 'check' : ($isCurrent ? 'auto_stories' : ($isAvailable ? 'auto_stories' : 'lock'))),
                'href' => $courseModule->isQuiz()
                    ? (($isCompleted || $isAvailable) && $canOpenQuiz ? route('student.quizzes.take', $publishedQuiz) : null)
                    : ($isCompleted || $isCurrent || $isAvailable ? route('student.modules.show', $courseModule) : null),
                'is_current' => $isCurrent,
                'is_completed' => $isCompleted,
            ];

            if (! $isCompleted && $allowNextAvailable) {
                $allowNextAvailable = false;
            }

            return $item;
        });
    }

    private function loadModule(Module $module): void
    {
        $this->module = $module->load([
            'course.modules' => fn ($query) => $query
                ->where('status', 'published')
                ->with('publishedQuiz')
                ->orderByRaw("CASE WHEN type = 'quiz' THEN 1 ELSE 0 END, sort_order"),
            'publishedQuiz.steps',
        ]);
    }

    private function refreshTimeline($student): void
    {
        $student->load(['lessonProgress.module', 'quizAttempts.quiz.module']);
        $this->timelineItems = $this->buildTimelineItems($student);
        $this->nextTimelineItem = $this->timelineItems
            ->first(fn (array $item) => $item['href'] && ! $item['is_current'] && $item['status'] === 'available');
    }

    private function refreshQuizState(): void
    {
        $quizModule = $this->module->course->modules->first(fn (Module $courseModule) => $courseModule->isQuiz());

        $this->hasFinalQuiz = (bool) $quizModule;
        $this->isFinalQuizAvailable = (bool) $quizModule?->publishedQuiz;
    }
}
