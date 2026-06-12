<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">{{ $lesson->title }}</h4>
                <small class="text-muted">{{ $lesson->module->course->title }} · {{ $lesson->module->title }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.lessons.edit', $lesson) }}" class="btn btn-info">Edit</a>
                <a href="{{ route('guru.modules.index', $lesson->module->course) }}" class="btn btn-light">Kembali</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex gap-2">
                    <span class="badge bg-{{ $lesson->status === 'published' ? 'success' : ($lesson->status === 'draft' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($lesson->status) }}
                    </span>
                    @if($lesson->published_at)
                        <span class="badge bg-info">Terbit {{ $lesson->published_at->format('d M Y H:i') }}</span>
                    @endif
                    @if($lesson->is_required)
                        <span class="badge bg-dark">Wajib</span>
                    @endif
                </div>

                @if($lesson->summary)
                    <p class="lead">{{ $lesson->summary }}</p>
                @endif

                <article class="lesson-content">
                    {!! $lesson->content !!}
                </article>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .lesson-content img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1rem 0;
    }

    .lesson-content table {
        width: 100%;
    }
</style>
@endpush
