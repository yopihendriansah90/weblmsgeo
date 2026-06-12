<div>
    <div class="row">
        <div class="col-12">
            <div class="guru-card">
                <div class="guru-card-header">
                    <div class="guru-toolbar justify-content-end">
                        <a href="{{ route('guru.courses.create') }}" class="btn btn-primary guru-btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Materi
                        </a>
                    </div>
                </div>

                <div class="guru-card-body">
                    <div class="row g-3 mt-1">
                        @forelse($courses as $course)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 border-0 shadow-sm course-card course-card-compact">
                                    <div class="course-cover position-relative">
                                        @if($course->coverUrl())
                                            <img
                                                src="{{ $course->coverUrl() }}"
                                                alt="{{ $course->title }}"
                                                class="w-100 h-100 object-fit-cover course-cover-image"
                                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');"
                                            >
                                        @endif

                                        <div class="course-cover-placeholder {{ $course->coverUrl() ? 'd-none' : '' }} d-flex flex-column justify-content-between h-100 p-3 text-white">
                                            <div class="small text-uppercase opacity-75">Materi</div>
                                            <div class="mt-auto">
                                                <div class="fs-4 fw-bold mb-1">B</div>
                                                <div class="fw-semibold small">{{ \Illuminate\Support\Str::limit($course->title, 34) }}</div>
                                            </div>
                                        </div>

                                        <span class="badge rounded-pill position-absolute top-0 end-0 m-2 small {{ $course->status === 'published' ? 'bg-success' : ($course->status === 'draft' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </div>

                                    <div class="card-body py-2 px-2 d-flex flex-column">
                                        <h6 class="card-title mb-2 course-title">{{ $course->title }}</h6>

                                        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: .78rem;">
                                            <span><i class="bi bi-folder2-open me-1"></i>{{ $course->lessons_count ?? 0 }} bab</span>
                                            <span>{{ $course->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-white border-0 pt-0 px-2 pb-2">
                                        <div class="btn-group w-100 btn-group-sm" role="group">
                                            <a href="{{ route('guru.courses.edit', $course) }}" class="btn btn-outline-primary btn-sm guru-btn-soft-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('guru.modules.index', $course) }}" class="btn btn-outline-secondary btn-sm guru-btn-soft-secondary">
                                                <i class="bi bi-layers"></i>
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger btn-sm guru-btn-soft-danger"
                                                wire:click="requestDeleteCourse({{ $course->id }})"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-light border text-center mb-0">
                                    Belum ada data materi.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="guru-card-footer clearfix">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .course-card {
            border-radius: .9rem;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .course-card-compact .course-cover {
            aspect-ratio: 1 / 1;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(15, 23, 42, 0.12) !important;
        }

        .course-cover {
            aspect-ratio: 3 / 4;
            background: linear-gradient(135deg, #1d4ed8 0%, #0f172a 100%);
            overflow: hidden;
        }

        .course-cover img {
            object-fit: cover;
        }

        .course-cover-image {
            position: relative;
            z-index: 1;
        }

        .course-cover-placeholder {
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 30%),
                linear-gradient(135deg, #1d4ed8 0%, #0f172a 100%);
            position: absolute;
            inset: 0;
        }

        .course-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.5rem;
        }
    </style>
@endpush
