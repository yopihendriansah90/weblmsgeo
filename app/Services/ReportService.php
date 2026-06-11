<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    public function scoreQuery(array $filters = []): Builder
    {
        return QuizAttempt::query()
            ->with(['student.user', 'student.school', 'quiz.lesson.module.course'])
            ->when($filters['school_id'] ?? null, fn ($query, $schoolId) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('school_id', $schoolId)))
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId))
            ->when($filters['quiz_id'] ?? null, fn ($query, $quizId) => $query->where('quiz_id', $quizId));
    }

    public function progressQuery(array $filters = []): Builder
    {
        return LessonProgress::query()
            ->with(['student.user', 'student.school', 'lesson.module.course'])
            ->when($filters['school_id'] ?? null, fn ($query, $schoolId) => $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('school_id', $schoolId)))
            ->when($filters['student_id'] ?? null, fn ($query, $studentId) => $query->where('student_id', $studentId));
    }
}
