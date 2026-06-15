<?php

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class CourseShow extends Component
{
    public Course $course;

    public Collection $courseItems;

    public function mount(Course $course): void
    {
        abort_unless($course->status === 'published', 404);
        $this->course = $course->load(['modules' => fn ($query) => $query
            ->where(function ($subQuery): void {
                $subQuery->where('type', 'lesson')
                    ->where('status', 'published');
            })
            ->orWhere(function ($subQuery): void {
                $subQuery->where('type', 'quiz')
                    ->where('status', 'published');
            })
            ->with('publishedQuiz')
            ->orderByRaw("CASE WHEN type = 'quiz' THEN 1 ELSE 0 END, sort_order")]);

        $student = auth()->user()->student()->with(['lessonProgress.module', 'quizAttempts.quiz.module'])->firstOrFail();
        $this->courseItems = $this->buildCourseItems($student);
    }

    public function render()
    {
        return view('livewire.student.course-show');
    }

    private function buildCourseItems($student): Collection
    {
        $lessonProgressByModule = $student->lessonProgress->keyBy('module_id');
        $quizAttemptsByModule = $student->quizAttempts
            ->filter(fn ($attempt) => $attempt->quiz?->module)
            ->groupBy(fn ($attempt) => $attempt->quiz->module->id);

        $allowNextAvailable = true;

        return $this->course->modules->values()->map(function (Module $module, int $index) use (&$allowNextAvailable, $lessonProgressByModule, $quizAttemptsByModule) {
            $progress = $lessonProgressByModule->get($module->id);
            $attempts = $quizAttemptsByModule->get($module->id, collect());
            $isQuiz = $module->isQuiz();

            $isCompleted = $isQuiz
                ? $attempts->contains(fn ($attempt) => $attempt->status === 'completed')
                : $progress?->status === 'completed';

            $isAvailable = $allowNextAvailable && ! $isCompleted;

            $href = null;

            if ($isQuiz) {
                if (($isCompleted || $isAvailable) && $module->publishedQuiz) {
                    $href = route('student.quizzes.take', $module->publishedQuiz);
                }
            } elseif ($isCompleted || $isAvailable) {
                $href = route('student.modules.show', $module);
            }

            if (! $isCompleted && $allowNextAvailable) {
                $allowNextAvailable = false;
            }

            return [
                'module' => $module,
                'label' => $isQuiz ? 'Quiz Materi' : 'Bab Pembahasan',
                'badge' => $isQuiz ? 'Item Quiz' : 'Bab '.($index + 1),
                'is_quiz' => $isQuiz,
                'is_completed' => $isCompleted,
                'is_available' => $isAvailable,
                'is_locked' => ! $isCompleted && ! $isAvailable,
                'href' => $href,
            ];
        });
    }
}
