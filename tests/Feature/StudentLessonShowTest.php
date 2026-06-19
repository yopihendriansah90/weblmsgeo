<?php

namespace Tests\Feature;

use App\Livewire\Student\LessonShow;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentLessonShowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_complete_redirects_student_to_next_lesson(): void
    {
        $student = $this->student();
        [$firstLesson, $secondLesson] = $this->courseLessons();

        $this->actingAs($student->user);

        Livewire::test(LessonShow::class, ['module' => $firstLesson])
            ->call('complete')
            ->assertRedirect(route('student.modules.show', $secondLesson));

        $this->assertDatabaseHas('lesson_progress', [
            'student_id' => $student->id,
            'module_id' => $firstLesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_complete_redirects_student_to_quiz_after_last_lesson(): void
    {
        $student = $this->student();
        [$lesson, $quizModule, $quiz] = $this->courseWithFinalQuiz();

        $this->actingAs($student->user);

        Livewire::test(LessonShow::class, ['module' => $lesson])
            ->call('complete')
            ->assertRedirect(route('student.quizzes.take', $quiz));

        $this->assertDatabaseHas('lesson_progress', [
            'student_id' => $student->id,
            'module_id' => $lesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_lesson_page_shows_final_quiz_as_available_when_course_quiz_exists(): void
    {
        $student = $this->student();
        [$lesson] = $this->courseWithFinalQuiz();

        $this->actingAs($student->user)
            ->get(route('student.modules.show', $lesson))
            ->assertOk()
            ->assertSee('Quiz di akhir materi')
            ->assertSee('Tersedia')
            ->assertDontSee('Belum ada');
    }

    public function test_lesson_page_renders_rich_editor_content(): void
    {
        $student = $this->student();
        $course = Course::create([
            'title' => 'Course Rich Content',
            'slug' => 'course-rich-content',
            'status' => 'published',
        ]);

        $lesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Rich Content',
            'slug' => 'bab-rich-content',
            'content' => implode('', [
                '<h1 style="text-align: center;">Judul Materi</h1>',
                '<p><strong>Tebal</strong> <em>miring</em> <u>garis bawah</u> <a href="https://example.test">tautan</a></p>',
                '<blockquote>Kutipan penting</blockquote>',
                '<ol><li>Langkah satu</li><li>Langkah dua</li></ol>',
                '<ul><li>Poin satu</li><li>Poin dua</li></ul>',
                '<table><tbody><tr><th>Kolom</th><td>Isi tabel</td></tr></tbody></table>',
                '<figure class="image image-style-align-center"><img src="/example.png" alt="Contoh"><figcaption>Caption gambar</figcaption></figure>',
                '<pre><code>kode contoh</code></pre>',
            ]),
            'sort_order' => 1,
            'status' => 'published',
        ]);

        $this->actingAs($student->user)
            ->get(route('student.modules.show', $lesson))
            ->assertOk()
            ->assertSee('style="text-align: center;"', false)
            ->assertSee('<u>garis bawah</u>', false)
            ->assertSee('<blockquote>Kutipan penting</blockquote>', false)
            ->assertSee('<ol><li>Langkah satu</li><li>Langkah dua</li></ol>', false)
            ->assertSee('<ul><li>Poin satu</li><li>Poin dua</li></ul>', false)
            ->assertSee('<table><tbody><tr><th>Kolom</th><td>Isi tabel</td></tr></tbody></table>', false)
            ->assertSee('image-style-align-center', false)
            ->assertSee('<figcaption>Caption gambar</figcaption>', false)
            ->assertSee('<pre><code>kode contoh</code></pre>', false);
    }

    private function student(): Student
    {
        Role::findOrCreate('siswa');

        $school = School::create([
            'name' => 'Sekolah Materi',
            'level' => 'SMP',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Siswa Materi',
            'username' => 'siswa_materi_'.Str::lower(Str::random(8)),
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

    private function courseLessons(): array
    {
        $course = Course::create([
            'title' => 'Course Materi',
            'slug' => 'course-materi',
            'status' => 'published',
        ]);

        $firstLesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab 1',
            'slug' => 'bab-1',
            'content' => '<p>Konten bab 1</p>',
            'sort_order' => 1,
            'status' => 'published',
        ]);

        $secondLesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab 2',
            'slug' => 'bab-2',
            'content' => '<p>Konten bab 2</p>',
            'sort_order' => 2,
            'status' => 'published',
        ]);

        return [$firstLesson, $secondLesson];
    }

    private function courseWithFinalQuiz(): array
    {
        $course = Course::create([
            'title' => 'Course Dengan Quiz',
            'slug' => 'course-dengan-quiz',
            'status' => 'published',
        ]);

        $lesson = Module::create([
            'course_id' => $course->id,
            'type' => 'lesson',
            'title' => 'Bab Akhir',
            'slug' => 'bab-akhir',
            'content' => '<p>Konten bab akhir</p>',
            'sort_order' => 1,
            'status' => 'published',
        ]);

        $quizModule = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Quiz Akhir',
            'slug' => 'quiz-akhir',
            'sort_order' => 2,
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
            'content_payload' => ['question' => 'Jelaskan materi ini.'],
        ]);

        return [$lesson, $quizModule, $quiz];
    }
}
