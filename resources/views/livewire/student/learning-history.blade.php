<div class="space-y-6">
    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Riwayat</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Riwayat Pembelajaran</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
            Daftar materi yang sudah dibuka, lengkap dengan status terakhir dan waktu aksesnya.
        </p>
    </section>

    <section class="overflow-hidden rounded-[24px] border border-slate-200/80 bg-white shadow-sm">
        <div class="divide-y divide-slate-200/80">
            @forelse($progressItems as $item)
                <div class="flex flex-col gap-3 p-5 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-lg font-semibold text-slate-900">{{ $item->module->title }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $item->module->course->title }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="rounded-full bg-indigo-100 px-3 py-1 font-medium text-indigo-700">{{ ucfirst($item->status) }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                            {{ optional($item->last_opened_at)->format('d M Y H:i') ?? '-' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-5 text-sm text-slate-600">Belum ada riwayat belajar.</div>
            @endforelse
        </div>
    </section>
</div>
