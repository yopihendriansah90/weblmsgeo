<div class="space-y-6">
    <section class="rounded-lg border border-neutral-200 bg-white p-5">
        <p class="text-sm text-neutral-600">{{ $quiz->lesson->title }}</p>
        <h1 class="text-2xl font-semibold">{{ $quiz->title }}</h1>
        <p class="mt-2 text-neutral-700">{{ $quiz->description }}</p>
    </section>

    <div class="grid gap-4 lg:grid-cols-[240px_1fr]">
        <aside class="rounded-lg border border-neutral-200 bg-white p-4">
            <div class="space-y-2">
                @foreach($steps as $step)
                    @php($stepAttempt = $stepAttempts->get($step->id))
                    <button wire:click="setActiveStep({{ $step->id }})" class="w-full rounded-md border px-3 py-2 text-left text-sm {{ $activeStepId === $step->id ? 'border-emerald-600 bg-emerald-50' : 'border-neutral-200' }}" @disabled($stepAttempt?->status === 'locked')>
                        <span class="block font-medium">{{ $step->title }}</span>
                        <span class="text-xs text-neutral-600">{{ $stepAttempt?->status ?? 'locked' }}</span>
                    </button>
                @endforeach
            </div>
        </aside>

        <section class="rounded-lg border border-neutral-200 bg-white p-5">
            @if($activeStep)
                @php($payload = $activeStep->content_payload ?? [])
                @php($activeAttempt = $stepAttempts->get($activeStep->id))
                <h2 class="text-xl font-semibold">{{ $activeStep->title }}</h2>
                <p class="mt-2 text-neutral-700">{{ $activeStep->instruction }}</p>

                @if($errorMessage)
                    <div class="mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errorMessage }}</div>
                @endif

                <div class="mt-5 space-y-4">
                    @if($activeStep->type === 'essay')
                        <p class="font-medium">{{ $payload['question'] ?? 'Tuliskan jawaban kamu.' }}</p>
                        <textarea wire:model="answer.essay" class="min-h-40 w-full rounded-md border border-neutral-300 px-3 py-2"></textarea>
                        @error('answer.essay') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    @elseif($activeStep->type === 'table_checklist')
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-sm">
                                <thead>
                                    <tr>
                                        <th class="border border-neutral-200 p-2 text-left">Pernyataan</th>
                                        @foreach(($payload['columns'] ?? []) as $column)
                                            <th class="border border-neutral-200 p-2">{{ $column['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($payload['rows'] ?? []) as $row)
                                        <tr>
                                            <td class="border border-neutral-200 p-2">{{ $row['label'] }}</td>
                                            @foreach(($payload['columns'] ?? []) as $column)
                                                <td class="border border-neutral-200 p-2 text-center">
                                                    <input type="radio" wire:model="answer.rows.{{ $row['id'] }}" value="{{ $column['id'] }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @foreach(($payload['items'] ?? []) as $item)
                            <div class="rounded-md border border-neutral-200 p-3">
                                <label class="block text-sm font-medium">
                                    @if($activeStep->type === 'image_text_matching' && filled($item['image_url'] ?? null))
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['alt'] ?? $item['label'] }}" class="mb-2 h-32 rounded-md object-cover">
                                    @endif
                                    {{ $item['label'] }}
                                </label>
                                <select wire:model="answer.matches.{{ $item['key'] }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-2">
                                    <option value="">Pilih jawaban</option>
                                    @foreach(($payload['options'] ?? []) as $option)
                                        <option value="{{ $option['key'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    @endif
                </div>

                @if($activeAttempt?->result_payload)
                    <div class="mt-5 rounded-md border border-neutral-200 bg-neutral-50 p-4">
                        <p class="font-medium">Hasil</p>
                        @if(isset($activeAttempt->result_payload['message']))
                            <p class="mt-1 text-sm text-neutral-700">{{ $activeAttempt->result_payload['message'] }}</p>
                        @else
                            <p class="mt-1 text-sm text-neutral-700">Benar {{ $activeAttempt->result_payload['correct'] ?? 0 }} dari {{ $activeAttempt->result_payload['total'] ?? 0 }}. Skor: {{ $activeAttempt->score }}</p>
                        @endif
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    @if(! in_array($activeAttempt?->status, ['auto_graded', 'pending_review', 'completed'], true))
                        <button wire:click="submit" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white">Kirim Jawaban</button>
                    @endif
                    @if(in_array($activeAttempt?->status, ['auto_graded', 'pending_review', 'completed'], true))
                        <button wire:click="next" class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-medium">Next</button>
                    @endif
                </div>
            @endif
        </section>
    </div>

    <section class="rounded-lg border border-neutral-200 bg-white p-5">
        <h2 class="text-lg font-semibold">Ringkasan</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-4">
            <div>Auto: <strong>{{ $attempt->fresh()->auto_score ?? '-' }}</strong></div>
            <div>Essay: <strong>{{ $attempt->fresh()->essay_score ?? '-' }}</strong></div>
            <div>Final: <strong>{{ $attempt->fresh()->final_score ?? '-' }}</strong></div>
            <div>Status: <strong>{{ $attempt->fresh()->status }}</strong></div>
        </div>
    </section>
</div>
