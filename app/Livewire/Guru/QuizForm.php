<?php

namespace App\Livewire\Guru;

use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizStep;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guru')]
class QuizForm extends Component
{
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
                'instruction' => 'Pasangkan objek visual dengan kategori yang tepat.',
                'options' => [
                    $this->blankRow('image_text_matching', 'options'),
                    $this->blankRow('image_text_matching', 'options'),
                ],
                'items' => [
                    $this->blankRow('image_text_matching', 'items'),
                    $this->blankRow('image_text_matching', 'items'),
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

        $this->stepForms['image_text_matching']['options'] = collect($payload['options'] ?? [])
            ->map(fn (array $row): array => [
                'key' => $row['key'] ?? '',
                'label' => $row['label'] ?? '',
            ])
            ->whenEmpty(fn () => collect([$this->blankRow('image_text_matching', 'options')]))
            ->all();

        $this->stepForms['image_text_matching']['items'] = collect($payload['items'] ?? [])
            ->map(function (array $row) use ($payload): array {
                $pair = collect($payload['pairs'] ?? [])->firstWhere('item_key', $row['key'] ?? null);

                return [
                    'key' => $row['key'] ?? '',
                    'label' => $row['label'] ?? '',
                    'image_url' => $row['image_url'] ?? '',
                    'alt' => $row['alt'] ?? '',
                    'correct_option_key' => $pair['correct_option_key'] ?? '',
                ];
            })
            ->whenEmpty(fn () => collect([$this->blankRow('image_text_matching', 'items')]))
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
            'stepForms.image_text_matching.options' => ['array'],
            'stepForms.image_text_matching.options.*.label' => ['required', 'string'],
            'stepForms.image_text_matching.items' => ['array'],
            'stepForms.image_text_matching.items.*.label' => ['required', 'string'],
            'stepForms.image_text_matching.items.*.image_url' => ['required', 'url'],
            'stepForms.image_text_matching.items.*.alt' => ['required', 'string'],
            'stepForms.image_text_matching.items.*.correct_option_key' => ['required', 'string'],
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
                'options' => collect($this->normalizeList($form['options'] ?? [], ['label']))
                    ->values()
                    ->map(fn (array $option, int $index): array => [
                        'key' => $this->alphabetKey($index),
                        'label' => $option['label'],
                    ])
                    ->all(),
                'items' => collect($this->normalizeList($form['items'] ?? [], ['label', 'image_url', 'alt', 'correct_option_key']))
                    ->values()
                    ->map(fn (array $item, int $index): array => [
                        'key' => $this->numberKey($index),
                        'label' => $item['label'],
                        'image_url' => $item['image_url'],
                        'alt' => $item['alt'],
                    ])
                    ->all(),
                'pairs' => collect($this->normalizeList($form['items'] ?? [], ['label', 'image_url', 'alt', 'correct_option_key']))
                    ->values()
                    ->map(fn (array $item, int $index): array => [
                        'item_key' => $this->numberKey($index),
                        'correct_option_key' => $item['correct_option_key'],
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
            'image_text_matching.options' => ['label' => ''],
            'image_text_matching.items' => ['label' => '', 'image_url' => '', 'alt' => '', 'correct_option_key' => ''],
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
}
