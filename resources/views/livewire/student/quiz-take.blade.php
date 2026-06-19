<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-[20px] border border-slate-200/80 bg-white px-4 py-3 shadow-sm">
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <a href="{{ route('student.courses.show', $quiz->module->course) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027]">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Materi
            </a>
            <a href="{{ route('student.quiz-history') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 transition hover:border-[#db8b73] hover:text-[#b64027]">
                <span class="material-symbols-outlined text-[18px]">history</span>
                Riwayat Kuis
            </a>
        </div>
        <span class="rounded-full bg-[#f8ded2] px-3 py-1 text-xs font-semibold text-[#b64027]">Mode fokus</span>
    </div>

    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">{{ $quiz->module->title }}</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">{{ $quiz->title }}</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">{{ $quiz->description }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @php
                    $attemptStatusLabel = match ($attempt->status) {
                        'in_progress' => 'Sedang berlangsung',
                        'pending_review' => 'Menunggu penilaian',
                        'completed' => 'Selesai',
                        default => 'Dalam proses',
                    };
                @endphp
                <span class="rounded-full bg-[#f8ded2] px-4 py-2 text-sm font-medium text-[#b64027]">{{ $attemptStatusLabel }}</span>
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">{{ $steps->count() }} langkah</span>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-12">
        <aside class="rounded-[24px] border border-slate-200/80 bg-white p-4 shadow-sm xl:col-span-3">
            <p class="px-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Langkah Quiz</p>
            <div class="mt-4 space-y-2">
                @foreach($steps as $step)
                    @php
                        $stepAttempt = $stepAttempts->get($step->id);
                        $isActive = $activeStepId === $step->id;
                        $stepStatusLabel = match ($stepAttempt?->status ?? 'locked') {
                            'active' => 'Sedang dikerjakan',
                            'pending_review' => 'Menunggu penilaian',
                            'auto_graded' => 'Sudah dinilai',
                            'completed' => 'Selesai',
                            default => 'Belum dibuka',
                        };
                    @endphp
                    <button
                        wire:click="setActiveStep({{ $step->id }})"
                        class="flex w-full items-start gap-3 rounded-[18px] border px-3 py-3 text-left transition {{ $isActive ? 'border-[#db8b73] bg-[#fff5ef] shadow-sm' : 'border-slate-200 bg-slate-50 hover:border-[#db8b73] hover:bg-white' }}"
                        @disabled($stepAttempt?->status === 'locked')
                    >
                        <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full {{ $isActive ? 'bg-[#c84a2f] text-white' : 'bg-white text-slate-500' }}">
                            {{ $loop->iteration }}
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-slate-900">{{ $step->title }}</span>
                            <span class="text-xs text-slate-500">{{ $stepStatusLabel }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>

        <section class="space-y-6 xl:col-span-9">
            @if($activeStep)
                @php
                    $payload = $activeStep->content_payload ?? [];
                    $activeAttempt = $stepAttempts->get($activeStep->id);
                @endphp

                <div class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Step Aktif</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $activeStep->title }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $activeStep->instruction }}</p>
                        </div>
                        @php
                            $stepTypeLabel = match ($activeStep->type) {
                                'essay' => 'Esai',
                                'text_matching' => 'Penjodohan Teks',
                                'image_text_matching' => 'Penjodohan Gambar-Teks',
                                'table_checklist' => 'Checklist Tabel',
                                default => ucwords(str_replace('_', ' ', $activeStep->type)),
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $stepTypeLabel }}</span>
                    </div>

                    @if($errorMessage)
                        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errorMessage }}</div>
                    @endif
                </div>

                <div class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                    @if($activeStep->type === 'essay')
                        <p class="text-lg font-semibold text-slate-900">{{ $payload['question'] ?? 'Tuliskan jawaban kamu.' }}</p>
                        <textarea wire:model="answer.essay" class="mt-4 min-h-48 w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]"></textarea>
                        @error('answer.essay') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    @elseif($activeStep->type === 'table_checklist')
                        <div class="overflow-x-auto">
                            <table class="w-full border-separate border-spacing-0 text-sm">
                                <thead>
                                    <tr class="text-left">
                                        <th class="border-b border-slate-200 px-3 py-3 font-semibold text-slate-700">No.</th>
                                        <th class="border-b border-slate-200 px-3 py-3 font-semibold text-slate-700">Pernyataan</th>
                                        @foreach(($payload['columns'] ?? []) as $column)
                                            <th class="border-b border-slate-200 px-3 py-3 text-center font-semibold text-slate-700">{{ $column['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($payload['rows'] ?? []) as $row)
                                        <tr class="align-top">
                                            <td class="border-b border-slate-100 px-3 py-3 font-medium text-slate-500">{{ $loop->iteration }}</td>
                                            <td class="border-b border-slate-100 px-3 py-3 font-medium text-slate-900">{{ $row['label'] }}</td>
                                            @foreach(($payload['columns'] ?? []) as $column)
                                                <td class="border-b border-slate-100 px-3 py-3 text-center">
                                                    <input
                                                        type="radio"
                                                        wire:model="answer.rows.{{ $row['id'] }}"
                                                        value="{{ $column['id'] }}"
                                                        class="h-5 w-5 appearance-none rounded-md border-2 border-slate-300 bg-white text-[#c84a2f] transition checked:border-[#c84a2f] checked:bg-[#c84a2f] focus:ring-2 focus:ring-[#f2cfc1]"
                                                    >
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @if($activeStep->type === 'text_matching')
                            <div class="grid gap-5 xl:grid-cols-12">
                                <div class="space-y-4 xl:col-span-7">
                                    @foreach($this->orderedItems($activeStep, $activeAttempt) as $item)
                                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-4">
                                            <div class="flex flex-wrap items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p>
                                                </div>

                                                <select wire:model="answer.matches.{{ $item['key'] }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4] sm:max-w-[120px]">
                                                    <option value="">Pilih huruf</option>
                                                    @foreach(($payload['options'] ?? []) as $option)
                                                        <option value="{{ $option['key'] }}">{{ $option['key'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="rounded-[22px] border border-slate-200 bg-white p-4 shadow-sm xl:col-span-5">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Daftar Jawaban</p>
                                    <div class="mt-4 space-y-3">
                                        @foreach(($payload['options'] ?? []) as $option)
                                            <div class="flex items-start gap-3 rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#c84a2f] text-sm font-semibold text-white">
                                                    {{ $option['key'] }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Jawaban {{ $option['key'] }}</p>
                                                    <p class="text-sm leading-6 text-slate-700">{{ $option['label'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            @if($activeStep->type === 'image_text_matching')
                                <div class="grid gap-5 md:grid-cols-2">
                                    @foreach($this->orderedItems($activeStep, $activeAttempt) as $item)
                                        @php
                                            $itemResult = collect(data_get($activeAttempt->result_payload ?? [], 'items', []))->firstWhere('item_key', $item['key']);
                                            $itemIsReviewed = in_array($activeAttempt?->status, ['auto_graded', 'completed'], true);
                                            $itemStateClass = ! $itemIsReviewed
                                                ? 'border-slate-200 bg-white'
                                                : (($itemResult['is_correct'] ?? false)
                                                    ? 'border-emerald-200 bg-emerald-50/70'
                                                    : 'border-red-200 bg-red-50/70');
                                        @endphp
                                        <article class="overflow-hidden rounded-[22px] border p-4 shadow-sm transition {{ $itemStateClass }}">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="inline-flex items-center rounded-full bg-[#f8ded2] px-3 py-1 text-xs font-semibold text-[#b64027]">Soal {{ $loop->iteration }}</span>
                                                @if(filled($item['image_url'] ?? null))
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600 transition hover:border-[#db8b73] hover:text-[#b64027]"
                                                        onclick="window.dispatchEvent(new CustomEvent('open-image-preview', { detail: { src: '{{ $item['image_url'] }}', alt: @js($item['alt'] ?? $item['label']) } }))"
                                                    >
                                                        <span class="material-symbols-outlined text-[16px]">zoom_in</span>
                                                        Perbesar
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="mt-4 overflow-hidden rounded-[20px] border border-slate-200 bg-gradient-to-br from-[#fffaf6] to-[#fff1e7]/60">
                                                <div class="flex aspect-[4/3] items-center justify-center p-4 sm:p-5">
                                                    @if(filled($item['image_url'] ?? null))
                                                        <img
                                                            src="{{ $item['image_url'] }}"
                                                            alt="{{ $item['alt'] ?? $item['label'] }}"
                                                            class="max-h-full w-full object-contain"
                                                        >
                                                    @else
                                                        <div class="flex h-full w-full items-center justify-center rounded-[16px] border border-dashed border-slate-300 bg-white text-sm font-medium text-slate-400">
                                                            Gambar tidak tersedia
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">Pilih kategori yang paling sesuai dengan objek pada gambar.</p>
                                            </div>

                                            <div class="mt-4">
                                                <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Jawaban</label>
                                                <select wire:model="answer.matches.{{ $item['key'] }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                                                    <option value="">Pilih jawaban</option>
                                                    @foreach(($payload['options'] ?? []) as $option)
                                                        <option value="{{ $option['key'] }}">{{ $option['label'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @if($itemIsReviewed && $itemResult)
                                                <div class="mt-4 rounded-[18px] border border-white/70 bg-white/80 px-4 py-3 text-sm">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <span class="font-semibold text-slate-900">Hasil soal</span>
                                                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-white {{ ($itemResult['is_correct'] ?? false) ? 'bg-emerald-600' : 'bg-red-600' }}">
                                                            {{ ($itemResult['is_correct'] ?? false) ? 'Tepat' : 'Belum tepat' }}
                                                        </span>
                                                    </div>
                                                    <p class="mt-3 text-slate-700">Jawaban Anda: <strong>{{ $itemResult['selected_option_label'] ?? '-' }}</strong></p>
                                                    <p class="mt-1 text-slate-700">Kunci jawaban: <strong>{{ $itemResult['correct_option_label'] ?? '-' }}</strong></p>
                                                </div>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($this->orderedItems($activeStep, $activeAttempt) as $item)
                                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-4">
                                            <label class="block text-sm font-semibold text-slate-900">
                                                {{ $item['label'] }}
                                            </label>
                                            <select wire:model="answer.matches.{{ $item['key'] }}" class="mt-3 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#db8b73] focus:ring-2 focus:ring-[#f7ded4]">
                                                <option value="">Pilih jawaban</option>
                                                @foreach(($payload['options'] ?? []) as $option)
                                                    <option value="{{ $option['key'] }}">{{ $option['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    @endif
                </div>

                @php
                    $resultPayload = $activeAttempt->result_payload ?? [];
                    $resultCorrect = (int) data_get($resultPayload, 'correct', 0);
                    $resultTotal = (int) data_get($resultPayload, 'total', 0);
                    $hasResultMessage = filled(data_get($resultPayload, 'message'));
                    $hasResultScore = array_key_exists('correct', $resultPayload);
                    $showResultPanel = $hasResultMessage || $hasResultScore;
                    $resultStatusLabel = $resultTotal > 0 ? 'Tepat ' . $resultCorrect . ' dari ' . $resultTotal : null;
                    $selectedLabel = in_array($activeStep->type, ['table_checklist'], true) ? 'Pilihan Anda' : 'Jawaban Anda';
                    $correctLabel = in_array($activeStep->type, ['table_checklist'], true) ? 'Kunci jawaban' : 'Kunci jawaban';
                @endphp

                @if($showResultPanel)
                    <div id="quiz-feedback-panel" tabindex="-1" class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm scroll-mt-24 motion-safe:animate-[quizFeedbackIn_380ms_ease-out]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Hasil</p>
                                <h3 class="mt-1 text-2xl font-semibold text-slate-900">Ringkasan Penilaian</h3>
                            </div>
                            @if($resultStatusLabel)
                                <span class="rounded-full bg-[#f8ded2] px-3 py-1 text-sm font-semibold text-[#b64027]">
                                    {{ $resultStatusLabel }}
                                </span>
                            @endif
                        </div>

                        @if($hasResultMessage)
                            <p class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                Jawaban esai telah dikirim dan menunggu penilaian guru.
                            </p>
                        @else
                            <p class="mt-4 text-sm text-slate-600">
                                Skor akhir: {{ $activeAttempt->score }}.
                            </p>
                            <div class="mt-4 space-y-3">
                                @foreach(data_get($resultPayload, 'items', []) as $item)
                                    <div class="rounded-[18px] border p-4 {{ ($item['is_correct'] ?? false) ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="font-semibold text-slate-900">{{ $item['item_label'] ?? $item['row_label'] ?? $item['item_key'] ?? $item['row_id'] }}</p>
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white {{ ($item['is_correct'] ?? false) ? 'bg-emerald-600' : 'bg-red-600' }}">
                                                {{ ($item['is_correct'] ?? false) ? 'Tepat' : 'Perlu ditinjau' }}
                                            </span>
                                        </div>

                                        @if($activeStep->type === 'table_checklist')
                                            <p class="mt-3 text-sm text-slate-700">{{ $selectedLabel }}: <strong>{{ $item['selected_column_label'] ?? '-' }}</strong></p>
                                            <p class="text-sm text-slate-700">{{ $correctLabel }}: <strong>{{ $item['correct_column_label'] ?? '-' }}</strong></p>
                                        @else
                                            <p class="mt-3 text-sm text-slate-700">{{ $selectedLabel }}: <strong>{{ $item['selected_option_label'] ?? '-' }}</strong></p>
                                            <p class="text-sm text-slate-700">{{ $correctLabel }}: <strong>{{ $item['correct_option_label'] ?? '-' }}</strong></p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3">
                    @if(! in_array($activeAttempt?->status, ['auto_graded', 'pending_review', 'completed'], true))
                        <button wire:click="submit" wire:loading.attr="disabled" wire:target="submit" class="inline-flex items-center justify-center rounded-full bg-[#c84a2f] px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25] disabled:cursor-not-allowed disabled:opacity-70">
                            <span wire:loading.remove wire:target="submit">Kirim Jawaban</span>
                            <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                                <span class="inline-flex h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                Memproses
                            </span>
                        </button>
                    @endif

                    @if(in_array($activeAttempt?->status, ['auto_graded', 'pending_review', 'completed'], true))
                        @if($this->isLastStep())
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <a href="{{ route('student.courses.show', $quiz->module->course) }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-[#efc2b2] bg-[#fff5ef] px-5 py-2.5 text-sm font-semibold text-[#b64027] transition hover:border-[#db8b73] hover:bg-[#f2cfc1]">
                                    Kembali ke Materi
                                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                                </a>
                                <a href="{{ route('student.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#c84a2f] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-[#c84a2f]/20 transition hover:bg-[#a93b25] hover:shadow-xl hover:shadow-[#c84a2f]/30">
                                    Kembali ke Beranda
                                    <span class="material-symbols-outlined text-[18px]">home</span>
                                </a>
                            </div>
                        @else
                            <button wire:click="next" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#c84a2f] px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-[#c84a2f]/20 transition hover:bg-[#a93b25] hover:shadow-lg hover:shadow-[#c84a2f]/30">
                                Soal Selanjutnya
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>
                        @endif
                    @endif
                </div>
            @endif
        </section>

    </div>
    <dialog id="quiz-image-preview" class="quiz-image-preview">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#b64027]">Preview Gambar</p>
                <p id="quiz-image-preview-caption" class="mt-1 text-sm font-medium text-slate-700">Preview gambar soal</p>
            </div>
            <form method="dialog">
                <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-[#db8b73] hover:text-[#b64027]">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </form>
        </div>
        <div class="bg-slate-50 p-5">
            <div class="flex max-h-[72vh] min-h-[320px] items-center justify-center rounded-[20px] border border-slate-200 bg-white p-4">
                <img id="quiz-image-preview-image" src="" alt="" class="max-h-[64vh] w-full object-contain">
            </div>
        </div>
    </dialog>
</div>

@push('styles')
    <style>
        @keyframes quizFeedbackIn {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        dialog.quiz-image-preview {
            width: min(92vw, 960px);
            max-height: 90vh;
            margin: auto;
            padding: 0;
            border: 0;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.28);
        }

        dialog.quiz-image-preview::backdrop {
            background: rgba(15, 23, 42, 0.58);
            backdrop-filter: blur(4px);
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.addEventListener('quiz-feedback-ready', () => {
            const panel = document.getElementById('quiz-feedback-panel');

            if (!panel) {
                return;
            }

            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            panel.focus({ preventScroll: true });
        });

        window.addEventListener('open-image-preview', (event) => {
            const dialog = document.getElementById('quiz-image-preview');
            const image = document.getElementById('quiz-image-preview-image');
            const caption = document.getElementById('quiz-image-preview-caption');

            if (!dialog || !image || !caption) {
                return;
            }

            image.src = event.detail.src;
            image.alt = event.detail.alt || 'Preview gambar soal';
            caption.textContent = event.detail.alt || 'Preview gambar soal';
            dialog.showModal();
        });
    </script>
@endpush
