<div class="row">
    <div class="col-12">
        <div class="card guru-panel">
            <div class="card-header bg-white">
                <div class="d-flex flex-column gap-1">
                    <p class="guru-kicker">Quiz Materi</p>
                    <h3 class="guru-panel-title">{{ $title }}</h3>
                    <p class="text-muted mb-0">Quiz ini berada di akhir materi dan terhubung dengan item quiz khusus di daftar materi.</p>
                </div>
            </div>

            <form wire:submit.prevent="save">
                <div class="card-body guru-panel-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Materi</label>
                            <input type="text" class="form-control" value="{{ $module->course->title }}" disabled>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Item</label>
                            <input type="text" class="form-control" value="{{ $module->title }}" disabled>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Judul Quiz</label>
                        <input type="text" wire:model.live="title" class="form-control @error('title') is-invalid @enderror">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea wire:model="description" rows="3" class="form-control @error('description') is-invalid @enderror"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mode</label>
                            <select wire:model="mode" class="form-select @error('mode') is-invalid @enderror">
                                <option value="practice">Latihan</option>
                                <option value="final">Final</option>
                            </select>
                            @error('mode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Boleh Ulang</label>
                            <select wire:model="allow_retake" class="form-select @error('allow_retake') is-invalid @enderror">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                            @error('allow_retake') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Maksimal Percobaan</label>
                            <input type="number" min="1" wire:model="max_attempts" class="form-control @error('max_attempts') is-invalid @enderror">
                            @error('max_attempts') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="draft">Draf</option>
                            <option value="published">Dipublikasikan</option>
                            <option value="archived">Diarsipkan</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <ul class="nav nav-tabs quiz-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button wire:click.prevent="setActiveTab('essay')" class="nav-link {{ $activeTab === 'essay' ? 'active' : '' }}" type="button" role="tab">Essay</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button wire:click.prevent="setActiveTab('text_matching')" class="nav-link {{ $activeTab === 'text_matching' ? 'active' : '' }}" type="button" role="tab">Penjodohan Teks</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button wire:click.prevent="setActiveTab('table_checklist')" class="nav-link {{ $activeTab === 'table_checklist' ? 'active' : '' }}" type="button" role="tab">Table Checklist</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button wire:click.prevent="setActiveTab('image_text_matching')" class="nav-link {{ $activeTab === 'image_text_matching' ? 'active' : '' }}" type="button" role="tab">Gambar-Teks</button>
                        </li>
                    </ul>

                    <div class="tab-content border border-top-0 rounded-bottom p-3 p-md-4 bg-white">
                        <div class="tab-pane fade {{ $activeTab === 'essay' ? 'show active' : '' }}" id="quiz-tab-essay" role="tabpanel">
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label">Judul Step</label>
                                    <input type="text" wire:model="stepForms.essay.title" class="form-control @error('stepForms.essay.title') is-invalid @enderror">
                                    @error('stepForms.essay.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Instruksi</label>
                                    <textarea wire:model="stepForms.essay.instruction" rows="2" class="form-control @error('stepForms.essay.instruction') is-invalid @enderror"></textarea>
                                    @error('stepForms.essay.instruction') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Pertanyaan Essay</label>
                                    <textarea wire:model="stepForms.essay.question" rows="4" class="form-control @error('stepForms.essay.question') is-invalid @enderror"></textarea>
                                    @error('stepForms.essay.question') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab === 'text_matching' ? 'show active' : '' }}" id="quiz-tab-text" role="tabpanel">
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label">Judul Step</label>
                                    <input type="text" wire:model="stepForms.text_matching.title" class="form-control">
                                </div>
                                <div>
                                    <label class="form-label">Instruksi</label>
                                    <textarea wire:model="stepForms.text_matching.instruction" rows="2" class="form-control"></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Soal dan Jawaban</label>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addRow('text_matching', 'pairs')">+ Baris</button>
                                </div>
                                <div class="row g-3">
                                    @foreach($stepForms['text_matching']['pairs'] as $index => $pair)
                                        <div wire:key="text-matching-pair-{{ $index }}" class="col-lg-6">
                                            <div class="border rounded-3 p-3 h-100">
                                                <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Soal {{ $index + 1 }}</span>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeRow('text_matching', 'pairs', {{ $index }})">Hapus</button>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label small">Label Soal</label>
                                                        <input type="text" wire:model="stepForms.text_matching.pairs.{{ $index }}.question_label" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label small">Label Jawaban</label>
                                                        <input type="text" wire:model="stepForms.text_matching.pairs.{{ $index }}.answer_label" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab === 'table_checklist' ? 'show active' : '' }}" id="quiz-tab-table" role="tabpanel">
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label">Judul Step</label>
                                    <input type="text" wire:model="stepForms.table_checklist.title" class="form-control">
                                </div>
                                <div>
                                    <label class="form-label">Instruksi</label>
                                    <textarea wire:model="stepForms.table_checklist.instruction" rows="2" class="form-control"></textarea>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addRow('table_checklist', 'columns')">+ Kolom</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addRow('table_checklist', 'rows')">+ Pernyataan</button>
                                </div>

                                <div class="table-responsive border rounded-3">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 72px;">No.</th>
                                                <th style="min-width: 260px;">Pernyataan</th>
                                                @foreach($stepForms['table_checklist']['columns'] as $columnIndex => $column)
                                                    <th class="text-center" style="min-width: 160px;">
                                                        <div class="d-flex flex-column gap-2">
                                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                                <span class="fw-semibold">Kolom {{ $this->displayAlphabetKey($columnIndex) }}</span>
                                                                @if(count($stepForms['table_checklist']['columns']) > 1)
                                                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeRow('table_checklist', 'columns', {{ $columnIndex }})">Hapus</button>
                                                                @endif
                                                            </div>
                                                            <input type="text" wire:model="stepForms.table_checklist.columns.{{ $columnIndex }}.label" class="form-control form-control-sm" placeholder="Label kolom">
                                                        </div>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stepForms['table_checklist']['rows'] as $index => $row)
                                                <tr wire:key="table-checklist-row-{{ $index }}">
                                                    <td class="text-slate-500">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-2">
                                                            <input type="text" wire:model="stepForms.table_checklist.rows.{{ $index }}.label" class="form-control form-control-sm" placeholder="Tulis pernyataan">
                                                            <div class="text-end">
                                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeRow('table_checklist', 'rows', {{ $index }})">Hapus</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @foreach($stepForms['table_checklist']['columns'] as $columnIndex => $column)
                                                        <td class="text-center">
                                                            <input
                                                                type="radio"
                                                                wire:model="stepForms.table_checklist.rows.{{ $index }}.correct_column_id"
                                                                value="{{ $this->displayAlphabetKey($columnIndex) }}"
                                                                class="h-5 w-5 border-slate-300 text-indigo-600 focus:ring-indigo-200"
                                                            >
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{ $activeTab === 'image_text_matching' ? 'show active' : '' }}" id="quiz-tab-image" role="tabpanel">
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label">Judul Step</label>
                                    <input type="text" wire:model="stepForms.image_text_matching.title" class="form-control">
                                </div>
                                <div>
                                    <label class="form-label">Instruksi</label>
                                    <textarea wire:model="stepForms.image_text_matching.instruction" rows="2" class="form-control"></textarea>
                                </div>

                                <div class="row g-3">
                                    <div class="col-lg-5">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Pilihan Teks</label>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addRow('image_text_matching', 'options')">+ Baris</button>
                                        </div>
                                        <div class="d-grid gap-2">
                                            @foreach($stepForms['image_text_matching']['options'] as $index => $option)
                                                <div wire:key="image-matching-option-{{ $index }}" class="border rounded-3 p-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Pilihan {{ $this->displayAlphabetKey($index) }}</span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeRow('image_text_matching', 'options', {{ $index }})">Hapus</button>
                                                    </div>
                                                    <label class="form-label small">Label Opsi</label>
                                                    <input type="text" wire:model="stepForms.image_text_matching.options.{{ $index }}.label" class="form-control form-control-sm">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Item Gambar</label>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="addRow('image_text_matching', 'items')">+ Baris</button>
                                        </div>
                                        <div class="d-grid gap-2">
                                            @foreach($stepForms['image_text_matching']['items'] as $index => $item)
                                                <div wire:key="image-matching-item-{{ $index }}" class="border rounded-3 p-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Gambar {{ $index + 1 }}</span>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeRow('image_text_matching', 'items', {{ $index }})">Hapus</button>
                                                    </div>
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <label class="form-label small">Label Item</label>
                                                            <input type="text" wire:model="stepForms.image_text_matching.items.{{ $index }}.label" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small">Jawaban Benar</label>
                                                            <select wire:model="stepForms.image_text_matching.items.{{ $index }}.correct_option_key" class="form-select form-select-sm">
                                                                <option value="">Pilih jawaban</option>
                                                                @foreach($stepForms['image_text_matching']['options'] as $optionIndex => $option)
                                                                    <option value="{{ $this->displayAlphabetKey($optionIndex) }}">
                                                                        {{ $this->displayAlphabetKey($optionIndex) }} - {{ $option['label'] ?: 'Pilihan '.($optionIndex + 1) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small">URL Gambar</label>
                                                            <input type="text" wire:model="stepForms.image_text_matching.items.{{ $index }}.image_url" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small">Alt</label>
                                                            <input type="text" wire:model="stepForms.image_text_matching.items.{{ $index }}.alt" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('guru.modules.index', $module->course) }}" class="btn btn-sm btn-outline-secondary guru-btn-sm guru-btn-bordered">Kembali</a>
                    <button type="submit" class="btn btn-sm btn-primary guru-btn-sm ms-auto">Simpan Quiz</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .quiz-tabs .nav-link {
        color: #475569;
    }

    .quiz-tabs .nav-link.active {
        font-weight: 600;
    }
</style>
@endpush
