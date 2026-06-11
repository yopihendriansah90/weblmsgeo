<div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Materi</h3>
                    <div class="card-tools">
                        <a href="{{ route('guru.courses.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i> Tambah Materi
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (session('success'))
                        <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                    @endif
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Judul</th>
                                <th>Status</th>
                                <th>Dibuat Pada</th>
                                <th style="width: 150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    <td>{{ ($courses->currentPage() - 1) * $courses->perPage() + $loop->iteration }}</td>
                                    <td>{{ $course->title }}</td>
                                    <td>
                                        <span class="badge {{ $course->status === 'published' ? 'bg-success' : ($course->status === 'draft' ? 'bg-warning' : 'bg-secondary') }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $course->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('guru.courses.edit', $course) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('guru.modules.index', $course) }}" class="btn btn-secondary btn-sm">
                                            <i class="bi bi-layers"></i>
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            wire:click="deleteCourse({{ $course->id }})"
                                            wire:confirm="Hapus materi ini?"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data materi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
