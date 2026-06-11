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
        $pairs = collect($step->content_payload['pairs'] ?? []);
        $answers = collect($answer['answers'] ?? []);
        $total = max($pairs->count(), 1);
        $items = [];
        $correct = 0;

        foreach ($pairs as $pair) {
            $itemKey = $pair['item_key'];
            $selected = $answers->firstWhere('item_key', $itemKey)['selected_option_key'] ?? null;
            $isCorrect = $selected === $pair['correct_option_key'];
            $correct += $isCorrect ? 1 : 0;
            $items[] = [
                'item_key' => $itemKey,
                'selected_option_key' => $selected,
                'correct_option_key' => $pair['correct_option_key'],
                'is_correct' => $isCorrect,
            ];
        }

        return [
            'score' => round(($correct / $total) * 100, 2),
            'is_correct' => $correct === $total,
            'result_payload' => ['items' => $items, 'correct' => $correct, 'total' => $total],
        ];
    }

    private function gradeTableChecklist(QuizStep $step, array $answer): array
    {
        $answers = collect($answer['answers'] ?? []);
        $rowCounts = $answers->countBy('row_id');

        if ($rowCounts->contains(fn (int $count) => $count > 1)) {
            throw new InvalidArgumentException('Setiap baris hanya boleh memiliki satu jawaban.');
        }

        $rows = collect($step->content_payload['rows'] ?? []);
        $correctCells = collect($step->content_payload['correct_cells'] ?? []);
        $total = max($rows->count(), 1);
        $items = [];
        $correct = 0;

        foreach ($rows as $row) {
            $rowId = $row['id'];
            $selected = $answers->firstWhere('row_id', $rowId)['selected_column_id'] ?? null;
            $correctColumn = $correctCells->firstWhere('row_id', $rowId)['column_id'] ?? null;
            $isCorrect = (string) $selected === (string) $correctColumn;
            $correct += $isCorrect ? 1 : 0;
            $items[] = [
                'row_id' => $rowId,
                'selected_column_id' => $selected,
                'correct_column_id' => $correctColumn,
                'is_correct' => $isCorrect,
            ];
        }

        return [
            'score' => round(($correct / $total) * 100, 2),
            'is_correct' => $correct === $total,
            'result_payload' => ['items' => $items, 'correct' => $correct, 'total' => $total],
        ];
    }
}
