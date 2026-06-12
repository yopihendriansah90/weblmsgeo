<div class="module-manager">
    <section class="module-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-between">
                <div>
                    <p class="text-uppercase text-muted small fw-semibold mb-1">Kelola Item Materi</p>
                    <h2 class="h4 mb-2">{{ $course->title }}</h2>
                    <p class="text-muted mb-0 module-hero-desc">Setiap item bisa berupa bab pembahasan atau quiz materi yang tampil pada daftar yang sama.</p>
                    <div class="module-search mt-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Cari item..."
                                wire:model.live.debounce.300ms="search"
                            >
                            @if(trim($search) !== '')
                                <button type="button" class="btn btn-outline-secondary" wire:click="$set('search', '')">
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('guru.courses.index') }}" class="btn btn-sm btn-outline-secondary guru-btn-sm guru-btn-bordered">Kembali</a>
                    <a href="{{ route('guru.modules.create', $course) }}" class="btn btn-sm btn-primary guru-btn-sm">+ Bab Pembahasan</a>
                </div>
            </div>
        </div>
    </section>

    @forelse($modules as $module)
        <section class="card shadow-sm border-0 mb-3 module-card">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between">
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="module-index">{{ $loop->iteration }}</span>
                            <h3 class="h5 mb-0 module-title">{{ $module->isQuiz() ? 'Quiz Materi' : $module->title }}</h3>
                            <span class="badge rounded-pill {{ $module->isQuiz() ? 'bg-dark text-white' : 'bg-'.($module->status === 'published' ? 'success' : ($module->status === 'draft' ? 'warning text-dark' : 'secondary')) }}">
                                {{ $module->isQuiz() ? 'Quiz' : ucfirst($module->status) }}
                            </span>
                        </div>
                        <div class="module-meta d-flex flex-wrap gap-2 mb-3">
                            @if(! $module->isQuiz())
                                <span class="module-meta-chip">Urutan mengikuti struktur belajar</span>
                                @if($module->estimated_duration)
                                    <span class="module-meta-chip"><strong>{{ $module->estimated_duration }}</strong> menit</span>
                                @endif
                            @endif
                        </div>
                        @if($module->isQuiz())
                            <p class="text-muted mb-2 module-summary-text">
                                {{ $module->description ?: 'Item khusus untuk manajemen quiz materi.' }}
                            </p>
                            <div class="text-muted small mb-3">
                                {{ ($module->quizzes_count ?? 0) > 0 ? 'Quiz sudah dibuat dan siap dikelola.' : 'Quiz belum dibuat.' }}
                            </div>
                        @else
                            <p class="text-muted mb-2 module-summary-text">{{ $module->description ?: 'Belum ada deskripsi bab.' }}</p>
                            <div class="text-muted small mb-3">
                                {{ \Illuminate\Support\Str::limit(strip_tags($module->content ?: ''), 180) ?: 'Belum ada isi bab.' }}
                            </div>
                        @endif
                    </div>

                    <div class="module-actions d-flex flex-wrap gap-2 justify-content-lg-end align-items-start">
                        @if($module->isQuiz())
                            @if(($module->quizzes_count ?? 0) > 0)
                                <a href="{{ route('guru.quizzes.edit', ['module' => $module, 'quiz' => $module->quizzes->first()]) }}" class="btn btn-sm btn-outline-success guru-btn-soft-secondary">
                                    Kelola Quiz
                                </a>
                            @else
                                <a href="{{ route('guru.quizzes.create', $module) }}" class="btn btn-sm btn-outline-success guru-btn-soft-secondary">
                                    + Quiz
                                </a>
                            @endif
                        @else
                            <a href="{{ route('guru.modules.edit', $module) }}" class="btn btn-sm btn-outline-primary guru-btn-soft-primary">Edit</a>
                            <button type="button" class="btn btn-sm btn-outline-danger guru-btn-soft-danger" wire:click="requestDeleteModule({{ $module->id }})">Hapus</button>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @empty
        <section class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                @if(trim($search) !== '')
                    Tidak ada bab yang cocok dengan kata kunci "{{ $search }}".
                @else
                    Belum ada bab pada materi ini.
                @endif
            </div>
        </section>
    @endforelse
</div>

@push('styles')
<style>
    .module-manager {
        --module-surface: #ffffff;
        --module-muted: #6c757d;
        --module-border: #dfe4ea;
        --module-soft: #f8f9fb;
    }

    .module-hero {
        background: linear-gradient(135deg, #ffffff 0%, #f5f8fc 100%);
    }

    .module-card {
        background: var(--module-surface);
        border-radius: 1rem;
    }

    .module-index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        background: #edf2f7;
        color: #334155;
        font-weight: 700;
    }

    .module-title {
        line-height: 1.25;
    }

    .module-summary-text {
        max-width: 62rem;
    }

    .module-meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.875rem;
        line-height: 1;
    }

    .module-hero-desc {
        max-width: 42rem;
    }

    .module-search {
        max-width: 32rem;
    }

    .module-actions .btn {
        padding-inline: 0.9rem;
        padding-block: 0.45rem;
        line-height: 1.2;
        align-self: flex-start;
    }
</style>
@endpush
