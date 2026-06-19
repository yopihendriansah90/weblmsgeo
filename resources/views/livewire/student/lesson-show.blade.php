<article class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-[20px] border border-slate-200/80 bg-white px-4 py-3 shadow-sm">
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <a href="{{ route('student.courses') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027]">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Daftar Materi
            </a>
            <a href="{{ route('student.courses.show', $module->course) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027]">
                <span class="material-symbols-outlined text-[18px]">library_books</span>
                Kembali ke Materi
            </a>
        </div>
        <span class="rounded-full bg-[#f8ded2] px-3 py-1 text-xs font-semibold text-[#b64027]">Mode baca</span>
    </div>

    @if($nextTimelineItem)
        <div class="flex justify-end">
            <a href="{{ $nextTimelineItem['href'] }}" class="inline-flex items-center gap-2 rounded-full bg-[#c84a2f] px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25]">
                Lanjut berikutnya
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
    @endif

    <section class="rounded-[24px] border border-slate-200/80 bg-white px-4 py-5 shadow-sm sm:px-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Timeline Belajar</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900 sm:text-2xl">Perjalanan pembelajaran materi</h2>
            </div>
            <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 sm:inline-flex">
                {{ $timelineItems->count() }} tahap
            </span>
        </div>

        <div class="mt-6 overflow-x-auto pb-2">
            <div class="timeline-track flex min-w-max items-start justify-between gap-3 px-2">
                @foreach($timelineItems as $item)
                    @php
                        $statusClasses = match ($item['status']) {
                            'completed' => 'border-emerald-200 bg-emerald-200 text-emerald-800',
                            'current' => 'border-[#b64027] bg-[#c84a2f] text-white shadow-[0_0_0_7px_rgba(195,72,45,0.16)]',
                            'available' => 'border-[#efc2b2] bg-[#f8ded2] text-[#b64027]',
                            default => 'border-slate-300 bg-slate-100 text-slate-400',
                        };
                    @endphp

                    <div class="timeline-step relative flex min-w-[150px] flex-1 flex-col items-center text-center">
                        @if($item['href'])
                            <a href="{{ $item['href'] }}" class="flex flex-col items-center transition hover:scale-[1.02]">
                                <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 text-lg {{ $statusClasses }}">
                                    <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                                </span>
                                <span class="mt-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $item['label'] }}</span>
                                <span class="mt-1 text-sm font-semibold {{ $item['is_current'] ? 'text-[#b64027]' : 'text-slate-700' }}">{{ $item['title'] }}</span>
                            </a>
                        @else
                            <div class="flex flex-col items-center">
                                <span class="flex h-14 w-14 items-center justify-center rounded-full border-2 text-lg {{ $statusClasses }}">
                                    <span class="material-symbols-outlined">{{ $item['icon'] }}</span>
                                </span>
                                <span class="mt-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $item['label'] }}</span>
                                <span class="mt-1 text-sm font-semibold text-slate-500">{{ $item['title'] }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">{{ $module->course->title }}</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">{{ $module->title }}</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">{{ $module->description }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if($module->estimated_duration)
                    <span class="rounded-full bg-[#f8ded2] px-4 py-2 text-sm font-medium text-[#b64027]">
                        {{ $module->estimated_duration }} menit
                    </span>
                @endif
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">
                    {{ $hasFinalQuiz ? 'Quiz di akhir materi' : 'Tanpa quiz' }}
                </span>
            </div>
        </div>
    </section>

    @if(session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-12">
        <section class="space-y-6 xl:col-span-8">
            <div class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Bab Pembahasan</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-900">Isi Materi</h2>
                    </div>
                </div>

                <div class="lesson-content prose max-w-none pt-4 text-slate-700">
                    {!! $module->content !!}
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button wire:click="complete" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#c84a2f] px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25] hover:shadow-lg hover:shadow-[#c84a2f]/30">
                    Tandai Selesai
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>
            </div>
        </section>

        <aside class="space-y-6 xl:col-span-4">
            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Ringkasan</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Informasi bab</h2>

                <dl class="mt-5 space-y-4">
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Durasi</dt>
                        <dd class="mt-2 text-xl font-semibold text-slate-900">{{ $module->estimated_duration ? $module->estimated_duration . ' menit' : '-' }}</dd>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Quiz akhir materi</dt>
                        <dd class="mt-2 text-xl font-semibold text-slate-900">
                            {{ $isFinalQuizAvailable ? 'Tersedia' : ($hasFinalQuiz ? 'Belum tersedia' : 'Belum ada') }}
                        </dd>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status bab</dt>
                        <dd class="mt-2 text-xl font-semibold text-slate-900">{{ ucfirst($module->status) }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </div>
</article>

@push('styles')
<style>
    .lesson-content img {
        max-width: 100%;
        height: auto;
        border-radius: 1rem;
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

    .lesson-content h1,
    .lesson-content h2,
    .lesson-content h3 {
        color: #7a2b1c;
    }

    .lesson-content p {
        line-height: 1.75;
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
        margin-left: auto;
        margin-right: auto;
    }

    .lesson-content p::after,
    .lesson-content div::after {
        clear: both;
        content: "";
        display: table;
    }

    .timeline-step:not(:last-child)::after {
        content: "";
        position: absolute;
        top: 1.7rem;
        left: calc(50% + 2rem);
        width: calc(100% - 1rem);
        height: 2px;
        background: linear-gradient(90deg, #cbd5e1 0%, #e2e8f0 100%);
        z-index: 0;
    }

    .timeline-step > * {
        position: relative;
        z-index: 1;
    }

    @media (max-width: 640px) {
        .timeline-step {
            min-width: 132px;
        }

        .timeline-step:not(:last-child)::after {
            width: calc(100% - 0.5rem);
        }
    }
</style>
@endpush
