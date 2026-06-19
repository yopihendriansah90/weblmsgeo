<div class="space-y-6">
    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Profil</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Profil Siswa</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
            Perbarui informasi akun yang dipakai untuk masuk, identitas belajar, dan kontak yang diperlukan selama pembelajaran.
        </p>
    </section>

    <div class="grid gap-6 xl:grid-cols-12">
        <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm xl:col-span-4">
            <div class="flex items-center gap-5">
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#c84a2f] to-[#7a2b1c] text-3xl font-semibold text-white shadow-lg shadow-[#c84a2f]/20 ring-4 ring-[#f8ded2] sm:h-28 sm:w-28">
                    {{ strtoupper(mb_substr($student->user->name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Nama siswa</p>
                    <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ $student->user->name }}</h2>
                    <p class="mt-2 text-sm text-slate-600">Student ID: {{ $student->nisn ?? $student->id }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4">
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Sekolah</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $student->school->name }}</p>
                </div>
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kelas</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $student->class_name ?: '-' }}</p>
                </div>
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ ucfirst($student->status) }}</p>
                </div>
                <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Role</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $student->user->getRoleNames()->first() ?? 'siswa' }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm xl:col-span-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Edit Profil</p>
                    <h2 class="mt-1 text-2xl font-semibold text-slate-900">Informasi akun</h2>
                </div>

                @if($saved)
                    <span class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700">Perubahan berhasil disimpan</span>
                @endif
            </div>

            <form wire:submit.prevent="save" class="mt-6 space-y-6">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama lengkap</label>
                        <input id="name" type="text" wire:model.defer="name" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
                        <input id="username" type="text" wire:model.defer="username" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                        @error('username') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" type="email" wire:model.defer="email" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="class_name" class="mb-2 block text-sm font-semibold text-slate-700">Kelas</label>
                        <input id="class_name" type="text" wire:model.defer="class_name" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                        @error('class_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP</label>
                        <input id="phone" type="text" wire:model.defer="phone" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                        @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="gender" class="mb-2 block text-sm font-semibold text-slate-700">Jenis kelamin</label>
                        <select id="gender" wire:model.defer="gender" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        @error('gender') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="birth_date" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal lahir</label>
                        <input id="birth_date" type="date" wire:model.defer="birth_date" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                        @error('birth_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="rounded-[22px] border border-slate-200 bg-slate-50 p-5">
                    <p class="text-sm font-semibold text-slate-900">Ubah password</p>
                    <p class="mt-1 text-sm text-slate-600">Kosongkan bagian ini jika kamu tidak ingin mengganti password.</p>

                    <div class="mt-4 grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password baru</label>
                            <input id="password" type="password" wire:model.defer="password" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                            @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi password baru</label>
                            <input id="password_confirmation" type="password" wire:model.defer="password_confirmation" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#c84a2f] px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25]">
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
