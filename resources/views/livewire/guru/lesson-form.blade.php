<div class="row">
    <div class="col-12">
        <div class="card guru-panel">
            <div class="card-header bg-white">
                <div class="d-flex flex-column gap-1">
                    <p class="guru-kicker">Subbab</p>
                    <h3 class="guru-panel-title">{{ $title }}</h3>
                </div>
            </div>
            <form wire:submit.prevent="save">
                <div class="card-body guru-panel-body">
                    <div class="mb-3">
                        <label class="form-label">Bab</label>
                        <input type="text" class="form-control" value="{{ $module?->title }}" disabled>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <label class="form-label">Judul Subbab</label>
                            <input type="text" wire:model.live="title" class="form-control @error('title') is-invalid @enderror">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label">Slug</label>
                            <input type="text" wire:model="slug" class="form-control @error('slug') is-invalid @enderror">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ringkasan</label>
                            <textarea wire:model="summary" rows="3" class="form-control @error('summary') is-invalid @enderror"></textarea>
                            @error('summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="guru-surface p-3 p-md-4 mt-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                            <div>
                                <h4 class="h6 mb-1">Isi Materi</h4>
                                <p class="text-muted mb-0 small">Tulis konten utama subbab di area ini.</p>
                            </div>
                        </div>
                        <div wire:ignore>
                            <textarea
                                id="lesson-content-editor"
                                data-tinymce
                                data-sync-target="lesson-content-sync"
                                class="form-control @error('content') is-invalid @enderror"
                                rows="16"
                            >{{ $content }}</textarea>
                        </div>
                        <input type="hidden" wire:model="content" id="lesson-content-sync">
                        @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Estimasi Menit</label>
                            <input type="number" wire:model="estimated_duration" class="form-control @error('estimated_duration') is-invalid @enderror">
                            @error('estimated_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Urutan</label>
                            <input type="number" wire:model="sort_order" class="form-control @error('sort_order') is-invalid @enderror">
                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Status</label>
                            <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="draft">Draf</option>
                                <option value="published">Dipublikasikan</option>
                                <option value="archived">Diarsipkan</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input type="checkbox" class="form-check-input" wire:model="is_required" id="is_required">
                        <label class="form-check-label" for="is_required">Wajib dikerjakan</label>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex align-items-center w-100">
                        <a href="{{ $module ? route('guru.modules.index', $module->course) : route('guru.courses.index') }}" class="btn btn-sm btn-outline-secondary guru-btn-sm guru-btn-bordered">Kembali</a>
                        <button type="submit" class="btn btn-sm btn-primary guru-btn-sm ms-auto">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
