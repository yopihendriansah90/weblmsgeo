<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\Student;

class QuizAccessService
{
    public function canOpen(Quiz $quiz, Student $student): bool
    {
        return $this->hasCompletedCourseLessons($quiz, $student)
            && $quiz->canStudentStartAttempt($student->id);
    }

    public function hasCompletedCourseLessons(Quiz $quiz, Student $student): bool
    {
        $course = $quiz->module?->course;

        if (! $course) {
            $quiz->loadMissing('module.course');
            $course = $quiz->module?->course;
        }

        if (! $course) {
            return false;
        }

        $lessonModuleIds = $course->modules()
            ->where('type', 'lesson')
            ->where('status', 'published')
            ->pluck('id');

        if ($lessonModuleIds->isEmpty()) {
            return true;
        }

        $completedCount = LessonProgress::query()
            ->where('student_id', $student->id)
            ->whereIn('module_id', $lessonModuleIds)
            ->where('status', 'completed')
            ->count();

        return $completedCount === $lessonModuleIds->count();
    }
}
