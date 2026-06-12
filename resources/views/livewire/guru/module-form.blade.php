<div class="row">
    <div class="col-12">
        <div class="card guru-panel">
            <div class="card-header bg-white">
                <div class="d-flex flex-column gap-1">
                    <p class="guru-kicker">Kelola Bab</p>
                    <h3 class="guru-panel-title">{{ $title }}</h3>
                </div>
            </div>
            <form wire:submit.prevent="save">
                <div class="card-body guru-panel-body">
                    <div class="mb-3">
                        <label class="form-label">Materi</label>
                        <input type="text" class="form-control" value="{{ $course?->title }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Judul Bab</label>
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
                        <textarea wire:model="description" rows="4" class="form-control @error('description') is-invalid @enderror"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Bab</label>
                        <p class="text-muted small mb-2">Tulis seluruh materi bab di sini. Heading di dalam editor bisa dipakai sebagai penanda bagian materi.</p>
                        <div wire:ignore>
                            <textarea
                                id="module-content-editor"
                                data-tinymce
                                data-sync-target="module-content-sync"
                                class="form-control @error('content') is-invalid @enderror"
                                rows="16"
                            >{{ $content }}</textarea>
                        </div>
                        <input type="hidden" wire:model="content" id="module-content-sync">
                        @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" wire:model="sort_order" class="form-control @error('sort_order') is-invalid @enderror">
                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estimasi Menit</label>
                            <input type="number" wire:model="estimated_duration" class="form-control @error('estimated_duration') is-invalid @enderror">
                            @error('estimated_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="draft">Draf</option>
                                <option value="published">Dipublikasikan</option>
                                <option value="archived">Diarsipkan</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ $course ? route('guru.modules.index', $course) : route('guru.courses.index') }}" class="btn btn-sm btn-outline-secondary guru-btn-sm guru-btn-bordered">Kembali</a>
                    <button type="submit" class="btn btn-sm btn-primary guru-btn-sm ms-auto">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
