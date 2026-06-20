<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
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

    public function test_final_quiz_redirects_when_opened_directly_before_lessons_are_completed(): void
    {
        $student = $this->student();
        [$course, $firstLesson, $secondLesson, $quizModule, $quiz] = $this->courseWithTwoLessonsAndQuiz();

        LessonProgress::create([
            'student_id' => $student->id,
            'module_id' => $firstLesson->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->actingAs($student->user)
            ->get(route('student.quizzes.take', $quiz))
            ->assertRedirect(route('student.courses.show', $course));

        $this->assertSame(0, $quiz->attempts()->where('student_id', $student->id)->count());
    }

    public function test_completed_quiz_without_retake_does_not_show_open_button(): void
    {
        $student = $this->student();
        [$course, $firstLesson, $secondLesson, $quizModule, $quiz] = $this->courseWithTwoLessonsAndQuiz([
            'allow_retake' => false,
            'max_attempts' => 1,
        ]);

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

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
            'final_score' => 80,
        ]);

        $this->actingAs($student->user)
            ->get(route('student.courses.show', $course))
            ->assertOk()
            ->assertSee('Quiz Selesai')
            ->assertDontSee('Buka Quiz');
    }

    public function test_completed_quiz_without_retake_redirects_when_opened_directly(): void
    {
        $student = $this->student();
        [$course, $firstLesson, $secondLesson, $quizModule, $quiz] = $this->courseWithTwoLessonsAndQuiz([
            'allow_retake' => false,
            'max_attempts' => 1,
        ]);

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

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'status' => 'completed',
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
            'final_score' => 80,
        ]);

        $this->actingAs($student->user)
            ->get(route('student.quizzes.take', $quiz))
            ->assertRedirect(route('student.courses.show', $course));

        $this->assertSame(1, $quiz->attempts()->where('student_id', $student->id)->count());
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

    private function courseWithTwoLessonsAndQuiz(array $quizAttributes = []): array
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
            ...$quizAttributes,
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
