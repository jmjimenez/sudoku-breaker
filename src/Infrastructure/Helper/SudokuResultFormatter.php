<?php declare(strict_types=1);

namespace App\Infrastructure\Helper;

use App\Domain\Sudoku\ValueObject\Result;

class SudokuResultFormatter
{
    public function format(Result $result, bool $includeSteps): string
    {
        $formatedResult = [
            'board' => [
            ]
        ];
        for ($row = 1; $row <= 9; $row++) {
            for ($column = 1; $column <= 9; $column++) {
                if (($value = $result->cell($row, $column)) !== null) {
                    $formatedResult['board'][] = [
                        'row' => $row,
                        'column' => $column,
                        'value' => $value
                    ];
                }
            }
        }

        if ($includeSteps) {
            $formatedResult['steps'] = $result->steps();
        }

        return json_encode($formatedResult);
    }
}
