<div class="row">
    <div class="col-12">
        <div class="card guru-panel">
            <div class="card-header bg-white">
                <div class="d-flex flex-column gap-1">
                    <p class="guru-kicker">Materi Pembelajaran</p>
                    <h3 class="guru-panel-title">{{ $title }}</h3>
                </div>
            </div>
            <form wire:submit.prevent="save">
                <div class="card-body guru-panel-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" wire:model.live="title" class="form-control @error('title') is-invalid @enderror">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" wire:model="slug" class="form-control @error('slug') is-invalid @enderror">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea wire:model="description" rows="5" class="form-control @error('description') is-invalid @enderror"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="draft">Draf</option>
                            <option value="published">Dipublikasikan</option>
                            <option value="archived">Diarsipkan</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cover Materi</label>
                        <input type="file" wire:model="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                        @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Cover akan tampil di kartu daftar materi.</div>

                        @if($course?->coverUrl())
                            <div class="mt-3">
                                <div class="position-relative rounded border overflow-hidden" style="max-width: 320px; aspect-ratio: 1 / 1;">
                                    <img
                                        src="{{ $course->coverUrl() }}"
                                        alt="Cover Materi"
                                        class="w-100 h-100 object-fit-cover"
                                        onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');"
                                    >
                                    <div class="d-none d-flex align-items-center justify-content-center h-100 text-white" style="background: linear-gradient(135deg, #1d4ed8 0%, #0f172a 100%);">
                                        <div class="text-center p-3">
                                            <div class="fs-3 fw-bold mb-2">B</div>
                                            <div class="small fw-semibold">{{ $course->title }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('guru.courses.index') }}" class="btn btn-sm btn-light guru-btn-sm">Kembali</a>
                    <button type="submit" class="btn btn-sm btn-primary guru-btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
