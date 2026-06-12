<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (! Schema::hasColumn('modules', 'type')) {
                $table->string('type')->default('lesson')->after('course_id');
            }
        });

        if (Schema::hasColumn('modules', 'type')) {
            DB::table('modules')->update(['type' => 'lesson']);
        }

        $courses = DB::table('courses')->orderBy('id')->get();

        foreach ($courses as $course) {
            $quizModule = DB::table('modules')
                ->where('course_id', $course->id)
                ->where('type', 'quiz')
                ->first();

            if (! $quizModule) {
                $maxSortOrder = (int) DB::table('modules')
                    ->where('course_id', $course->id)
                    ->where('type', 'lesson')
                    ->max('sort_order');

                $quizModuleId = DB::table('modules')->insertGetId([
                    'course_id' => $course->id,
                    'type' => 'quiz',
                    'title' => 'Quiz Materi',
                    'slug' => 'quiz-materi',
                    'description' => 'Quiz akhir materi.',
                    'content' => null,
                    'estimated_duration' => null,
                    'sort_order' => $maxSortOrder + 1,
                    'status' => 'published',
                    'published_at' => now(),
                    'created_by' => $course->created_by,
                    'updated_by' => $course->updated_by,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $quizModule = DB::table('modules')->where('id', $quizModuleId)->first();
            }

            $lessonModuleIds = DB::table('modules')
                ->where('course_id', $course->id)
                ->where('type', 'lesson')
                ->pluck('id');

            $quizIds = DB::table('quizzes')
                ->whereIn('module_id', $lessonModuleIds)
                ->pluck('id');

            if ($quizIds->isNotEmpty()) {
                DB::table('quizzes')
                    ->whereIn('id', $quizIds)
                    ->update(['module_id' => $quizModule->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
