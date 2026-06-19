<div class="space-y-6">
    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Materi</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">{{ $course->title }}</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">{{ $course->description }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-[#f8ded2] px-4 py-2 text-sm font-medium text-[#b64027]">{{ $course->modules->where('type', 'lesson')->count() }} bab pembahasan</span>
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">Quiz di item terakhir</span>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-12">
        <section class="xl:col-span-8">
            <div class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Daftar Bab</p>
                        <h2 class="mt-1 text-2xl font-semibold text-slate-900">Susunan pembelajaran</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $course->modules->count() }} item</span>
                </div>

                <div class="mt-5 space-y-3">
                    @foreach($courseItems as $item)
                        @php($module = $item['module'])
                        @php($isQuiz = $item['is_quiz'])
                        <div class="group rounded-[20px] border p-4 transition {{ $item['is_locked'] ? 'border-slate-200 bg-slate-100/80' : 'border-slate-200 bg-slate-50 hover:border-[#db8b73] hover:bg-white' }}">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="flex gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $item['is_locked'] ? 'bg-slate-200 text-slate-500' : ($isQuiz ? 'bg-[#f8ded2] text-[#a4472f]' : 'bg-[#f8ded2] text-[#b64027]') }}">
                                        <span class="material-symbols-outlined">{{ $item['is_locked'] ? 'lock' : ($isQuiz ? 'quiz' : 'menu_book') }}</span>
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">
                                                {{ $item['label'] }}
                                            </p>
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['is_locked'] ? 'bg-slate-200 text-slate-600' : ($isQuiz ? 'bg-[#f8ded2] text-[#a4472f]' : 'bg-[#f8ded2] text-[#b64027]') }}">
                                                {{ $item['badge'] }}
                                            </span>
                                            @if($item['is_locked'])
                                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Terkunci</span>
                                            @elseif($item['is_completed'])
                                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Selesai</span>
                                            @endif
                                        </div>
                                        <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ $isQuiz ? ($module->title ?: 'Quiz Akhir Materi') : $module->title }}</h3>
                                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                            {{ $isQuiz
                                                ? ($item['is_locked']
                                                    ? 'Quiz akan terbuka setelah semua bab pembahasan pada learning path ini selesai dipelajari.'
                                                    : ($module->description ?: 'Item khusus untuk quiz materi.'))
                                                : (\Illuminate\Support\Str::limit(trim(strip_tags($module->description ?: $module->content)), 140)) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if($isQuiz)
                                        @if($item['is_locked'])
                                            <span class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-500">
                                                <span class="material-symbols-outlined text-[18px]">lock</span>
                                                Quiz Terkunci
                                            </span>
                                        @elseif($module->publishedQuiz)
                                            <a class="inline-flex items-center justify-center rounded-full bg-[#c84a2f] px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25]" href="{{ route('student.quizzes.take', $module->publishedQuiz) }}">
                                                Buka Quiz
                                            </a>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-500">
                                                Quiz belum tersedia
                                            </span>
                                        @endif
                                    @else
                                        @if($item['href'])
                                            <a class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027]" href="{{ $item['href'] }}">
                                                Buka Materi
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <aside class="space-y-6 xl:col-span-4">
            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Ringkasan</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Informasi materi</h2>

                <div class="mt-5 space-y-3">
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Bab pembahasan</p>
                        <p class="mt-2 text-3xl font-semibold text-[#b64027]">{{ $course->modules->where('type', 'lesson')->count() }}</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Item quiz</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $course->modules->where('type', 'quiz')->count() }}</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status course</p>
                        <p class="mt-2 text-xl font-semibold text-slate-900">{{ ucfirst($course->status) }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[24px] border border-slate-200/80 bg-[#c84a2f] p-5 text-white shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#fff1e7]/80">Catatan</p>
                <h2 class="mt-1 text-2xl font-semibold">Quiz berada di item terakhir</h2>
                <p class="mt-3 text-sm leading-6 text-[#fff7ed]/90">
                    Struktur materi mengikuti urutan bab pembahasan, lalu diakhiri dengan item quiz agar siswa memahami alur belajar secara utuh.
                </p>
            </section>
        </aside>
    </div>
</div>
