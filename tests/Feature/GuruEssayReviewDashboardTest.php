<?php

namespace Tests\Feature;

use App\Livewire\Guru\EssayReviewIndex;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSchoolAssignment;
use App\Models\User;
use App\Services\QuizFlowService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruEssayReviewDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guru_can_review_pending_student_essay_from_dedicated_page(): void
    {
        Role::findOrCreate('guru');
        Role::findOrCreate('siswa');

        $school = School::create(['name' => 'Sekolah Binaan', 'level' => 'SMP', 'status' => 'active']);
        $teacherUser = User::create([
            'name' => 'Guru Reviewer',
            'username' => 'guru_reviewer_dashboard',
            'email' => 'guru-reviewer@example.test',
            'password' => 'password',
            'status' => 'active',
        ]);
        $teacherUser->assignRole('guru');

        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'status' => 'active',
        ]);

        TeacherSchoolAssignment::create([
            'teacher_id' => $teacher->id,
            'school_id' => $school->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        $student = $this->student($school, 'siswa_review_dashboard');
        [$quiz, $stepAttempt] = $this->pendingEssayAttempt($student);

        $this->actingAs($teacherUser);

        Livewire::test(EssayReviewIndex::class)
            ->assertSee($student->user->name)
            ->assertSee($quiz->title)
            ->set('reviewScore', 88)
            ->set('reviewFeedback', 'Jawaban sudah tepat dan runtut.')
            ->call('submitEssayReview')
            ->assertDispatched('guru-notify', type: 'success', message: 'Penilaian essay berhasil disimpan.');

        $this->assertDatabaseHas('essay_reviews', [
            'quiz_step_attempt_id' => $stepAttempt->id,
            'reviewed_by' => $teacherUser->id,
            'score' => 88,
            'status' => 'reviewed',
        ]);

        $this->assertDatabaseHas('quiz_step_attempts', [
            'id' => $stepAttempt->id,
            'status' => 'completed',
            'score' => 88,
            'feedback' => 'Jawaban sudah tepat dan runtut.',
        ]);

        $this->assertDatabaseHas('quiz_attempts', [
            'id' => $stepAttempt->quizAttempt->id,
            'status' => 'completed',
            'essay_score' => 88,
            'final_score' => 88,
        ]);
    }

    public function test_guru_essay_review_page_hides_pending_essay_from_other_school(): void
    {
        Role::findOrCreate('guru');
        Role::findOrCreate('siswa');

        $assignedSchool = School::create(['name' => 'Sekolah A', 'level' => 'SMP', 'status' => 'active']);
        $otherSchool = School::create(['name' => 'Sekolah B', 'level' => 'SMP', 'status' => 'active']);

        $teacherUser = User::create([
            'name' => 'Guru Filter',
            'username' => 'guru_filter_dashboard',
            'email' => 'guru-filter@example.test',
            'password' => 'password',
            'status' => 'active',
        ]);
        $teacherUser->assignRole('guru');

        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'status' => 'active']);

        TeacherSchoolAssignment::create([
            'teacher_id' => $teacher->id,
            'school_id' => $assignedSchool->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        $visibleStudent = $this->student($assignedSchool, 'visible_dashboard_alpha', 'Siswa Alpha');
        $hiddenStudent = $this->student($otherSchool, 'hidden_dashboard_beta', 'Siswa Beta');

        [, $visibleAttempt] = $this->pendingEssayAttempt($visibleStudent, 'quiz-visible');
        [, $hiddenAttempt] = $this->pendingEssayAttempt($hiddenStudent, 'quiz-hidden');

        $this->actingAs($teacherUser);

        Livewire::test(EssayReviewIndex::class)
            ->assertSee($visibleStudent->user->name)
            ->assertDontSee($hiddenStudent->user->name)
            ->assertSet('selectedEssayAttemptId', $visibleAttempt->id);
    }

    private function student(School $school, string $username, ?string $name = null): Student
    {
        $user = User::create([
            'name' => $name ?? 'Siswa '.strtoupper(substr($username, -4)),
            'username' => $username,
            'password' => 'password',
            'status' => 'active',
        ]);
        $user->assignRole('siswa');

        return Student::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'class_name' => 'VIII A',
            'status' => 'active',
        ]);
    }

    private function pendingEssayAttempt(Student $student, string $slug = 'quiz-essay-dashboard'): array
    {
        $course = Course::create([
            'title' => 'Materi '.$slug,
            'slug' => 'course-'.$slug,
            'status' => 'published',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'type' => 'quiz',
            'title' => 'Quiz '.$slug,
            'slug' => $slug,
            'status' => 'published',
        ]);

        $quiz = Quiz::create([
            'module_id' => $module->id,
            'title' => 'Quiz '.$slug,
            'status' => 'published',
        ]);

        $step = QuizStep::create([
            'quiz_id' => $quiz->id,
            'title' => 'Essay Utama',
            'type' => 'essay',
            'sort_order' => 1,
            'status' => 'published',
            'content_payload' => ['question' => 'Apa manfaat SIG bagi sekolah?'],
        ]);

        $flowService = app(QuizFlowService::class);
        $attempt = $flowService->startOrContinue($quiz->fresh('steps'), $student);
        $flowService->submitStep($attempt, $step, ['essay' => 'SIG membantu analisis data spasial di lingkungan sekolah.']);

        return [$quiz->fresh('module.course'), $attempt->fresh('stepAttempts')->stepAttempts->first()];
    }
}
