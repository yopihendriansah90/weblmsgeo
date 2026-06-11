<div class="space-y-6">
    <div>
        <p class="text-sm text-neutral-600">Kursus</p>
        <h1 class="text-2xl font-semibold">{{ $course->title }}</h1>
        <p class="mt-2 text-neutral-700">{{ $course->description }}</p>
    </div>

    @foreach($course->modules as $module)
        <section class="rounded-lg border border-neutral-200 bg-white p-5">
            <h2 class="text-lg font-semibold">{{ $module->title }}</h2>
            <div class="mt-4 divide-y divide-neutral-100">
                @foreach($module->lessons as $lesson)
                    <div class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $lesson->title }}</p>
                            <p class="text-sm text-neutral-600">{{ $lesson->summary }}</p>
                        </div>
                        <a class="rounded-md border border-neutral-300 px-3 py-2 text-sm" href="{{ route('student.lessons.show', $lesson) }}">Buka</a>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
