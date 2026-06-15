<div>
    <div class="row g-4">
        <div class="col-12 col-xl-5">
            <div class="card guru-card h-100">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                        <div>
                            <p class="guru-kicker mb-1">Penilaian Essay</p>
                            <h3 class="card-title mb-0">Jawaban siswa yang perlu direview</h3>
                            <br><p class="text-muted mb-0 mt-2">Daftar ini otomatis membaca jawaban essay siswa yang statusnya masih menunggu penilaian.</p>
                        </div>
                        <span class="badge rounded-pill text-bg-warning px-3 py-2">{{ $pendingEssayCount }} antrean</span>
                    </div>
                </div>

                <div class="card-body pt-3">
                    @forelse($pendingEssayAttempts as $stepAttempt)
                        @php
                            $student = $stepAttempt->quizAttempt->student;
                            $essayAnswer = $stepAttempt->answers->firstWhere('question_key', 'essay');
                            $essayText = trim((string) data_get($essayAnswer?->answer_payload, 'essay', ''));
                            $isActive = $selectedEssayAttempt?->id === $stepAttempt->id;
                        @endphp
                        <button
                            type="button"
                            wire:click="selectEssayAttempt({{ $stepAttempt->id }})"
                            class="btn w-100 text-start border rounded-4 p-3 mb-3 {{ $isActive ? 'border-primary bg-primary-subtle' : 'border-light-subtle bg-light' }}"
                        >
                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $student->user->name }}</div>
                                    <div class="small text-muted">{{ $student->school->name }}{{ $student->class_name ? ' · '.$student->class_name : '' }}</div>
                                </div>
                                <div class="text-md-end">
                                    <div class="small fw-semibold text-dark">{{ $stepAttempt->quizAttempt->quiz->title }}</div>
                                    <div class="small text-muted">{{ optional($stepAttempt->submitted_at)->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="mt-3 small text-muted">
                                <div class="fw-semibold text-dark">{{ $stepAttempt->quizAttempt->quiz->module->course->title }}</div>
                                <div>{{ $stepAttempt->quizStep->title }}</div>
                                <div class="mt-2 text-body-secondary">{{ \Illuminate\Support\Str::limit($essayText, 140) ?: 'Jawaban essay belum terbaca.' }}</div>
                            </div>
                        </button>
                    @empty
                        <div class="rounded-4 border border-success-subtle bg-success-subtle p-4 text-success-emphasis">
                            Tidak ada essay yang menunggu penilaian. Semua jawaban siswa untuk saat ini sudah sinkron dengan hasil review.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card guru-card h-100">
                <div class="card-header border-0 pb-0">
                    <p class="guru-kicker mb-1">Form Review</p>
                    <h3 class="card-title mb-0">Detail jawaban essay siswa</h3>
                </div>

                <div class="card-body pt-3">
                    @if($selectedEssayAttempt)
                        @php
                            $essayAnswer = $selectedEssayAttempt->answers->firstWhere('question_key', 'essay');
                            $essayText = trim((string) data_get($essayAnswer?->answer_payload, 'essay', ''));
                            $student = $selectedEssayAttempt->quizAttempt->student;
                        @endphp

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="rounded-4 border bg-light p-3 h-100">
                                    <div class="small text-uppercase text-muted fw-semibold">Siswa</div>
                                    <div class="fs-5 fw-semibold text-dark mt-2">{{ $student->user->name }}</div>
                                    <div class="text-muted small mt-1">{{ $student->school->name }}{{ $student->class_name ? ' · '.$student->class_name : '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-4 border bg-light p-3 h-100">
                                    <div class="small text-uppercase text-muted fw-semibold">Quiz</div>
                                    <div class="fs-5 fw-semibold text-dark mt-2">{{ $selectedEssayAttempt->quizAttempt->quiz->title }}</div>
                                    <div class="text-muted small mt-1">{{ $selectedEssayAttempt->quizAttempt->quiz->module->course->title }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-4 border bg-light p-4 mb-4">
                            <div class="small text-uppercase text-muted fw-semibold">Pertanyaan</div>
                            <div class="mt-2 fs-6 text-dark">{{ data_get($selectedEssayAttempt->quizStep->content_payload, 'question', 'Pertanyaan essay belum tersedia.') }}</div>

                            <div class="small text-uppercase text-muted fw-semibold mt-4">Jawaban siswa</div>
                            <div class="mt-2 text-dark" style="white-space: pre-line;">{{ $essayText ?: 'Jawaban essay belum tersedia.' }}</div>
                        </div>

                        <form wire:submit.prevent="submitEssayReview" class="row g-3">
                            <div class="col-md-4">
                                <label for="review-score" class="form-label fw-semibold">Nilai</label>
                                <input id="review-score" type="number" min="0" max="100" step="0.01" wire:model.defer="reviewScore" class="form-control @error('reviewScore') is-invalid @enderror" placeholder="0 - 100">
                                @error('reviewScore') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label for="review-feedback" class="form-label fw-semibold">Feedback</label>
                                <textarea id="review-feedback" rows="5" wire:model.defer="reviewFeedback" class="form-control @error('reviewFeedback') is-invalid @enderror" placeholder="Tulis catatan singkat untuk siswa..." style="resize: vertical;"></textarea>
                                @error('reviewFeedback') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    Simpan Penilaian
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="rounded-4 border border-dashed bg-light p-4 text-muted">
                            Belum ada jawaban essay yang siap dinilai untuk sekolah yang kamu ampu.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
