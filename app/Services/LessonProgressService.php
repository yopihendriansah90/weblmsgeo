<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Student;
use App\Models\StudentLearningActivity;

class LessonProgressService
{
    public function open(Student $student, Lesson $lesson): LessonProgress
    {
        $progress = LessonProgress::firstOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lesson->id],
            ['status' => 'not_started'],
        );

        if ($progress->status === 'not_started') {
            $progress->status = 'in_progress';
        }

        $progress->last_opened_at = now();
        $progress->save();

        StudentLearningActivity::create([
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
            'activity_type' => 'lesson_opened',
            'metadata' => ['lesson_title' => $lesson->title],
            'occurred_at' => now(),
        ]);

        return $progress;
    }

    public function complete(Student $student, Lesson $lesson): LessonProgress
    {
        $progress = $this->open($student, $lesson);
        $progress->update(['status' => 'completed', 'completed_at' => now()]);

        StudentLearningActivity::create([
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
            'activity_type' => 'lesson_completed',
            'metadata' => ['lesson_title' => $lesson->title],
            'occurred_at' => now(),
        ]);

        return $progress;
    }

    public function percentage(Student $student): int
    {
        $total = Lesson::where('status', 'published')->count();

        if ($total === 0) {
            return 0;
        }

        $completed = LessonProgress::where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();

        return (int) round(($completed / $total) * 100);
    }
}
