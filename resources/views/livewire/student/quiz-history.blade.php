<div class="space-y-6">
    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Riwayat</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Riwayat Kuis</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
            Rekap kuis yang sudah dikerjakan, nilai akhir, dan status pengerjaan.
        </p>
    </section>

    <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-sm">
        <div class="divide-y divide-slate-200/80">
            @forelse($attempts as $attempt)
                <div class="flex flex-col gap-4 p-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-lg font-semibold text-slate-900">{{ $attempt->quiz->title }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $attempt->quiz->module->course->title }} · {{ $attempt->quiz->module->title }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">Status: {{ ucfirst($attempt->status) }}</span>
                        <span class="rounded-full bg-indigo-100 px-3 py-1 font-medium text-indigo-700">Nilai: {{ $attempt->final_score ?? 'Pending' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-5 text-sm text-slate-600">Belum ada riwayat kuis.</div>
            @endforelse
        </div>
    </section>
</div>
