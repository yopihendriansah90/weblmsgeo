<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $course->title }}</h4>
            <small class="text-muted">Kelola bab dan subbab di dalam materi ini.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.courses.index') }}" class="btn btn-light">Kembali</a>
            <a href="{{ route('guru.modules.create', $course) }}" class="btn btn-primary">+ Tambah Bab</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Judul Bab</th>
                        <th>Subbab</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modules as $module)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $module->title }}</div>
                                <div class="text-muted small">{{ $module->description }}</div>
                            </td>
                            <td>{{ $module->lessons_count }}</td>
                            <td>
                                <span class="badge bg-{{ $module->status === 'published' ? 'success' : ($module->status === 'draft' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($module->status) }}
                                </span>
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('guru.modules.edit', $module) }}" class="btn btn-info btn-sm">Edit</a>
                                <a href="{{ route('guru.lessons.create', $module) }}" class="btn btn-primary btn-sm">+ Subbab</a>
                                <button type="button" class="btn btn-danger btn-sm" wire:click="deleteModule({{ $module->id }})">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="4" class="bg-light">
                                <div class="small text-muted mb-2">Daftar Subbab</div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>Judul Subbab</th>
                                                <th>Status</th>
                                                <th>Urutan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($module->lessons as $lesson)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $lesson->title }}</div>
                                                        <div class="text-muted small">{{ $lesson->summary }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $lesson->status === 'published' ? 'success' : ($lesson->status === 'draft' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst($lesson->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $lesson->sort_order }}</td>
                                                    <td class="d-flex gap-1">
                                                        <a href="{{ route('guru.lessons.edit', $lesson) }}" class="btn btn-info btn-sm">Edit</a>
                                                        <button type="button" class="btn btn-danger btn-sm" wire:click="deleteLesson({{ $lesson->id }})">Hapus</button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Belum ada subbab.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Belum ada bab pada materi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
