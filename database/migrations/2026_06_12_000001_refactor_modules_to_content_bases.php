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
            if (! Schema::hasColumn('modules', 'content')) {
                $table->longText('content')->nullable()->after('description');
            }

            if (! Schema::hasColumn('modules', 'estimated_duration')) {
                $table->unsignedInteger('estimated_duration')->nullable()->after('content');
            }

            if (! Schema::hasColumn('modules', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('modules', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('modules', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('quizzes', function (Blueprint $table) {
            if (! Schema::hasColumn('quizzes', 'module_id')) {
                $table->foreignId('module_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_progress', 'module_id')) {
                $table->foreignId('module_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
        });

        $lessonProgressUniqueExists = collect(DB::select("SHOW INDEX FROM lesson_progress WHERE Key_name = 'lesson_progress_student_id_module_id_unique'"))->isNotEmpty();

        if (! $lessonProgressUniqueExists && Schema::hasColumn('lesson_progress', 'student_id') && Schema::hasColumn('lesson_progress', 'module_id')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->unique(['student_id', 'module_id']);
            });
        }

        Schema::table('student_learning_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('student_learning_activities', 'module_id')) {
                $table->foreignId('module_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
        });

        DB::table('modules')->orderBy('id')->get()->each(function ($module): void {
            $lessons = DB::table('lessons')
                ->where('module_id', $module->id)
                ->orderBy('sort_order')
                ->get();

            if ($lessons->isEmpty()) {
                return;
            }

            $contentParts = [];
            $estimatedDuration = 0;
            $publishedAt = null;
            $createdBy = null;
            $updatedBy = null;

            foreach ($lessons as $lesson) {
                if ($lesson->title) {
                    $contentParts[] = '<h2>'.e($lesson->title).'</h2>';
                }

                if ($lesson->summary) {
                    $contentParts[] = '<p><strong>Ringkasan:</strong> '.e($lesson->summary).'</p>';
                }

                if ($lesson->content) {
                    $contentParts[] = $lesson->content;
                }

                $estimatedDuration += (int) ($lesson->estimated_duration ?? 0);
                $publishedAt = $lesson->published_at ?: $publishedAt;
                $createdBy ??= $lesson->created_by;
                $updatedBy ??= $lesson->updated_by;
            }

            DB::table('modules')
                ->where('id', $module->id)
                ->update([
                    'content' => trim(implode("\n", $contentParts)),
                    'estimated_duration' => $estimatedDuration ?: null,
                    'published_at' => $publishedAt,
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                ]);
        });

        if (Schema::hasColumn('quizzes', 'lesson_id')) {
            DB::table('quizzes')
                ->join('lessons', 'quizzes.lesson_id', '=', 'lessons.id')
                ->update(['quizzes.module_id' => DB::raw('lessons.module_id')]);

            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropForeign(['lesson_id']);
                $table->dropColumn('lesson_id');
            });
        }

        if (Schema::hasColumn('lesson_progress', 'lesson_id')) {
            DB::table('lesson_progress')
                ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
                ->update(['lesson_progress.module_id' => DB::raw('lessons.module_id')]);

            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->dropForeign(['lesson_id']);
                $table->dropColumn('lesson_id');
            });
        }

        if (Schema::hasColumn('student_learning_activities', 'lesson_id')) {
            DB::table('student_learning_activities')
                ->join('lessons', 'student_learning_activities.lesson_id', '=', 'lessons.id')
                ->update(['student_learning_activities.module_id' => DB::raw('lessons.module_id')]);

            Schema::table('student_learning_activities', function (Blueprint $table) {
                $table->dropForeign(['lesson_id']);
                $table->dropColumn('lesson_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('student_learning_activities', function (Blueprint $table) {
            if (Schema::hasColumn('student_learning_activities', 'module_id')) {
                $table->dropConstrainedForeignId('module_id');
            }
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_progress', 'module_id')) {
                $table->dropConstrainedForeignId('module_id');
            }
        });

        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'module_id')) {
                $table->dropConstrainedForeignId('module_id');
            }
        });

        Schema::table('modules', function (Blueprint $table) {
            foreach (['created_by', 'updated_by'] as $column) {
                if (Schema::hasColumn('modules', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            $drops = array_values(array_filter([
                Schema::hasColumn('modules', 'content') ? 'content' : null,
                Schema::hasColumn('modules', 'estimated_duration') ? 'estimated_duration' : null,
                Schema::hasColumn('modules', 'published_at') ? 'published_at' : null,
            ]));

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
