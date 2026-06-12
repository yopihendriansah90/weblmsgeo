<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-[20px] border border-slate-200/80 bg-white px-4 py-3 shadow-sm">
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <a href="{{ route('student.courses.show', $quiz->module->course) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Materi
            </a>
            <a href="{{ route('student.quiz-history') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700">
                <span class="material-symbols-outlined text-[18px]">history</span>
                Riwayat Kuis
            </a>
        </div>
        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Mode fokus</span>
    </div>

    <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">{{ $quiz->module->title }}</p>
                <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">{{ $quiz->title }}</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600 sm:text-base">{{ $quiz->description }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700">{{ $attempt->status }}</span>
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">{{ $steps->count() }} step</span>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-12">
        <aside class="rounded-[24px] border border-slate-200/80 bg-white p-4 shadow-sm xl:col-span-3">
            <p class="px-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Langkah Quiz</p>
            <div class="mt-4 space-y-2">
                @foreach($steps as $step)
                    @php($stepAttempt = $stepAttempts->get($step->id))
                    @php($isActive = $activeStepId === $step->id)
                    <button
                        wire:click="setActiveStep({{ $step->id }})"
                        class="flex w-full items-start gap-3 rounded-[18px] border px-3 py-3 text-left transition {{ $isActive ? 'border-indigo-300 bg-indigo-50 shadow-sm' : 'border-slate-200 bg-slate-50 hover:border-indigo-300 hover:bg-white' }}"
                        @disabled($stepAttempt?->status === 'locked')
                    >
                        <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full {{ $isActive ? 'bg-indigo-600 text-white' : 'bg-white text-slate-500' }}">
                            {{ $loop->iteration }}
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-slate-900">{{ $step->title }}</span>
                            <span class="text-xs text-slate-500">{{ $stepAttempt?->status ?? 'locked' }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </aside>

        <section class="space-y-6 xl:col-span-6">
            @if($activeStep)
                @php($payload = $activeStep->content_payload ?? [])
                @php($activeAttempt = $stepAttempts->get($activeStep->id))

                <div class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Step Aktif</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $activeStep->title }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $activeStep->instruction }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $activeStep->type }}</span>
                    </div>

                    @if($errorMessage)
                        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errorMessage }}</div>
                    @endif
                </div>

                <div class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                    @if($activeStep->type === 'essay')
                        <p class="text-lg font-semibold text-slate-900">{{ $payload['question'] ?? 'Tuliskan jawaban kamu.' }}</p>
                        <textarea wire:model="answer.essay" class="mt-4 min-h-48 w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100"></textarea>
                        @error('answer.essay') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    @elseif($activeStep->type === 'table_checklist')
                        <div class="overflow-x-auto">
                            <table class="w-full border-separate border-spacing-0 text-sm">
                                <thead>
                                    <tr class="text-left">
                                        <th class="border-b border-slate-200 px-3 py-3 font-semibold text-slate-700">Pernyataan</th>
                                        @foreach(($payload['columns'] ?? []) as $column)
                                            <th class="border-b border-slate-200 px-3 py-3 text-center font-semibold text-slate-700">{{ $column['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($payload['rows'] ?? []) as $row)
                                        <tr class="align-top">
                                            <td class="border-b border-slate-100 px-3 py-3 font-medium text-slate-900">{{ $row['label'] }}</td>
                                            @foreach(($payload['columns'] ?? []) as $column)
                                                <td class="border-b border-slate-100 px-3 py-3 text-center">
                                                    <input type="radio" wire:model="answer.rows.{{ $row['id'] }}" value="{{ $column['id'] }}" class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-200">
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
                                                    <span class="mb-2 inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                                        Soal {{ $item['key'] }}
                                                    </span>
                                                    <p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p>
                                                </div>

                                                <select wire:model="answer.matches.{{ $item['key'] }}" class="w-full max-w-[160px] rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100">
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
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Daftar Jawaban</p>
                                    <h3 class="mt-1 text-lg font-semibold text-slate-900">Pilih huruf dari kolom ini</h3>
                                    <div class="mt-4 space-y-3">
                                        @foreach(($payload['options'] ?? []) as $option)
                                            <div class="flex items-start gap-3 rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white">
                                                    {{ $option['key'] }}
                                                </span>
                                                <p class="text-sm leading-6 text-slate-700">{{ $option['label'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($this->orderedItems($activeStep, $activeAttempt) as $item)
                                    <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-4">
                                        <label class="block text-sm font-semibold text-slate-900">
                                            @if($activeStep->type === 'image_text_matching' && filled($item['image_url'] ?? null))
                                                <img src="{{ $item['image_url'] }}" alt="{{ $item['alt'] ?? $item['label'] }}" class="mb-3 h-36 w-full rounded-2xl object-cover">
                                            @endif
                                            {{ $item['label'] }}
                                        </label>
                                        <select wire:model="answer.matches.{{ $item['key'] }}" class="mt-3 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100">
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
                </div>

                @if(isset($activeAttempt->result_payload['message']) || isset($activeAttempt->result_payload['correct']))
                    <div class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Hasil</p>
                                <h3 class="mt-1 text-2xl font-semibold text-slate-900">Umpan balik jawaban</h3>
                            </div>
                            @if(isset($activeAttempt->result_payload['correct']))
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-700">
                                    {{ $activeAttempt->result_payload['correct'] ?? 0 }} / {{ $activeAttempt->result_payload['total'] ?? 0 }}
                                </span>
                            @endif
                        </div>

                        @if(isset($activeAttempt->result_payload['message']))
                            <p class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                {{ $activeAttempt->result_payload['message'] }}
                            </p>
                        @else
                            <p class="mt-4 text-sm text-slate-600">
                                Benar {{ $activeAttempt->result_payload['correct'] ?? 0 }} dari {{ $activeAttempt->result_payload['total'] ?? 0 }}. Skor: {{ $activeAttempt->score }}
                            </p>
                            <div class="mt-4 space-y-3">
                                @foreach($activeAttempt->result_payload['items'] ?? [] as $item)
                                    <div class="rounded-[18px] border p-4 {{ ($item['is_correct'] ?? false) ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="font-semibold text-slate-900">{{ $item['item_label'] ?? $item['row_label'] ?? $item['item_key'] ?? $item['row_id'] }}</p>
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white {{ ($item['is_correct'] ?? false) ? 'bg-emerald-600' : 'bg-red-600' }}">
                                                {{ ($item['is_correct'] ?? false) ? 'Benar' : 'Salah' }}
                                            </span>
                                        </div>

                                        @if($activeStep->type === 'table_checklist')
                                            <p class="mt-3 text-sm text-slate-700">Jawabanmu: <strong>{{ $item['selected_column_label'] ?? '-' }}</strong></p>
                                            <p class="text-sm text-slate-700">Jawaban benar: <strong>{{ $item['correct_column_label'] ?? '-' }}</strong></p>
                                        @else
                                            <p class="mt-3 text-sm text-slate-700">Jawabanmu: <strong>{{ $item['selected_option_label'] ?? '-' }}</strong></p>
                                            <p class="text-sm text-slate-700">Jawaban benar: <strong>{{ $item['correct_option_label'] ?? '-' }}</strong></p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="flex flex-wrap gap-3">
                    @if(! in_array($activeAttempt?->status, ['auto_graded', 'pending_review', 'completed'], true))
                        <button wire:click="submit" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700">
                            Kirim Jawaban
                        </button>
                    @endif
                    @if(in_array($activeAttempt?->status, ['auto_graded', 'pending_review', 'completed'], true))
                        <button wire:click="next" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-indigo-300 hover:text-indigo-700">
                            Next
                        </button>
                    @endif
                </div>
            @endif
        </section>

        <aside class="space-y-6 xl:col-span-3">
            <section class="rounded-[24px] border border-slate-200/80 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-indigo-700">Ringkasan</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Status attempt</h2>

                <div class="mt-5 space-y-3">
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Auto</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $attempt->fresh()->auto_score ?? '-' }}</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Essay</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $attempt->fresh()->essay_score ?? '-' }}</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Final</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $attempt->fresh()->final_score ?? '-' }}</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ $attempt->fresh()->status }}</p>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>
