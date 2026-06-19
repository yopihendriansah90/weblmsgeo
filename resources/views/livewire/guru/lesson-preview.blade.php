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

    .lesson-content > * + * {
        margin-top: 1rem;
    }

    .lesson-content p {
        line-height: 1.75;
        margin: 0.85rem 0;
    }

    .lesson-content h1,
    .lesson-content h2,
    .lesson-content h3 {
        color: #7a2b1c;
        font-weight: 700;
        line-height: 1.25;
        margin: 1.5rem 0 0.75rem;
    }

    .lesson-content h1 {
        font-size: clamp(2rem, 4vw, 2.75rem);
    }

    .lesson-content h2 {
        font-size: clamp(1.5rem, 3vw, 2rem);
    }

    .lesson-content h3 {
        font-size: clamp(1.25rem, 2vw, 1.5rem);
    }

    .lesson-content a {
        color: #b64027;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 0.18em;
    }

    .lesson-content blockquote {
        border-left: 4px solid #db8b73;
        color: #475569;
        font-style: italic;
        margin: 1.25rem 0;
        padding: 0.75rem 1rem;
        background: #fff7f1;
        border-radius: 0 0.75rem 0.75rem 0;
    }

    .lesson-content code {
        background: #fff5ef;
        border: 1px solid #f3d8cb;
        border-radius: 0.4rem;
        color: #7a2b1c;
        font-size: 0.9em;
        padding: 0.1rem 0.35rem;
    }

    .lesson-content pre {
        background: #1f2937;
        border-radius: 1rem;
        color: #f8fafc;
        line-height: 1.7;
        margin: 1.25rem 0;
        overflow-x: auto;
        padding: 1rem;
    }

    .lesson-content pre code {
        background: transparent;
        border: 0;
        color: inherit;
        padding: 0;
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
        display: block;
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
