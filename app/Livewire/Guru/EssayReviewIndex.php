<?php

namespace App\Livewire\Guru;

use App\Models\QuizStepAttempt;
use App\Services\EssayReviewService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guru')]
class EssayReviewIndex extends Component
{
    public ?int $selectedEssayAttemptId = null;

    public ?float $reviewScore = null;

    public string $reviewFeedback = '';

    public function mount(): void
    {
        $firstPendingAttempt = $this->pendingEssayAttemptsQuery()
            ->select('quiz_step_attempts.id')
            ->first();

        $this->selectedEssayAttemptId = $firstPendingAttempt?->id;
    }

    public function selectEssayAttempt(int $stepAttemptId): void
    {
        $stepAttempt = $this->pendingEssayAttemptsQuery()->findOrFail($stepAttemptId);

        $this->selectedEssayAttemptId = $stepAttempt->id;
        $this->reviewScore = $stepAttempt->score !== null ? (float) $stepAttempt->score : null;
        $this->reviewFeedback = (string) ($stepAttempt->feedback ?? '');
        $this->resetValidation();
    }

    public function submitEssayReview(EssayReviewService $essayReviewService): void
    {
        $validated = $this->validate([
            'selectedEssayAttemptId' => ['required', 'integer'],
            'reviewScore' => ['required', 'numeric', 'min:0', 'max:100'],
            'reviewFeedback' => ['nullable', 'string', 'max:2000'],
        ], [
            'reviewScore.required' => 'Nilai essay wajib diisi.',
            'reviewScore.numeric' => 'Nilai essay harus berupa angka.',
            'reviewScore.min' => 'Nilai essay minimal 0.',
            'reviewScore.max' => 'Nilai essay maksimal 100.',
        ]);

        $stepAttempt = $this->pendingEssayAttemptsQuery()->findOrFail($validated['selectedEssayAttemptId']);

        $essayReviewService->review(
            $stepAttempt,
            auth()->user(),
            (float) $validated['reviewScore'],
            filled($validated['reviewFeedback']) ? trim($validated['reviewFeedback']) : null,
        );

        $nextPendingAttempt = $this->pendingEssayAttemptsQuery()
            ->select('quiz_step_attempts.id')
            ->first();

        $this->selectedEssayAttemptId = $nextPendingAttempt?->id;
        $this->reviewScore = null;
        $this->reviewFeedback = '';
        $this->resetValidation();

        $this->dispatch('guru-notify', type: 'success', message: 'Penilaian essay berhasil disimpan.');
    }

    public function render()
    {
        $pendingEssayAttempts = $this->pendingEssayAttemptsQuery()
            ->with([
                'quizAttempt.student.user',
                'quizAttempt.student.school',
                'quizAttempt.quiz.module.course',
                'quizStep',
                'answers',
                'essayReview',
            ])
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get();

        if ($this->selectedEssayAttemptId && ! $pendingEssayAttempts->contains('id', $this->selectedEssayAttemptId)) {
            $this->selectedEssayAttemptId = $pendingEssayAttempts->first()?->id;
        }

        $selectedEssayAttempt = $pendingEssayAttempts->firstWhere('id', $this->selectedEssayAttemptId)
            ?? $pendingEssayAttempts->first();

        return view('livewire.guru.essay-review-index', [
            'title' => 'Penilaian Essay',
            'pendingEssayAttempts' => $pendingEssayAttempts,
            'selectedEssayAttempt' => $selectedEssayAttempt,
            'pendingEssayCount' => $pendingEssayAttempts->count(),
        ]);
    }

    private function pendingEssayAttemptsQuery(): Builder
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (! $user->hasRole('super_admin') && ! $teacher) {
            abort(403, 'Profil guru tidak ditemukan.');
        }

        return QuizStepAttempt::query()
            ->where('quiz_step_attempts.status', 'pending_review')
            ->whereHas('quizStep', fn (Builder $query) => $query->where('type', 'essay'))
            ->whereHas('quizAttempt.student', function (Builder $query) use ($user, $teacher) {
                if ($user->hasRole('super_admin') && ! $teacher) {
                    return;
                }

                $query->whereIn('school_id', $teacher->activeAssignments()->pluck('school_id'));
            });
    }
}
