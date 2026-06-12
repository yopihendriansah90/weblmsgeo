<div class="space-y-6">
    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Materi</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Daftar Materi</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">
                    Pilih materi yang ingin dipelajari. Setiap materi berisi bab pembahasan dan item kuis di bagian akhir.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700">{{ $courses->count() }} materi aktif</span>
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">Quiz di akhir materi</span>
            </div>
        </div>
    </section>

    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($courses as $course)
            <a href="{{ route('student.courses.show', $course) }}" class="group overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-[0_10px_30px_rgba(79,70,229,0.10)]">
                <div class="relative h-52 overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-500 to-slate-900 text-white">
                    @if($course->coverUrl())
                        <img
                            src="{{ $course->coverUrl() }}"
                            alt="{{ $course->title }}"
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/35 to-transparent"></div>
                    @else
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.16),transparent_30%),linear-gradient(135deg,#4f46e5_0%,#0f172a_100%)]"></div>
                    @endif
                    <div class="relative flex h-full flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white/90">Materi</span>
                            <span class="rounded-full bg-cyan-300 px-3 py-1 text-xs font-semibold text-slate-900">{{ $course->lessons_count }} bab</span>
                        </div>
                        <div class="mt-auto max-w-[80%]">
                            <p class="text-sm text-indigo-100/90">{{ $course->title }}</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight">
                                {{ \Illuminate\Support\Str::limit($course->description ?: $course->title, 42) }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 p-5">
                    <p class="text-sm leading-6 text-slate-600">
                        {{ \Illuminate\Support\Str::limit($course->description ?: 'Belum ada deskripsi materi.', 110) }}
                    </p>
                    <div class="flex items-center justify-between text-sm text-slate-600">
                        <span>{{ $course->lessons_count }} bab pembahasan</span>
                        <span class="font-medium text-indigo-700">1 kuis akhir</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">Buka materi</span>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-700 transition group-hover:bg-indigo-600 group-hover:text-white">
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-[24px] border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-600">
                Belum ada kursus published.
            </div>
        @endforelse
    </section>
</div>
