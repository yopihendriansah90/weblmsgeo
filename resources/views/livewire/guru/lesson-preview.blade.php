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

    .lesson-content ol {
        list-style: decimal;
    }

    .lesson-content ul {
        list-style: disc;
    }

    .lesson-content ol,
    .lesson-content ul {
        margin: 1rem 0 1rem 1.5rem;
        padding-left: 1.25rem;
    }

    .lesson-content li {
        margin: 0.35rem 0;
        padding-left: 0.25rem;
        line-height: 1.75;
    }

    .lesson-content table {
        width: 100%;
        margin: 1rem 0;
        border-collapse: collapse;
    }

    .lesson-content th,
    .lesson-content td {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        vertical-align: top;
    }

    .lesson-content th {
        background: #f8f9fa;
        font-weight: 700;
    }

    .lesson-content figure.image {
        display: table;
        max-width: 100%;
        margin: 1rem 0;
    }

    .lesson-content figure.image img {
        display: block;
        margin: 0;
    }

    .lesson-content figure.image figcaption {
        caption-side: bottom;
        color: #6c757d;
        display: table-caption;
        font-size: 0.875rem;
        padding-top: 0.5rem;
        text-align: center;
    }

    .lesson-content img.image-style-align-left,
    .lesson-content figure.image.image-style-align-left {
        float: left;
        margin: 0.5rem 1.25rem 1rem 0;
    }

    .lesson-content img.image-style-align-right,
    .lesson-content figure.image.image-style-align-right {
        float: right;
        margin: 0.5rem 0 1rem 1.25rem;
    }

    .lesson-content img.image-style-align-center,
    .lesson-content figure.image.image-style-align-center,
    .lesson-content figure.image.align-center {
        margin-left: auto;
        margin-right: auto;
    }

    .lesson-content p::after,
    .lesson-content div::after {
        clear: both;
        content: "";
        display: table;
    }
</style>
@endpush
