<div class="space-y-6">
    <section class="relative overflow-hidden rounded-[24px] border border-[#7a2b1c]/10 bg-gradient-to-br from-[#b64027] via-[#d35a3b] to-[#7a2b1c] p-5 text-white shadow-[0_18px_50px_rgba(195,72,45,0.18)] sm:rounded-[28px] sm:p-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.22),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(255,208,189,0.18),transparent_34%)]"></div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#fff1e7]/80">Dashboard Siswa</p>
                <h1 class="mt-3 text-2xl font-semibold tracking-tight sm:text-5xl">Selamat datang, {{ $student->user->name }}</h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-[#fff7ed]/90 sm:text-base">
                    {{ $student->school->name }} · {{ $student->class_name }}. Lanjutkan materi, kerjakan kuis, dan pantau bagian yang masih perlu diperbaiki dari satu tempat.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-2 sm:gap-3">
                <div class="rounded-xl border border-white/10 bg-white/10 px-3 py-3 backdrop-blur sm:rounded-2xl sm:px-4 sm:py-4">
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#fff1e7]/80 sm:text-[11px] sm:tracking-[0.24em]">Progres</p>
                    <p class="mt-1 text-2xl font-semibold sm:text-3xl">{{ $summary['progress_percentage'] }}%</p>
                </div>
                <div class="rounded-xl border border-white/10 bg-white/10 px-3 py-3 backdrop-blur sm:rounded-2xl sm:px-4 sm:py-4">
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#fff1e7]/80 sm:text-[11px] sm:tracking-[0.24em]">Nilai</p>
                    <p class="mt-1 text-2xl font-semibold sm:text-3xl">{{ $summary['latest_score'] ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-white/10 bg-white/10 px-3 py-3 backdrop-blur sm:rounded-2xl sm:px-4 sm:py-4">
                    <p class="text-[9px] font-semibold uppercase tracking-[0.18em] text-[#fff1e7]/80 sm:text-[11px] sm:tracking-[0.24em]">Kuis</p>
                    <p class="mt-1 text-2xl font-semibold sm:text-3xl">{{ $summary['available_quizzes']->count() }}</p>
                </div>
            </div>
        </div>

        <div class="relative mt-5 h-2 rounded-full bg-white/15 sm:mt-6 sm:h-3">
            <div class="h-2 rounded-full bg-[#ffd0bd] shadow-[0_0_24px_rgba(255,208,189,0.5)] sm:h-3" style="width: {{ $summary['progress_percentage'] }}%"></div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-8">
            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                @if($summary['last_module'])
                    <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Lanjut Belajar</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">Materi terakhir dibuka</h2>
                        </div>
                        <a class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027] hover:shadow-sm" href="{{ route('student.courses') }}">Semua Materi</a>
                    </div>

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
                                <a class="inline-flex items-center justify-center rounded-full bg-[#c84a2f] px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25]" href="{{ route('student.modules.show', $summary['last_module']) }}">
                                    Buka Materi
                                </a>
                                @if($summary['last_module_quiz'])
                                    <a class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027]" href="{{ route('student.quizzes.take', $summary['last_module_quiz']) }}">
                                        Buka Kuis
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-[22px] border border-dashed border-[#efc2b2] bg-[#fff8f4] p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Mulai Belajar</p>
                        <h2 class="mt-2 text-2xl font-semibold text-slate-900">Mulai materi pertamamu</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Kamu belum membuka materi apa pun. Pilih materi untuk mulai membaca bab pembahasan dan membuka quiz akhir materi.
                        </p>
                        <a class="mt-5 inline-flex rounded-full bg-[#c84a2f] px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25]" href="{{ route('student.courses') }}">Pilih Materi</a>
                    </div>
                @endif
            </section>

            @if($summary['recent_materials']->isNotEmpty())
                <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Materi Terbaru</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">Akses cepat ke materi</h2>
                        </div>
                        <a class="text-sm font-medium text-[#b64027] transition hover:text-[#8f321f]" href="{{ route('student.courses') }}">Lihat semua</a>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @foreach($summary['recent_materials'] as $progress)
                            <a href="{{ route('student.modules.show', $progress->module) }}" class="group rounded-[20px] border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-[#db8b73] hover:bg-white hover:shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500">{{ $progress->module->course->title }}</p>
                                        <h3 class="mt-2 text-lg font-semibold text-slate-900 group-hover:text-[#b64027]">{{ $progress->module->title }}</h3>
                                    </div>
                                    <span class="rounded-full bg-[#f8ded2] px-3 py-1 text-xs font-semibold text-[#b64027]">{{ ucfirst($progress->status) }}</span>
                                </div>
                                <p class="mt-3 text-sm text-slate-600">
                                    Dibuka {{ optional($progress->last_opened_at)->format('d M Y H:i') ?? '-' }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Kartu kuis tersedia disembunyikan sementara sesuai arahan produk. --}}
        </div>

        <div class="space-y-6 xl:col-span-4">
            @if($summary['wrong_answers']->isNotEmpty())
                <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Analisis</p>
                    <h2 class="mt-1 text-2xl font-semibold text-slate-900">Jawaban salah terakhir</h2>

                    <div class="mt-5 space-y-3">
                        @foreach($summary['wrong_answers'] as $wrong)
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
                        @endforeach
                    </div>

                    <a class="mt-4 inline-flex text-sm font-medium text-[#b64027] transition hover:text-[#8f321f]" href="{{ route('student.quiz-history') }}">Lihat riwayat kuis</a>
                </section>
            @endif

            <section class="hidden rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm xl:block">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Ringkasan</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Statistik singkat</h2>

                <div class="mt-5 grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Progres</p>
                        <p class="mt-2 text-3xl font-semibold text-[#b64027]">{{ $summary['progress_percentage'] }}%</p>
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
