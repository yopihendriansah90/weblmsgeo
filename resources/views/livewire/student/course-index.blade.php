<div class="space-y-4">
    <h1 class="text-2xl font-semibold">Mata Pelajaran</h1>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse($courses as $course)
            <a href="{{ route('student.courses.show', $course) }}" class="rounded-lg border border-neutral-200 bg-white p-5 hover:border-emerald-600">
                <p class="text-lg font-semibold">{{ $course->title }}</p>
                <p class="mt-2 text-sm text-neutral-600">{{ $course->description }}</p>
                <p class="mt-4 text-sm">{{ $course->modules->count() }} bab</p>
            </a>
        @empty
            <p class="text-neutral-600">Belum ada kursus published.</p>
        @endforelse
    </div>
</div>
