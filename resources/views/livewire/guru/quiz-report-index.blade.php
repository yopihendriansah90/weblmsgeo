<div>
    <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary shadow-sm">
                    <i class="bi bi-journal-check"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Attempt</span>
                    <span class="info-box-number">{{ $summary['total_attempts'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-success shadow-sm">
                    <i class="bi bi-check-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Selesai Dinilai</span>
                    <span class="info-box-number">{{ $summary['completed_attempts'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning shadow-sm">
                    <i class="bi bi-hourglass-split"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Menunggu Review</span>
                    <span class="info-box-number">{{ $summary['pending_attempts'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger shadow-sm">
                    <i class="bi bi-graph-up"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Rata-rata Nilai</span>
                    <span class="info-box-number">{{ $summary['average_score'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card guru-card mt-4">
        <div class="card-header border-0 pb-0">
            <p class="guru-kicker mb-1">Filter</p>
            <h3 class="card-title mb-0">Laporan hasil quiz siswa</h3>
        </div>

        <div class="card-body pt-3">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="report-school" class="form-label fw-semibold">Sekolah</label>
                    <select id="report-school" wire:model.live="schoolId" class="form-select">
                        <option value="">Semua sekolah yang diampu</option>
                        @foreach($assignedSchools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="report-status" class="form-label fw-semibold">Status</label>
                    <select id="report-status" wire:model.live="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="completed">Selesai</option>
                        <option value="pending_review">Menunggu penilaian</option>
                        <option value="in_progress">Sedang dikerjakan</option>
                    </select>
                </div>

                <div class="col-12 col-md-5">
                    <label for="report-search" class="form-label fw-semibold">Cari siswa / quiz / materi</label>
                    <input id="report-search" type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Mis. nama siswa atau judul quiz">
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary btn-sm">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="card guru-card mt-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Sekolah</th>
                            <th class="px-4 py-3">Materi / Quiz</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Nilai</th>
                            <th class="px-4 py-3">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                            @php
                                $statusLabel = match ($attempt->status) {
                                    'completed' => 'Selesai',
                                    'pending_review' => 'Menunggu penilaian',
                                    'in_progress' => 'Sedang dikerjakan',
                                    default => 'Diproses',
                                };

                                $statusClass = match ($attempt->status) {
                                    'completed' => 'text-bg-success',
                                    'pending_review' => 'text-bg-warning',
                                    default => 'text-bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark">{{ $attempt->student->user->name }}</div>
                                    <div class="small text-muted">{{ $attempt->student->user->username }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $attempt->student->school->name }}</td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark">{{ $attempt->quiz->title }}</div>
                                    <div class="small text-muted">{{ $attempt->quiz->module->course->title }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark">{{ $attempt->final_score ?? '-' }}</div>
                                    <div class="small text-muted">Auto: {{ $attempt->auto_score ?? '-' }} | Essay: {{ $attempt->essay_score ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>{{ optional($attempt->started_at)->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                    <div class="small text-muted">Selesai: {{ optional($attempt->completed_at)->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-5 text-center text-muted">
                                    Belum ada hasil quiz siswa yang cocok dengan filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($attempts->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>
</div>
