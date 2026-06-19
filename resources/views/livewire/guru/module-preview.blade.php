<div class="module-preview-page">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <p class="guru-kicker mb-1">Preview Tampilan Siswa</p>
            <h3 class="guru-panel-title mb-1">{{ $module->title }}</h3>
            <p class="text-muted mb-0">Pratinjau ini memakai gaya render materi yang sama dengan halaman pembahasan siswa.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('guru.modules.edit', $module) }}" class="btn btn-sm btn-outline-primary guru-btn-sm guru-btn-bordered">Edit Bab</a>
            <a href="{{ route('guru.modules.index', $module->course) }}" class="btn btn-sm btn-outline-secondary guru-btn-sm guru-btn-bordered">Kembali</a>
        </div>
    </div>

    <section class="student-preview-hero">
        <div class="student-preview-hero-copy">
            <p class="student-preview-kicker">{{ $module->course->title }}</p>
            <h1>{{ $module->title }}</h1>
            @if($module->description)
                <p>{{ $module->description }}</p>
            @endif
        </div>

        <div class="student-preview-badges">
            @if($module->estimated_duration)
                <span>{{ $module->estimated_duration }} menit</span>
            @endif
            <span>{{ $module->status === 'published' ? 'Dipublikasikan' : ucfirst($module->status) }}</span>
        </div>
    </section>

    <section class="student-preview-content">
        <div class="student-preview-content-heading">
            <p class="student-preview-kicker">Bab Pembahasan</p>
            <h2>Isi Materi</h2>
        </div>

        <div class="lesson-content student-preview-lesson-content">
            @if(filled($module->content))
                {!! $module->content !!}
            @else
                <p class="text-muted mb-0">Belum ada isi bab untuk ditampilkan.</p>
            @endif
        </div>
    </section>
</div>

@push('styles')
<style>
    .module-preview-page {
        max-width: 1180px;
        margin-inline: auto;
    }

    .student-preview-hero,
    .student-preview-content {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }

    .student-preview-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 1.5rem;
    }

    .student-preview-hero-copy {
        max-width: 48rem;
    }

    .student-preview-kicker {
        margin: 0;
        color: #b64027;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }

    .student-preview-hero h1 {
        margin: 0.25rem 0 0;
        color: #0f172a;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 700;
        letter-spacing: -0.025em;
    }

    .student-preview-hero-copy > p:not(.student-preview-kicker) {
        margin: 0.75rem 0 0;
        color: #475569;
        font-size: 1rem;
        line-height: 1.6;
    }

    .student-preview-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .student-preview-badges span {
        border-radius: 999px;
        background: #f8ded2;
        color: #b64027;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .student-preview-content {
        padding: 1.25rem;
    }

    .student-preview-content-heading h2 {
        margin: 0.25rem 0 0;
        color: #0f172a;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .student-preview-lesson-content {
        padding-top: 1rem;
        color: #334155;
    }

    @media (max-width: 768px) {
        .student-preview-hero {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    .lesson-content img {
        max-width: 100%;
        height: auto;
        border-radius: 1rem;
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
        border: 1px solid #e2e8f0;
        padding: 0.75rem;
        vertical-align: top;
    }

    .lesson-content th {
        background: #fff5ef;
        color: #7a2b1c;
        font-weight: 700;
    }

    .lesson-content figure.image {
        display: table;
        max-width: 100%;
        margin: 1rem 0;
    }

    .lesson-content figure.image img {
        display: block;
    }

    .lesson-content figure.image figcaption {
        caption-side: bottom;
        color: #64748b;
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
