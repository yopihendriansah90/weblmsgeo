<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentCourseShowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_final_quiz_stays_locked_until_all_lessons_are_completed(): void
    {
        $student = $this->student();
        [$course] = $this->courseWithTwoLessonsAndQuiz();

        $this->actingAs($student->user)
            ->get(route('student.courses.show', $course))
            ->assertOk()
            ->assertSee('Quiz Terkunci')
            ->assertSee('Quiz akan terbuka setelah semua bab pembahasan pada learning path ini selesai dipelajari.')
            ->assertDontSee('Buka Quiz');
    }

    public function test_final_quiz_unlocks_after_all_lessons_are_completed(): void
    {
        $student = $this->student();
        [$course, $firstLesson, $secondLesson] = $this->courseWithTwoLessonsAndQuiz();

        LessonProgress::create([
            'student_id' => $student->id,
            'module_id' => $firstLesson->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        LessonProgress::create([
            'student_id' => $student->id,
            'module_id' => $secondLesson->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->actingAs($student->user)
            ->get(route('student.courses.show', $course))
            ->assertOk()
            ->assertSee('Buka Quiz')
            ->assertDontSee('Quiz Terkunci');
    }

    private function student(): Student
    {
        Role::findOrCreate('siswa');

        $school = School::create([
            'name' => 'Sekolah Course Show',
            'level' => 'SMP',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Siswa Course Show',
            'username' => 'siswa_course_show_'.Str::lower(Str::random(6)),
            'password' => 'password',
            'status' => 'active',
        ]);
        $user->assignRole('siswa');

        return Student::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'status' => 'active',
        ]);
    }

    private function courseWithTwoLessonsAndQuiz(): array
    {
        $suffix = Str::lower(Str::random(6));

        $course = Course::create([
            'title' => 'Course Path Quiz',
            'slug' => 'course-path-quiz-'.$suffix,
            'status' => 'published',
        ]);

        $firstLesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab 1',
            'slug' => 'course-path-bab-1-'.$suffix,
            'content' => '<p>Konten 1</p>',
            'sort_order' => 1,
            'status' => 'published',
        ]);

        $secondLesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab 2',
            'slug' => 'course-path-bab-2-'.$suffix,
            'content' => '<p>Konten 2</p>',
            'sort_order' => 2,
            'status' => 'published',
        ]);

        $quizModule = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Quiz Materi',
            'slug' => 'course-path-quiz-module-'.$suffix,
            'sort_order' => 3,
            'status' => 'published',
        ]);

        $quiz = Quiz::create([
            'module_id' => $quizModule->id,
            'title' => 'Quiz Akhir Materi',
            'status' => 'published',
        ]);

        QuizStep::create([
            'quiz_id' => $quiz->id,
            'title' => 'Essay',
            'type' => 'essay',
            'sort_order' => 1,
            'status' => 'published',
            'content_payload' => ['question' => 'Apa isi materi ini?'],
        ]);

        return [$course, $firstLesson, $secondLesson, $quizModule, $quiz];
    }
}
