<?php

namespace App\Livewire\Guru;

use App\Models\School;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.guru')]
class QuizReportIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'school')]
    public string $schoolId = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSchoolId(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->schoolId = '';
        $this->status = '';
        $this->search = '';
        $this->resetPage();
    }

    public function render(ReportService $reportService)
    {
        $assignedSchools = $this->assignedSchools();
        $assignedSchoolIds = $assignedSchools->pluck('id');

        $query = $reportService->scoreQuery([
            'school_id' => $this->schoolId !== '' ? (int) $this->schoolId : null,
        ])
            ->whereHas('student', fn (Builder $studentQuery) => $studentQuery->whereIn('school_id', $assignedSchoolIds))
            ->when($this->status !== '', fn (Builder $query) => $query->where('status', $this->status))
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $nestedQuery) use ($search) {
                    $nestedQuery
                        ->whereHas('student.user', fn (Builder $userQuery) => $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%"))
                        ->orWhereHas('quiz', fn (Builder $quizQuery) => $quizQuery->where('title', 'like', "%{$search}%"))
                        ->orWhereHas('quiz.module.course', fn (Builder $courseQuery) => $courseQuery->where('title', 'like', "%{$search}%"));
                });
            });

        $summaryQuery = (clone $query);
        $attempts = $query->latest('started_at')->paginate(12);

        $summary = [
            'total_attempts' => (clone $summaryQuery)->count(),
            'completed_attempts' => (clone $summaryQuery)->where('status', 'completed')->count(),
            'pending_attempts' => (clone $summaryQuery)->where('status', 'pending_review')->count(),
            'average_score' => round((float) ((clone $summaryQuery)->whereNotNull('final_score')->avg('final_score') ?? 0), 2),
        ];

        return view('livewire.guru.quiz-report-index', [
            'title' => 'Laporan Hasil Quiz',
            'attempts' => $attempts,
            'assignedSchools' => $assignedSchools,
            'summary' => $summary,
        ]);
    }

    private function assignedSchools(): Collection
    {
        $teacher = auth()->user()->teacher;

        abort_unless($teacher, 403, 'Profil guru tidak ditemukan.');

        return School::query()
            ->whereIn('id', $teacher->activeAssignments()->pluck('school_id'))
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
