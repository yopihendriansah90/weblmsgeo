<div class="module-manager">
    <section class="module-hero card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row gap-3 align-items-lg-start justify-content-between">
                <div>
                    <p class="text-uppercase text-muted small fw-semibold mb-2">Kelola Bab Materi</p>
                    <h2 class="h1 mb-2">{{ $course->title }}</h2>
                    <p class="text-muted mb-0">Susun bab dan subbab materi dalam urutan belajar yang jelas untuk guru dan siswa.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary" wire:click="expandAll">Expand All</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="collapseAll">Collapse All</button>
                    <a href="{{ route('guru.courses.index') }}" class="btn btn-light">Kembali ke Materi</a>
                    <a href="{{ route('guru.modules.create', $course) }}" class="btn btn-primary">+ Tambah Bab</a>
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    @forelse($modules as $module)
        @php($isExpanded = in_array($module->id, $expandedModules, true))
        <section class="card shadow-sm border-0 mb-4 module-card">
            <div class="card-body p-0">
                <div class="module-summary p-4">
                    <div class="d-flex flex-column flex-xl-row gap-3 justify-content-between">
                        <div class="d-flex gap-3 align-items-start flex-grow-1">
                            <button
                                type="button"
                                class="btn btn-sm module-toggle {{ $isExpanded ? 'btn-dark' : 'btn-outline-secondary' }}"
                                wire:click="toggleModule({{ $module->id }})"
                                aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
                            >
                                {{ $isExpanded ? '−' : '+' }}
                            </button>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                    <span class="module-index">{{ $loop->iteration }}</span>
                                    <h3 class="h3 mb-0">{{ $module->title }}</h3>
                                    <span class="badge bg-{{ $module->status === 'published' ? 'success' : ($module->status === 'draft' ? 'warning text-dark' : 'secondary') }}">
                                        {{ ucfirst($module->status) }}
                                    </span>
                                </div>
                                <p class="text-muted mb-3">{{ $module->description ?: 'Belum ada deskripsi bab.' }}</p>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span><strong>{{ $module->lessons_count }}</strong> subbab</span>
                                    <span>Urutan bab mengikuti struktur materi siswa</span>
                                </div>
                            </div>
                        </div>

                        <div class="module-actions d-flex flex-wrap gap-2 justify-content-xl-end align-items-start">
                            <a href="{{ route('guru.lessons.create', $module) }}" class="btn btn-sm btn-primary">+ Subbab</a>
                            <a href="{{ route('guru.modules.edit', $module) }}" class="btn btn-sm btn-info text-white">Edit Bab</a>
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteModule({{ $module->id }})">Hapus</button>
                        </div>
                    </div>
                </div>

                @if($isExpanded)
                    <div class="module-detail border-top">
                        <div class="px-4 pt-4 pb-3 d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
                            <div>
                                <h4 class="h4 mb-1">Daftar Subbab</h4>
                                <p class="text-muted mb-0">{{ $module->title }}</p>
                            </div>
                            <span class="badge rounded-pill text-bg-light px-3 py-2">{{ $module->lessons_count }} subbab</span>
                        </div>

                        @if($module->lessons->isEmpty())
                            <div class="px-4 pb-4">
                                <div class="empty-state rounded-4 border border-dashed p-4 text-center text-muted">
                                    Belum ada subbab pada bab ini.
                                </div>
                            </div>
                        @else
                            <div class="px-4 pb-4">
                                <div class="sublesson-table-wrap rounded-4 border overflow-hidden">
                                    <table class="table align-middle mb-0 sublesson-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 64px;">#</th>
                                                <th>Judul Subbab</th>
                                                <th style="width: 120px;">Status</th>
                                                <th style="width: 100px;">Urutan</th>
                                                <th style="width: 330px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($module->lessons as $lesson)
                                                <tr>
                                                    <td class="text-muted fw-semibold">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="fw-semibold fs-5">{{ $lesson->title }}</div>
                                                        <div class="text-muted mt-1">{{ $lesson->summary ?: 'Belum ada ringkasan subbab.' }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $lesson->status === 'published' ? 'success' : ($lesson->status === 'draft' ? 'warning text-dark' : 'secondary') }}">
                                                            {{ ucfirst($lesson->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-semibold">{{ $lesson->sort_order }}</td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <a href="{{ route('guru.lessons.edit', $lesson) }}" class="btn btn-sm btn-info text-white">Edit</a>
                                                            <a href="{{ route('guru.lessons.preview', $lesson) }}" class="btn btn-sm btn-outline-secondary">Preview</a>
                                                            <button type="button" class="btn btn-sm btn-warning" wire:click="toggleLessonStatus({{ $lesson->id }})">
                                                                {{ $lesson->status === 'published' ? 'Jadikan Draft' : 'Publish' }}
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteLesson({{ $lesson->id }})">Hapus</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @empty
        <section class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                Belum ada bab pada materi ini.
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
    }

    .module-summary {
        background: var(--module-surface);
    }

    .module-toggle {
        min-width: 38px;
        min-height: 38px;
        border-radius: 12px;
        font-size: 1rem;
        line-height: 1;
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

    .module-detail {
        background: var(--module-soft);
    }

    .module-actions .btn {
        padding-inline: 0.9rem;
        padding-block: 0.45rem;
        line-height: 1.2;
        align-self: flex-start;
    }

    .sublesson-table-wrap {
        background: #ffffff;
        border-color: var(--module-border) !important;
    }

    .sublesson-table thead th {
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        border-bottom: 1px solid var(--module-border);
    }

    .sublesson-table tbody td {
        border-color: var(--module-border);
        vertical-align: middle;
    }

    .empty-state {
        background: #ffffff;
        border-color: var(--module-border) !important;
    }
</style>
@endpush
