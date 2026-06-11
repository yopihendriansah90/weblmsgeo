<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Student;

class StudentDashboardService
{
    public function __construct(private readonly LessonProgressService $progressService) {}

    public function summary(Student $student): array
    {
        $attempts = $student->quizAttempts()->with('quiz.lesson.module.course')->latest()->get();
        $completedQuizIds = $attempts->where('status', 'completed')->pluck('quiz_id');

        return [
            'progress_percentage' => $this->progressService->percentage($student),
            'last_lesson' => Lesson::query()
                ->whereHas('lessonProgress', fn ($query) => $query->where('student_id', $student->id))
                ->latest('updated_at')
                ->first(),
            'available_quizzes' => Quiz::where('status', 'published')->whereNotIn('id', $completedQuizIds)->with('lesson')->get(),
            'attempts' => $attempts,
            'pending_essays' => $attempts->where('status', 'pending_review'),
            'latest_score' => $attempts->whereNotNull('final_score')->first()?->final_score,
        ];
    }
}
