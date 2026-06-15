<div class="space-y-6">
    <section class="relative overflow-hidden rounded-[28px] border border-indigo-900/10 bg-gradient-to-br from-indigo-700 via-indigo-600 to-slate-900 p-6 text-white shadow-[0_18px_50px_rgba(79,70,229,0.18)] sm:p-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(14,165,233,0.22),transparent_34%)]"></div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-indigo-100/80">Dashboard Siswa</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-5xl">Selamat datang, {{ $student->user->name }}</h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-indigo-50/90 sm:text-base">
                    {{ $student->school->name }} · {{ $student->class_name }}. Lanjutkan materi, kerjakan kuis, dan pantau bagian yang masih perlu diperbaiki dari satu tempat.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-100/80">Progres belajar</p>
                    <p class="mt-1 text-3xl font-semibold">{{ $summary['progress_percentage'] }}%</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-100/80">Nilai terbaru</p>
                    <p class="mt-1 text-3xl font-semibold">{{ $summary['latest_score'] ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-4 backdrop-blur">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-100/80">Kuis aktif</p>
                    <p class="mt-1 text-3xl font-semibold">{{ $summary['available_quizzes']->count() }}</p>
                </div>
            </div>
        </div>

        <div class="relative mt-6 h-3 rounded-full bg-white/15">
            <div class="h-3 rounded-full bg-cyan-300 shadow-[0_0_24px_rgba(103,232,249,0.5)]" style="width: {{ $summary['progress_percentage'] }}%"></div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-8">
            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Lanjut Belajar</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-900">Materi terakhir dibuka</h2>
                    </div>
                    <a class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700 hover:shadow-sm" href="{{ route('student.courses') }}">Semua Materi</a>
                </div>

                @if($summary['last_module'])
                    <div class="mt-5 rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">{{ $summary['last_module']->course->title }}</p>
                                <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ $summary['last_module']->title }}</h3>
                                @if($summary['last_module']->description)
                                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $summary['last_module']->description }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <a class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700" href="{{ route('student.modules.show', $summary['last_module']) }}">
                                    Buka Materi
                                </a>
                                @if($summary['last_module']->publishedQuiz)
                                    <a class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700" href="{{ route('student.quizzes.take', $summary['last_module']->publishedQuiz) }}">
                                        Buka Kuis
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-5 rounded-[22px] border border-dashed border-slate-300 bg-slate-50 p-5">
                        <p class="text-sm text-slate-600">Belum ada materi yang pernah dibuka.</p>
                        <a class="mt-4 inline-flex rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-600/20" href="{{ route('student.courses') }}">Pilih Materi</a>
                    </div>
                @endif
            </section>

            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Materi Terbaru</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-900">Akses cepat ke materi</h2>
                    </div>
                    <a class="text-sm font-medium text-indigo-700 transition hover:text-indigo-800" href="{{ route('student.courses') }}">Lihat semua</a>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @forelse($summary['recent_materials'] as $progress)
                        <a href="{{ route('student.modules.show', $progress->module) }}" class="group rounded-[20px] border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white hover:shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $progress->module->course->title }}</p>
                                    <h3 class="mt-2 text-lg font-semibold text-slate-900 group-hover:text-indigo-700">{{ $progress->module->title }}</h3>
                                </div>
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($progress->status) }}</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-600">
                                Dibuka {{ optional($progress->last_opened_at)->format('d M Y H:i') ?? '-' }}
                            </p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-600">Belum ada materi yang pernah dibuka.</p>
                    @endforelse
                </div>
            </section>

            {{-- Kartu kuis tersedia disembunyikan sementara sesuai arahan produk. --}}
        </div>

        <div class="space-y-6 xl:col-span-4">
            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Analisis</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Jawaban salah terakhir</h2>

                <div class="mt-5 space-y-3">
                    @forelse($summary['wrong_answers'] as $wrong)
                        <div class="rounded-[18px] border border-red-100 bg-red-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-red-700">{{ $wrong['quiz_title'] }}</p>
                                    <p class="mt-1 text-sm text-slate-700">{{ $wrong['question_label'] }}</p>
                                </div>
                                <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white">Salah</span>
                            </div>
                            <div class="mt-3 space-y-1 text-xs text-slate-600">
                                <p>Benar: <span class="font-medium text-slate-800">{{ $wrong['correct_label'] }}</span></p>
                                <p>Jawabanmu: <span class="font-medium text-slate-800">{{ $wrong['selected_label'] }}</span></p>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-[18px] border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">Belum ada jawaban salah yang tercatat.</p>
                    @endforelse
                </div>

                <a class="mt-4 inline-flex text-sm font-medium text-indigo-700 transition hover:text-indigo-800" href="{{ route('student.quiz-history') }}">Lihat riwayat kuis</a>
            </section>

            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Ringkasan</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Statistik singkat</h2>

                <div class="mt-5 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Progres</p>
                        <p class="mt-2 text-3xl font-semibold text-indigo-700">{{ $summary['progress_percentage'] }}%</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nilai terbaru</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['latest_score'] ?? '-' }}</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kuis aktif</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $summary['available_quizzes']->count() }}</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
