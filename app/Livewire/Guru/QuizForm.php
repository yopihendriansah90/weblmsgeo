<?php

namespace App\Livewire\Guru;

use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guru')]
class QuizForm extends Component
{
    use WithFileUploads;

    public Module $module;
    public ?Quiz $quiz = null;

    public string $title = '';
    public ?string $description = null;
    public string $mode = 'practice';
    public bool $allow_retake = false;
    public ?int $max_attempts = 1;
    public string $status = 'draft';
    public string $activeTab = 'essay';

    public array $stepForms = [];
    public array $imageUploads = [];

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function mount(Module $module, ?Quiz $quiz = null): void
    {
        abort_unless($module->course, 404);
        abort_unless($module->isQuiz(), 404);

        $this->module = $module->load('course');
        $this->quiz = $quiz?->load('steps');

        $this->initializeStepForms();

        if ($this->quiz && $this->quiz->module_id !== $this->module->id) {
            abort(404);
        }

        if ($this->quiz) {
            $this->title = $this->quiz->title;
            $this->description = $this->quiz->description;
            $this->mode = $this->quiz->mode;
            $this->allow_retake = (bool) $this->quiz->allow_retake;
            $this->max_attempts = $this->quiz->max_attempts;
            $this->status = $this->quiz->status;
            $this->fillStepFormsFromQuiz();
        } else {
            $this->title = 'Kuis Materi';
            $this->description = 'Quiz akhir materi.';
        }
    }

    public function addRow(string $type, string $section): void
    {
        $this->activeTab = $type;
        $this->stepForms[$type][$section][] = $this->blankRow($type, $section);

        if ($type === 'image_text_matching' && $section === 'pairs') {
            $this->imageUploads[] = null;
        }
    }

    public function removeRow(string $type, string $section, int $index): void
    {
        $this->activeTab = $type;

        if (! isset($this->stepForms[$type][$section][$index])) {
            return;
        }

        unset($this->stepForms[$type][$section][$index]);
        $this->stepForms[$type][$section] = array_values($this->stepForms[$type][$section]);

        if ($this->stepForms[$type][$section] === []) {
            $this->stepForms[$type][$section][] = $this->blankRow($type, $section);
        }

        if ($type === 'image_text_matching' && $section === 'pairs') {
            $this->imageUploads = array_values($this->imageUploads);

            if ($this->imageUploads === []) {
                $this->imageUploads[] = null;
            }
        }
    }

    public function save()
    {
        $validated = $this->validate($this->rules());

        DB::transaction(function () use ($validated): void {
            $quizPayload = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'mode' => $validated['mode'],
                'allow_retake' => (bool) $validated['allow_retake'],
                'max_attempts' => $validated['max_attempts'],
                'status' => $validated['status'],
                'module_id' => $this->module->id,
                'created_by' => $this->quiz?->created_by ?? auth()->id(),
                'updated_by' => auth()->id(),
            ];

            $this->quiz ? $this->quiz->update($quizPayload) : $this->quiz = Quiz::create($quizPayload);

            foreach ($this->stepDefinitions() as $type => $definition) {
                $form = $this->stepForms[$type];
                if ($type === 'image_text_matching') {
                    $form = $this->prepareImageTextMatchingForm($form);
                }

                QuizStep::updateOrCreate(
                    ['quiz_id' => $this->quiz->id, 'type' => $type],
                    [
                        'title' => $form['title'],
                        'type' => $type,
                        'instruction' => $form['instruction'],
                        'content_payload' => $this->buildPayload($type, $form),
                        'sort_order' => $definition['sort_order'],
                        'answer_mode' => $definition['answer_mode'],
                        'is_required' => true,
                        'show_result_after_submit' => true,
                        'allow_next_after_submit' => true,
                        'status' => 'published',
                    ]
                );
            }
        });

        session()->flash('success', 'Quiz berhasil disimpan.');

