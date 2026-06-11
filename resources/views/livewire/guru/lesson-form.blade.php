<div class="row">
    <div class="col-md-11">
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
                        <label class="form-label">Bab</label>
                        <input type="text" class="form-control" value="{{ $module?->title }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Judul Subbab</label>
                        <input type="text" wire:model.live="title" class="form-control @error('title') is-invalid @enderror">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" wire:model="slug" class="form-control @error('slug') is-invalid @enderror">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ringkasan</label>
                        <textarea wire:model="summary" rows="3" class="form-control @error('summary') is-invalid @enderror"></textarea>
                        @error('summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cover Subbab</label>
                            <input type="file" wire:model="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                            @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if($lesson?->getFirstMediaUrl('lesson_covers'))
                                <div class="mt-2">
                                    <img src="{{ $lesson->getFirstMediaUrl('lesson_covers') }}" alt="Cover" class="img-fluid rounded border" style="max-height: 180px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lampiran</label>
                            <input type="file" wire:model="attachments" class="form-control @error('attachments') is-invalid @enderror" multiple>
                            @error('attachments') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @error('attachments.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            <div class="mt-2 small text-muted">Bisa upload lebih dari satu file.</div>
                        </div>
                    </div>

                    @if($lesson?->getMedia('lesson_attachments')->count())
                        <div class="mb-3">
                            <label class="form-label">Lampiran Saat Ini</label>
                            <div class="list-group">
                                @foreach($lesson->getMedia('lesson_attachments') as $media)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ $media->getUrl() }}" target="_blank">{{ $media->name ?: $media->file_name }}</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteMedia({{ $media->id }})">Hapus</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Isi Materi</label>
                        <div wire:ignore>
                            <textarea
                                id="lesson-content-editor"
                                data-tinymce
                                data-sync-target="lesson-content-sync"
                                class="form-control @error('content') is-invalid @enderror"
                                rows="12"
                            >{{ $content }}</textarea>
                        </div>
                        <input type="hidden" wire:model="content" id="lesson-content-sync">
                        @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estimasi Menit</label>
                            <input type="number" wire:model="estimated_duration" class="form-control @error('estimated_duration') is-invalid @enderror">
                            @error('estimated_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" wire:model="sort_order" class="form-control @error('sort_order') is-invalid @enderror">
                            @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" wire:model="is_required" id="is_required">
                        <label class="form-check-label" for="is_required">Wajib dikerjakan</label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ $module ? route('guru.modules.index', $module->course) : route('guru.courses.index') }}" class="btn btn-light">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
