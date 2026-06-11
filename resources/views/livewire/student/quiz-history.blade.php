<div class="rounded-lg border border-neutral-200 bg-white p-5">
    <h1 class="text-2xl font-semibold">Riwayat Kuis</h1>
    <div class="mt-4 divide-y divide-neutral-100">
        @forelse($attempts as $attempt)
            <div class="grid gap-2 py-3 md:grid-cols-4">
                <div class="md:col-span-2">
                    <p class="font-medium">{{ $attempt->quiz->title }}</p>
                    <p class="text-sm text-neutral-600">{{ $attempt->quiz->lesson->title }}</p>
                </div>
                <p class="text-sm">Status: {{ $attempt->status }}</p>
                <p class="text-sm">Nilai: {{ $attempt->final_score ?? 'Pending' }}</p>
            </div>
        @empty
            <p class="text-neutral-600">Belum ada riwayat kuis.</p>
        @endforelse
    </div>
</div>