        return redirect()->route('guru.quizzes.edit', [
            'module' => $this->module,
            'quiz' => $this->quiz,
        ]);
    }

    public function render()
    {
        return view('livewire.guru.quiz-form', [
            'title' => $this->quiz ? 'Kelola Quiz' : 'Tambah Quiz',
            'stepDefinitions' => $this->stepDefinitions(),
        ]);
    }

    private function initializeStepForms(): void
    {
        $this->stepForms = [
            'essay' => [
                'title' => 'Essay',
                'instruction' => 'Jawab dengan singkat dan jelas.',
                'question' => 'Jelaskan...',
            ],
            'text_matching' => [
                'title' => 'Penjodohan Teks',
                'instruction' => 'Pasangkan soal dengan jawaban yang tepat.',
                'pairs' => [
                    $this->blankRow('text_matching', 'pairs'),
                    $this->blankRow('text_matching', 'pairs'),
                    $this->blankRow('text_matching', 'pairs'),
                ],
            ],
            'table_checklist' => [
                'title' => 'Checklist Tabel',
                'instruction' => 'Isi label kolom di atas, tulis pernyataan pada baris, lalu pilih kolom yang benar di setiap baris.',
                'columns' => [
                    $this->blankRow('table_checklist', 'columns'),
                    $this->blankRow('table_checklist', 'columns'),
                    $this->blankRow('table_checklist', 'columns'),
                ],
                'rows' => [
                    $this->blankRow('table_checklist', 'rows'),
                    $this->blankRow('table_checklist', 'rows'),
                    $this->blankRow('table_checklist', 'rows'),
                ],
            ],
            'image_text_matching' => [
                'title' => 'Penjodohan Gambar-Teks',
                'instruction' => 'Isi soal, pilih jawaban, lalu unggah gambar untuk setiap pasangan.',
                'pairs' => [
                    $this->blankRow('image_text_matching', 'pairs'),
                    $this->blankRow('image_text_matching', 'pairs'),
                ],
            ],
        ];
    }

    private function fillStepFormsFromQuiz(): void
    {
        foreach ($this->quiz->steps as $step) {
            if (! isset($this->stepForms[$step->type])) {
                continue;
            }

            $this->stepForms[$step->type]['title'] = $step->title;
            $this->stepForms[$step->type]['instruction'] = $step->instruction;

            match ($step->type) {
                'essay' => $this->stepForms[$step->type]['question'] = $step->content_payload['question'] ?? '',
                'text_matching' => $this->hydrateTextMatching($step),
                'table_checklist' => $this->hydrateTableChecklist($step),
                'image_text_matching' => $this->hydrateImageTextMatching($step),
                default => null,
            };
        }
    }

    private function hydrateTextMatching(QuizStep $step): void
    {
        $payload = $step->content_payload ?? [];
        $items = collect($payload['items'] ?? []);
        $options = collect($payload['options'] ?? []);
        $pairs = collect($payload['pairs'] ?? []);

        if ($pairs->isEmpty() && $items->isNotEmpty() && $options->isNotEmpty()) {
            $pairs = $items->values()->map(function (array $item, int $index) use ($options): array {
                $option = $options->values()->get($index) ?? [];

                return [
                    'item_key' => $item['key'] ?? $this->numberKey($index),
                    'correct_option_key' => $option['key'] ?? $this->alphabetKey($index),
                    'question_label' => $item['label'] ?? '',
                    'answer_label' => $option['label'] ?? '',
                ];
            });
        }

        $this->stepForms['text_matching']['pairs'] = $pairs
            ->map(function (array $pair) use ($items, $options): array {
                $item = $items->firstWhere('key', $pair['item_key'] ?? null) ?? [];
                $option = $options->firstWhere('key', $pair['correct_option_key'] ?? null) ?? [];

                return [
                    'question_label' => $pair['question_label'] ?? $item['label'] ?? '',
                    'answer_label' => $pair['answer_label'] ?? $option['label'] ?? '',
                ];
            })
            ->whenEmpty(fn () => collect([$this->blankRow('text_matching', 'pairs')]))
            ->all();

        if ($this->stepForms['text_matching']['pairs'] === []) {
            $this->stepForms['text_matching']['pairs'][] = $this->blankRow('text_matching', 'pairs');
        }
    }

    private function hydrateTableChecklist(QuizStep $step): void
    {
        $payload = $step->content_payload ?? [];

        $this->stepForms['table_checklist']['columns'] = collect($payload['columns'] ?? [])
            ->map(fn (array $row): array => [
                'label' => $row['label'] ?? '',
            ])
            ->whenEmpty(fn () => collect([$this->blankRow('table_checklist', 'columns')]))
            ->all();

        $this->stepForms['table_checklist']['rows'] = collect($payload['rows'] ?? [])
            ->map(function (array $row) use ($payload): array {
                $correctCell = collect($payload['correct_cells'] ?? [])->firstWhere('row_id', $row['id'] ?? null);
                $columnIndex = collect($payload['columns'] ?? [])
                    ->values()
                    ->search(fn (array $column): bool => ($column['id'] ?? null) === ($correctCell['column_id'] ?? null));

                return [
                    'label' => $row['label'] ?? '',
                    'correct_column_id' => $columnIndex === false ? '' : $this->tableChecklistColumnKey((int) $columnIndex),
                ];
            })
            ->whenEmpty(fn () => collect([$this->blankRow('table_checklist', 'rows')]))
            ->all();
    }

    private function hydrateImageTextMatching(QuizStep $step): void
    {
        $payload = $step->content_payload ?? [];
        $pairs = collect($payload['pairs'] ?? []);
        $items = collect($payload['items'] ?? []);
        $options = collect($payload['options'] ?? []);

        if ($pairs->isEmpty() && $items->isNotEmpty() && $options->isNotEmpty()) {
            $pairs = $items->values()->map(function (array $item, int $index) use ($options): array {
                $option = $options->values()->get($index) ?? [];

                return [
                    'question_label' => $item['label'] ?? $item['question_label'] ?? '',
                    'answer_label' => $option['label'] ?? '',
                    'image_url' => $item['image_url'] ?? '',
                ];
            });
        }

        $this->stepForms['image_text_matching']['pairs'] = $pairs
            ->map(function (array $pair) use ($items, $options): array {
                $questionLabel = $pair['question_label'] ?? $pair['label'] ?? '';
                $answerLabel = $pair['answer_label'] ?? '';
                $item = $items->firstWhere('label', $questionLabel) ?? $items->firstWhere('question_label', $questionLabel) ?? [];
                $option = $options->firstWhere('label', $answerLabel) ?? [];

                return [
                    'question_label' => $questionLabel,
                    'answer_label' => $answerLabel,
                    'image_url' => $pair['image_url'] ?? $item['image_url'] ?? '',
                    'correct_option_key' => $pair['correct_option_key'] ?? $option['key'] ?? '',
                ];
            })
            ->whenEmpty(fn () => collect([$this->blankRow('image_text_matching', 'pairs')]))
            ->all();
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'mode' => ['required', Rule::in(['practice', 'final'])],
            'allow_retake' => ['boolean'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],

            'stepForms.essay.title' => ['required', 'string', 'max:255'],
            'stepForms.essay.instruction' => ['nullable', 'string'],
            'stepForms.essay.question' => ['required', 'string'],

            'stepForms.text_matching.title' => ['required', 'string', 'max:255'],
            'stepForms.text_matching.instruction' => ['nullable', 'string'],
            'stepForms.text_matching.pairs' => ['array'],
            'stepForms.text_matching.pairs.*.question_label' => ['required', 'string'],
            'stepForms.text_matching.pairs.*.answer_label' => ['required', 'string'],

            'stepForms.table_checklist.title' => ['required', 'string', 'max:255'],
            'stepForms.table_checklist.instruction' => ['nullable', 'string'],
            'stepForms.table_checklist.columns' => ['array'],
            'stepForms.table_checklist.columns.*.label' => ['required', 'string'],
            'stepForms.table_checklist.rows' => ['array'],
            'stepForms.table_checklist.rows.*.label' => ['required', 'string'],
            'stepForms.table_checklist.rows.*.correct_column_id' => ['required', 'string'],

            'stepForms.image_text_matching.title' => ['required', 'string', 'max:255'],
            'stepForms.image_text_matching.instruction' => ['nullable', 'string'],
            'stepForms.image_text_matching.pairs' => ['array'],
            'stepForms.image_text_matching.pairs.*.question_label' => ['required', 'string'],
            'stepForms.image_text_matching.pairs.*.answer_label' => ['required', 'string'],
            'imageUploads' => ['array'],
            'imageUploads.*' => ['nullable', 'image', 'max:5120'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul quiz wajib diisi.',
            'mode.required' => 'Mode quiz wajib dipilih.',
            'max_attempts.min' => 'Maksimal percobaan minimal 1.',
            'status.required' => 'Status quiz wajib dipilih.',
            'stepForms.essay.title.required' => 'Judul step esai wajib diisi.',
            'stepForms.essay.question.required' => 'Pertanyaan esai wajib diisi.',
            'stepForms.text_matching.title.required' => 'Judul step penjodohan teks wajib diisi.',
            'stepForms.text_matching.pairs.*.question_label.required' => 'Label soal wajib diisi.',
            'stepForms.text_matching.pairs.*.answer_label.required' => 'Label jawaban wajib diisi.',
            'stepForms.table_checklist.title.required' => 'Judul step checklist tabel wajib diisi.',
            'stepForms.table_checklist.columns.*.label.required' => 'Label kolom wajib diisi.',
            'stepForms.table_checklist.rows.*.label.required' => 'Pernyataan wajib diisi.',
            'stepForms.table_checklist.rows.*.correct_column_id.required' => 'Kolom yang benar wajib dipilih.',
            'stepForms.image_text_matching.title.required' => 'Judul step penjodohan gambar-teks wajib diisi.',
            'stepForms.image_text_matching.pairs.*.question_label.required' => 'Soal wajib diisi.',
            'stepForms.image_text_matching.pairs.*.answer_label.required' => 'Label jawaban wajib diisi.',
            'imageUploads.*.image' => 'File gambar harus berupa gambar.',
        ];
    }

    private function buildPayload(string $type, array $form): array
    {
        return match ($type) {
            'essay' => [
                'question' => trim($form['question'] ?? ''),
            ],
            'text_matching' => [
                'options' => collect($this->normalizeList($form['pairs'] ?? [], ['question_label', 'answer_label']))
                    ->values()
                    ->map(fn (array $pair, int $index): array => [
                        'key' => $this->alphabetKey($index),
                        'label' => $pair['answer_label'],
                    ])
                    ->all(),
                'items' => collect($this->normalizeList($form['pairs'] ?? [], ['question_label', 'answer_label']))
                    ->values()
                    ->map(fn (array $pair, int $index): array => [
                        'key' => $this->numberKey($index),
                        'label' => $pair['question_label'],
                    ])
                    ->all(),
                'pairs' => collect($this->normalizeList($form['pairs'] ?? [], ['question_label', 'answer_label']))
                    ->values()
                    ->map(fn (array $pair, int $index): array => [
                        'item_key' => $this->numberKey($index),
                        'correct_option_key' => $this->alphabetKey($index),
                        'question_label' => $pair['question_label'],
                        'answer_label' => $pair['answer_label'],
                    ])
                    ->all(),
            ],
            'table_checklist' => [
                'columns' => collect($this->normalizeList($form['columns'] ?? [], ['label']))
                    ->values()
                    ->map(fn (array $row, int $index): array => [
                        'id' => $this->tableChecklistColumnKey($index),
                        'label' => $row['label'],
                    ])
                    ->all(),
                'rows' => collect($this->normalizeList($form['rows'] ?? [], ['label', 'correct_column_id']))
                    ->values()
                    ->map(fn (array $row, int $index): array => [
                        'id' => $this->tableChecklistRowKey($index),
                        'label' => $row['label'],
                    ])
                    ->all(),
                'correct_cells' => collect($this->normalizeList($form['rows'] ?? [], ['label', 'correct_column_id']))
                    ->values()
                    ->map(function (array $row, int $index) use ($form): array {
                        $columnIndex = $this->resolveChecklistColumnIndex($form['columns'] ?? [], $row['correct_column_id']);

                        return [
                            'row_id' => $this->tableChecklistRowKey($index),
                            'column_id' => $this->tableChecklistColumnKey($columnIndex),
                        ];
                    })
                    ->all(),
            ],
            'image_text_matching' => [
                'options' => collect($this->normalizeList($form['pairs'] ?? [], ['question_label', 'answer_label']))
                    ->values()
                    ->map(fn (array $pair, int $index): array => [
                        'key' => $this->alphabetKey($index),
                        'label' => $pair['answer_label'],
                    ])
                    ->all(),
                'items' => collect($this->normalizeList($form['pairs'] ?? [], ['question_label', 'answer_label', 'image_url']))
                    ->values()
                    ->map(fn (array $pair, int $index): array => [
                        'key' => $this->numberKey($index),
                        'label' => $pair['question_label'],
                        'image_url' => $pair['image_url'] ?? '',
                        'alt' => $pair['question_label'],
                    ])
                    ->all(),
                'pairs' => collect($this->normalizeList($form['pairs'] ?? [], ['question_label', 'answer_label', 'image_url']))
                    ->values()
                    ->map(fn (array $pair, int $index): array => [
                        'item_key' => $this->numberKey($index),
                        'correct_option_key' => $this->alphabetKey($index),
                        'question_label' => $pair['question_label'],
                        'answer_label' => $pair['answer_label'],
                        'image_url' => $pair['image_url'] ?? '',
                    ])
                    ->all(),
            ],
        };
    }

    private function normalizeList(array $items, array $requiredKeys): array
    {
        return collect($items)
            ->map(function ($item) use ($requiredKeys) {
                $row = Arr::only((array) $item, $requiredKeys);

                return collect($row)
                    ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                    ->all();
            })
            ->filter(function (array $row) use ($requiredKeys): bool {
                foreach ($requiredKeys as $key) {
                    if (! array_key_exists($key, $row) || $row[$key] === null || $row[$key] === '') {
                        return false;
                    }
                }

                return true;
            })
            ->values()
            ->all();
    }

    private function blankRow(string $type, string $section): array
    {
        return match ($type.'.'.$section) {
            'essay.question' => ['question' => ''],
            'text_matching.pairs' => ['question_label' => '', 'answer_label' => ''],
            'table_checklist.columns' => ['label' => ''],
            'table_checklist.rows' => ['label' => '', 'correct_column_id' => ''],
            'image_text_matching.pairs' => ['question_label' => '', 'answer_label' => '', 'image_url' => ''],
            default => [],
        };
    }

    private function stepDefinitions(): array
    {
        return [
            'essay' => ['sort_order' => 1, 'answer_mode' => null],
            'text_matching' => ['sort_order' => 2, 'answer_mode' => null],
            'table_checklist' => ['sort_order' => 3, 'answer_mode' => 'single_answer_per_row'],
            'image_text_matching' => ['sort_order' => 4, 'answer_mode' => null],
        ];
    }

    private function alphabetKey(int $index): string
    {
        $index++;
        $key = '';

        while ($index > 0) {
            $index--;
            $key = chr(65 + ($index % 26)).$key;
            $index = intdiv($index, 26);
        }

        return $key;
    }

    public function displayAlphabetKey(int $index): string
    {
        return $this->alphabetKey($index);
    }

    private function numberKey(int $index): string
    {
        return (string) ($index + 1);
    }

    public function displayNumberKey(int $index): string
    {
        return $this->numberKey($index);
    }

    private function tableChecklistColumnKey(int $index): string
    {
        return $this->alphabetKey($index);
    }

    private function tableChecklistRowKey(int $index): string
    {
        return 'row_'.($index + 1);
    }

    private function resolveChecklistColumnIndex(array $columns, string $selectedKey): int
    {
        $generatedKeys = collect($columns)
            ->values()
            ->map(fn ($column, int $index): string => $this->tableChecklistColumnKey($index));

        $index = $generatedKeys->search($selectedKey);

        return $index === false ? 0 : (int) $index;
    }

    private function prepareImageTextMatchingForm(array $form): array
    {
        $pairs = collect($form['pairs'] ?? [])
            ->values()
            ->map(function (array $pair, int $index): array {
                $upload = $this->imageUploads[$index] ?? null;
                $imageUrl = $pair['image_url'] ?? '';

                if ($upload) {
                    $path = $upload->storePublicly('quiz-images', 'public');
                    $imageUrl = Storage::url($path);
                }

                return [
                    'question_label' => $pair['question_label'] ?? '',
                    'answer_label' => $pair['answer_label'] ?? '',
                    'image_url' => $imageUrl,
                ];
            })
            ->all();

        return [
            ...$form,
            'pairs' => $pairs,
        ];
    }
}
