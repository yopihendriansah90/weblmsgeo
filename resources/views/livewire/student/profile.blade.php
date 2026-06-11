<div class="rounded-lg border border-neutral-200 bg-white p-5">
    <h1 class="text-2xl font-semibold">Profil Siswa</h1>
    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
        <div><dt class="text-sm text-neutral-600">Nama</dt><dd class="font-medium">{{ $student->user->name }}</dd></div>
        <div><dt class="text-sm text-neutral-600">Username</dt><dd class="font-medium">{{ $student->user->username }}</dd></div>
        <div><dt class="text-sm text-neutral-600">Sekolah</dt><dd class="font-medium">{{ $student->school->name }}</dd></div>
        <div><dt class="text-sm text-neutral-600">Kelas</dt><dd class="font-medium">{{ $student->class_name }}</dd></div>
        <div><dt class="text-sm text-neutral-600">NISN</dt><dd class="font-medium">{{ $student->nisn ?? '-' }}</dd></div>
        <div><dt class="text-sm text-neutral-600">Status</dt><dd class="font-medium">{{ $student->status }}</dd></div>
    </dl>
</div>
