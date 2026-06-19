<div class="space-y-6">
    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Materi</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Daftar Materi</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">
                    Pilih materi yang ingin dipelajari. Setiap materi berisi bab pembahasan dan item kuis di bagian akhir.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-[#f8ded2] px-4 py-2 text-sm font-medium text-[#b64027]">{{ $courses->count() }} materi aktif</span>
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">Quiz di akhir materi</span>
            </div>
        </div>
    </section>

    <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($courses as $course)
            <a href="{{ route('student.courses.show', $course) }}" class="group overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-[#db8b73] hover:shadow-[0_10px_30px_rgba(195,72,45,0.10)]">
                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-[#c84a2f] via-[#d96043] to-[#7a2b1c] text-white sm:h-52">
                    @if($course->coverUrl())
                        <img
                            src="{{ $course->coverUrl() }}"
                            alt="{{ $course->title }}"
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#3d150d]/35 via-transparent to-transparent"></div>
                    @else
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.16),transparent_30%),linear-gradient(135deg,#c84a2f_0%,#7a2b1c_100%)]"></div>
                    @endif
                    <div class="absolute left-4 top-4">
                        <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#b64027] shadow-sm">Materi</span>
                    </div>
                </div>

                <div class="space-y-4 p-5">
                    <div>
                        <h2 class="line-clamp-2 text-xl font-semibold leading-tight text-slate-900 group-hover:text-[#b64027] sm:text-2xl">
                            {{ $course->title }}
                        </h2>
                    </div>

                    <p class="text-sm leading-6 text-slate-600">
                        {{ \Illuminate\Support\Str::limit($course->description ?: 'Belum ada deskripsi materi.', 110) }}
                    </p>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <span class="rounded-full bg-[#f8ded2] px-3 py-1 font-medium text-[#b64027]">{{ $course->lessons_count }} bab</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 font-medium text-slate-600">1 kuis akhir</span>
                    </div>
                    <div class="flex items-center justify-between rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 transition group-hover:border-[#db8b73] group-hover:bg-[#fff5ef]">
                        <span class="text-sm font-semibold text-slate-700 group-hover:text-[#b64027]">Buka materi</span>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[#fff5ef] text-[#b64027] transition group-hover:bg-[#c84a2f] group-hover:text-white">
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
