<?php

namespace App\Services;

use App\Models\Module;
use App\Models\LessonProgress;
use App\Models\Student;
use App\Models\StudentLearningActivity;

class LessonProgressService
{
    public function open(Student $student, Module $module): LessonProgress
    {
        $progress = LessonProgress::firstOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            ['status' => 'not_started'],
        );

        if ($progress->status === 'not_started') {
            $progress->status = 'in_progress';
        }

        $progress->last_opened_at = now();
        $progress->save();

        StudentLearningActivity::create([
            'student_id' => $student->id,
            'module_id' => $module->id,
            'activity_type' => 'module_opened',
            'metadata' => ['module_title' => $module->title],
            'occurred_at' => now(),
        ]);

        return $progress;
    }

    public function complete(Student $student, Module $module): LessonProgress
    {
        $progress = $this->open($student, $module);
        $progress->update(['status' => 'completed', 'completed_at' => now()]);

        StudentLearningActivity::create([
            'student_id' => $student->id,
            'module_id' => $module->id,
            'activity_type' => 'module_completed',
            'metadata' => ['module_title' => $module->title],
            'occurred_at' => now(),
        ]);

        return $progress;
    }

    public function percentage(Student $student): int
    {
        $total = Module::where('status', 'published')->count();

        if ($total === 0) {
            return 0;
        }

        $completed = LessonProgress::where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();

        return (int) round(($completed / $total) * 100);
    }
}
