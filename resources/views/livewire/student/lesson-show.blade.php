<article class="space-y-6">
    <div class="rounded-lg border border-neutral-200 bg-white p-5">
        <p class="text-sm text-neutral-600">{{ $lesson->module->course->title }} · {{ $lesson->module->title }}</p>
        <h1 class="mt-1 text-2xl font-semibold">{{ $lesson->title }}</h1>
        <p class="mt-2 text-neutral-700">{{ $lesson->summary }}</p>
    </div>

    @if(session('status'))
        <div class="rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    @if($lesson->getFirstMediaUrl('lesson_covers'))
        <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
            <img src="{{ $lesson->getFirstMediaUrl('lesson_covers') }}" alt="Cover Subbab" class="h-auto w-full object-cover">
        </div>
    @endif

    <div class="prose max-w-none rounded-lg border border-neutral-200 bg-white p-5 lesson-content">
        {!! $lesson->content !!}
    </div>

    @if($lesson->getMedia('lesson_attachments')->count())
        <div class="rounded-lg border border-neutral-200 bg-white p-5">
            <h2 class="text-lg font-semibold">Lampiran</h2>
            <ul class="mt-3 space-y-2">
                @foreach($lesson->getMedia('lesson_attachments') as $media)
                    <li>
                        <a class="text-emerald-700 underline" href="{{ $media->getUrl() }}" target="_blank">
                            {{ $media->name ?: $media->file_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-wrap gap-3">
        <button wire:click="complete" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white">Tandai Selesai</button>
        @if($lesson->publishedQuiz)
            <a class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium" href="{{ route('student.quizzes.take', $lesson->publishedQuiz) }}">Mulai Kuis</a>
        @endif
    </div>
</article>

@push('styles')
<style>
    .lesson-content img {
        max-width: 100%;
        height: auto;
    }

    .lesson-content table {
        width: 100%;
    }
</style>
@endpush
