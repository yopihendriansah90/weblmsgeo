<div class="space-y-6">
    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Profil</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Profil Siswa</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
            Informasi akun dan identitas siswa yang digunakan untuk login dan pelacakan progres belajar.
        </p>
    </section>

    <div class="grid gap-6 xl:grid-cols-12">
        <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm xl:col-span-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex items-center gap-5">
                    <div class="flex h-28 w-28 items-center justify-center rounded-[28px] bg-gradient-to-br from-indigo-600 to-slate-900 text-3xl font-semibold text-white shadow-lg shadow-indigo-600/20">
                        {{ strtoupper(mb_substr($student->user->name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Nama siswa</p>
                        <h2 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">{{ $student->user->name }}</h2>
                        <p class="mt-2 text-sm text-slate-600">Student ID: {{ $student->nisn ?? $student->id }}</p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700" type="button">
                        Edit Profil
                    </button>
                    <button class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700" type="button">
                        Bagikan
                    </button>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sekolah</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $student->school->name }}</p>
                </div>
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kelas</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $student->class_name }}</p>
                </div>
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ ucfirst($student->status) }}</p>
                </div>
            </div>
        </section>

        <aside class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm xl:col-span-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Informasi akun</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-900">Detail profil</h2>

            <dl class="mt-5 space-y-4">
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Username</dt>
                    <dd class="mt-2 text-base font-medium text-slate-900">{{ $student->user->username }}</dd>
                </div>
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">NISN</dt>
                    <dd class="mt-2 text-base font-medium text-slate-900">{{ $student->nisn ?? '-' }}</dd>
                </div>
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Role</dt>
                    <dd class="mt-2 text-base font-medium text-slate-900">{{ $student->user->getRoleNames()->first() ?? 'siswa' }}</dd>
                </div>
            </dl>
        </aside>
    </div>
</div>
