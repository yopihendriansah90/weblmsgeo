<div class="space-y-6">
    <section class="rounded-lg border border-neutral-200 bg-white p-5">
        <p class="text-sm text-neutral-600">{{ $student->school->name }} · {{ $student->class_name }}</p>
        <h1 class="mt-1 text-2xl font-semibold">{{ $student->user->name }}</h1>
        <div class="mt-4 h-3 rounded-full bg-neutral-100">
            <div class="h-3 rounded-full bg-emerald-600" style="width: {{ $summary['progress_percentage'] }}%"></div>
        </div>
        <p class="mt-2 text-sm text-neutral-700">Progres belajar {{ $summary['progress_percentage'] }}%</p>
    </section>

    <div class="grid gap-4 lg:grid-cols-3">
        <section class="rounded-lg border border-neutral-200 bg-white p-5 lg:col-span-2">
            <h2 class="text-lg font-semibold">Lanjut Belajar</h2>
            @if($summary['last_lesson'])
                <p class="mt-2 text-neutral-700">{{ $summary['last_lesson']->title }}</p>
                <a class="mt-4 inline-flex rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white" href="{{ route('student.lessons.show', $summary['last_lesson']) }}">Buka Materi</a>
            @else
                <p class="mt-2 text-neutral-700">Belum ada materi yang dibuka.</p>
                <a class="mt-4 inline-flex rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white" href="{{ route('student.courses') }}">Pilih Kursus</a>
            @endif
        </section>

        <section class="rounded-lg border border-neutral-200 bg-white p-5">
            <h2 class="text-lg font-semibold">Nilai Terbaru</h2>
            <p class="mt-3 text-4xl font-semibold">{{ $summary['latest_score'] ?? '-' }}</p>
        </section>
    </div>

    <section class="rounded-lg border border-neutral-200 bg-white p-5">
        <h2 class="text-lg font-semibold">Kuis Tersedia</h2>
        <div class="mt-4 divide-y divide-neutral-100">
            @forelse($summary['available_quizzes'] as $quiz)
                <div class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-medium">{{ $quiz->title }}</p>
                        <p class="text-sm text-neutral-600">{{ $quiz->lesson->title }}</p>
                    </div>
                    <a class="rounded-md border border-neutral-300 px-3 py-2 text-sm" href="{{ route('student.quizzes.take', $quiz) }}">Kerjakan</a>
                </div>
            @empty
                <p class="py-3 text-neutral-600">Tidak ada kuis baru.</p>
            @endforelse
        </div>
    </section>
</div>
