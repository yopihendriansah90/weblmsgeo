<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;

class StudentDashboardService
{
    public function __construct(private readonly LessonProgressService $progressService) {}

    public function summary(Student $student): array
    {
        $attempts = $student->quizAttempts()->with(['quiz.module.course', 'stepAttempts.quizStep'])->latest()->get();
        $latestAttemptsByQuizId = $attempts->groupBy('quiz_id')->map(fn ($group) => $group->first());
        $latestCompletedAttempt = $attempts->first(fn (QuizAttempt $attempt): bool => $attempt->final_score !== null);
        $recentMaterials = LessonProgress::query()
            ->where('student_id', $student->id)
            ->whereHas('module', fn ($query) => $query->where('type', 'lesson'))
            ->with(['module.course'])
            ->latest('last_opened_at')
            ->take(4)
            ->get()
            ->unique('module_id')
            ->values();

        return [
            'progress_percentage' => $this->progressService->percentage($student),
            'last_module' => LessonProgress::query()
                ->where('student_id', $student->id)
                ->whereHas('module', fn ($query) => $query->where('type', 'lesson'))
                ->with('module.course')
                ->latest('last_opened_at')
                ->first()?->module,
            'recent_materials' => $recentMaterials,
            'available_quizzes' => Quiz::query()
                ->where('status', 'published')
                ->whereHas('module', function ($query): void {
                    $query->where('type', 'quiz')
                        ->where('status', 'published')
                        ->whereHas('course', fn ($courseQuery) => $courseQuery->where('status', 'published'));
                })
                ->with('module.course')
                ->get(),
            'quiz_states' => $latestAttemptsByQuizId->map(fn ($attempt): array => [
                'status' => $attempt->status,
                'final_score' => $attempt->final_score,
                'auto_score' => $attempt->auto_score,
                'essay_score' => $attempt->essay_score,
            ]),
            'attempts' => $attempts,
            'pending_essays' => $attempts->where('status', 'pending_review'),
            'latest_score' => $latestCompletedAttempt?->final_score,
            'wrong_answers' => $this->extractWrongAnswers($attempts),
        ];
    }

    private function extractWrongAnswers($attempts)
    {
        return $attempts
            ->flatMap(function ($attempt) {
                return $attempt->stepAttempts->flatMap(function ($stepAttempt) use ($attempt) {
                    $payload = $stepAttempt->result_payload ?? [];

                    return collect($payload['items'] ?? [])
                        ->filter(fn (array $item): bool => ! ($item['is_correct'] ?? false))
                        ->map(function (array $item) use ($attempt, $stepAttempt): array {
                            return [
                                'quiz_title' => $attempt->quiz->title,
                                'module_title' => $attempt->quiz->module->title,
                                'step_title' => $stepAttempt->quizStep->title,
                                'step_type' => $stepAttempt->quizStep->type,
                                'question_label' => $item['item_label'] ?? $item['row_label'] ?? $item['item_key'] ?? $item['row_id'] ?? '-',
                                'selected_label' => $item['selected_option_label'] ?? $item['selected_column_label'] ?? '-',
                                'correct_label' => $item['correct_option_label'] ?? $item['correct_column_label'] ?? '-',
                                'submitted_at' => $stepAttempt->submitted_at ?? $stepAttempt->updated_at,
                            ];
                        });
                });
            })
            ->sortByDesc('submitted_at')
            ->values()
            ->take(5);
    }
}
