<div class="rounded-lg border border-neutral-200 bg-white p-5">
    <h1 class="text-2xl font-semibold">Riwayat Pembelajaran</h1>
    <div class="mt-4 divide-y divide-neutral-100">
        @forelse($progressItems as $item)
            <div class="py-3">
                <p class="font-medium">{{ $item->lesson->title }}</p>
                <p class="text-sm text-neutral-600">{{ $item->lesson->module->course->title }} · {{ $item->status }} · {{ optional($item->last_opened_at)->format('d M Y H:i') }}</p>
            </div>
        @empty
            <p class="text-neutral-600">Belum ada riwayat belajar.</p>
        @endforelse
    </div>
</div>
