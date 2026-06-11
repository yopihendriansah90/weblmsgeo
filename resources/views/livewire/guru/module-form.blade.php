<div class="row">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $title }}</h3>
            </div>
            <form wire:submit.prevent="save">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" wire:model="sort_order" class="form-control @error('sort_order') is-invalid @enderror">
                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
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
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ $course ? route('guru.modules.index', $course) : route('guru.courses.index') }}" class="btn btn-light">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
