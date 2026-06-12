<?php

namespace App\Services;

use App\Models\QuizStep;
use InvalidArgumentException;

class QuizScoringService
{
    public function grade(QuizStep $step, array $answer): array
    {
        return match ($step->type) {
            'text_matching', 'image_text_matching' => $this->gradeMatching($step, $answer),
            'table_checklist' => $this->gradeTableChecklist($step, $answer),
            default => throw new InvalidArgumentException('Step ini tidak mendukung auto grading.'),
        };
    }

    private function gradeMatching(QuizStep $step, array $answer): array
    {
        $payload = $step->content_payload ?? [];
        $pairs = collect($payload['pairs'] ?? []);
        $answers = collect($answer['answers'] ?? []);
        $items = collect($payload['items'] ?? []);
        $options = collect($payload['options'] ?? []);
        $total = max($pairs->count(), 1);
        $results = [];
        $correct = 0;

        foreach ($pairs as $pair) {
            $itemKey = $pair['item_key'];
            $selected = $answers->firstWhere('item_key', $itemKey)['selected_option_key'] ?? null;
            $isCorrect = $selected === $pair['correct_option_key'];
            $item = $items->firstWhere('key', $itemKey) ?? [];
            $selectedOption = $options->firstWhere('key', $selected) ?? [];
            $correctOption = $options->firstWhere('key', $pair['correct_option_key']) ?? [];
            $correct += $isCorrect ? 1 : 0;
            $results[] = [
                'item_key' => $itemKey,
                'item_label' => $item['label'] ?? $itemKey,
                'selected_option_key' => $selected,
                'selected_option_label' => $selectedOption['label'] ?? null,
                'correct_option_key' => $pair['correct_option_key'],
                'correct_option_label' => $correctOption['label'] ?? null,
                'is_correct' => $isCorrect,
            ];
        }

        return [
            'score' => round(($correct / $total) * 100, 2),
            'is_correct' => $correct === $total,
            'result_payload' => ['items' => $results, 'correct' => $correct, 'total' => $total],
        ];
    }

    private function gradeTableChecklist(QuizStep $step, array $answer): array
    {
        $payload = $step->content_payload ?? [];
        $answers = collect($answer['answers'] ?? []);
        $rowCounts = $answers->countBy('row_id');

        if ($rowCounts->contains(fn (int $count) => $count > 1)) {
            throw new InvalidArgumentException('Setiap baris hanya boleh memiliki satu jawaban.');
        }

        $rows = collect($payload['rows'] ?? []);
        $columns = collect($payload['columns'] ?? []);
        $correctCells = collect($payload['correct_cells'] ?? []);
        $total = max($rows->count(), 1);
        $results = [];
        $correct = 0;

        foreach ($rows as $row) {
            $rowId = $row['id'];
            $selected = $answers->firstWhere('row_id', $rowId)['selected_column_id'] ?? null;
            $correctColumn = $correctCells->firstWhere('row_id', $rowId)['column_id'] ?? null;
            $selectedColumn = $columns->firstWhere('id', $selected) ?? [];
            $correctColumnItem = $columns->firstWhere('id', $correctColumn) ?? [];
            $isCorrect = (string) $selected === (string) $correctColumn;
            $correct += $isCorrect ? 1 : 0;
            $results[] = [
                'row_id' => $rowId,
                'row_label' => $row['label'] ?? $rowId,
                'selected_column_id' => $selected,
                'selected_column_label' => $selectedColumn['label'] ?? null,
                'correct_column_id' => $correctColumn,
                'correct_column_label' => $correctColumnItem['label'] ?? null,
                'is_correct' => $isCorrect,
            ];
        }

        return [
            'score' => round(($correct / $total) * 100, 2),
            'is_correct' => $correct === $total,
            'result_payload' => ['items' => $results, 'correct' => $correct, 'total' => $total],
        ];
    }
}
