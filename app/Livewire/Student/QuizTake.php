<?php

namespace App\Livewire\Student;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizStep;
use App\Models\QuizStepAttempt;
use App\Services\QuizFlowService;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.student')]
class QuizTake extends Component
{
    public Quiz $quiz;

    public QuizAttempt $attempt;

    public ?int $activeStepId = null;

    public array $answer = [];

    public ?string $errorMessage = null;

    public function mount(Quiz $quiz, QuizFlowService $flowService): void
    {
        abort_unless($quiz->status === 'published', 404);
        $this->quiz = $quiz->load('module.course', 'steps');
        $this->attempt = $flowService->startOrContinue($quiz, auth()->user()->student);
        $this->activeStepId = $this->attempt->current_step_id ?? $this->quiz->steps->first()?->id;
    }

    public function setActiveStep(int $stepId): void
    {
        $stepAttempt = $this->attempt->stepAttempts()->where('quiz_step_id', $stepId)->first();

        if (! $stepAttempt || $stepAttempt->status === 'locked') {
            return;
        }

        $this->activeStepId = $stepId;
        $this->answer = [];
        $this->errorMessage = null;
    }

    public function submit(QuizFlowService $flowService): void
    {
        $this->errorMessage = null;
        $step = QuizStep::findOrFail($this->activeStepId);

        try {
            $flowService->submitStep($this->attempt->fresh(['quiz.steps', 'stepAttempts']), $step, $this->normalizedAnswer($step));
            $this->attempt = $this->attempt->fresh(['quiz.steps', 'stepAttempts.quizStep', 'stepAttempts.answers', 'stepAttempts.essayReview']);
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function next(): void
    {
        $current = $this->quiz->steps->firstWhere('id', $this->activeStepId);
        $next = $this->quiz->steps->where('sort_order', '>', $current?->sort_order)->sortBy('sort_order')->first();

        if ($next) {
            $this->setActiveStep($next->id);
        }
    }

    public function isLastStep(): bool
    {
        $lastStep = $this->quiz->steps->sortBy('sort_order')->last();

        return $lastStep?->id === $this->activeStepId;
    }

    private function normalizedAnswer(QuizStep $step): array
    {
        if ($step->type === 'essay') {
            $this->validate(['answer.essay' => ['required', 'string', 'min:3']]);

            return ['essay' => trim($this->answer['essay'])];
        }

        if ($step->type === 'table_checklist') {
            $answers = collect($this->answer['rows'] ?? [])
                ->filter(fn ($columnId) => filled($columnId))
                ->map(fn ($columnId, $rowId) => ['row_id' => (string) $rowId, 'selected_column_id' => (string) $columnId])
                ->values()
                ->all();

            return ['answers' => $answers];
        }

        $answers = collect(Arr::get($this->answer, 'matches', []))
            ->filter(fn ($optionKey) => filled($optionKey))
            ->map(fn ($optionKey, $itemKey) => ['item_key' => (string) $itemKey, 'selected_option_key' => (string) $optionKey])
            ->values()
            ->all();

        return ['answers' => $answers];
    }

    public function render()
    {
        return view('livewire.student.quiz-take', [
            'steps' => $this->quiz->steps()->where('status', 'published')->get(),
            'stepAttempts' => $this->attempt->stepAttempts()->with(['quizStep', 'answers', 'essayReview'])->get()->keyBy('quiz_step_id'),
            'activeStep' => QuizStep::find($this->activeStepId),
        ]);
    }

    public function orderedItems(QuizStep $step, ?QuizStepAttempt $stepAttempt = null): array
    {
        $items = collect($step->content_payload['items'] ?? []);

        if (! in_array($step->type, ['text_matching', 'image_text_matching'], true)) {
            return $items->all();
        }

        $order = data_get($stepAttempt?->result_payload, 'presentation.item_order', []);

        if ($order === []) {
            return $items->all();
        }

        $positionMap = array_flip($order);

        return $items
            ->sortBy(fn (array $item): int => $positionMap[$item['key']] ?? PHP_INT_MAX)
            ->values()
            ->all();
    }
}
