<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Entity;

trait BoardGeneratorMethods
{
    private function generateCompletedBoard(): array
    {
        return [
            1 => [
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9,
            ],
            2 => [
                1 => 4,
                2 => 5,
                3 => 6,
                4 => 7,
                5 => 8,
                6 => 9,
                7 => 1,
                8 => 2,
                9 => 3,
            ],
            3 => [
                1 => 7,
                2 => 8,
                3 => 9,
                4 => 1,
                5 => 2,
                6 => 3,
                7 => 4,
                8 => 5,
                9 => 6,
            ],
            4 => [
                1 => 2,
                2 => 3,
                3 => 4,
                4 => 5,
                5 => 6,
                6 => 7,
                7 => 8,
                8 => 9,
                9 => 1,
            ],
            5 => [
                1 => 5,
                2 => 6,
                3 => 7,
                4 => 8,
                5 => 9,
                6 => 1,
                7 => 2,
                8 => 3,
                9 => 4,
            ],
            6 => [
                1 => 8,
                2 => 9,
                3 => 1,
                4 => 2,
                5 => 3,
                6 => 4,
                7 => 5,
                8 => 6,
                9 => 7,
            ],
            7 => [
                1 => 3,
                2 => 4,
                3 => 5,
                4 => 6,
                5 => 7,
                6 => 8,
                7 => 9,
                8 => 1,
                9 => 2,
            ],
            8 => [
                1 => 6,
                2 => 7,
                3 => 8,
                4 => 9,
                5 => 1,
                6 => 2,
                7 => 3,
                8 => 4,
                9 => 5,
            ],
            9 => [
                1 => 9,
                2 => 1,
                3 => 2,
                4 => 3,
                5 => 4,
                6 => 5,
                7 => 6,
                8 => 7,
                9 => 8,
            ]
        ];
    }

    private function generateCompletedBoardExceptOneCell(int $i, int $j): array
    {
        $data = $this->generateCompletedBoard();
        unset($data[$i][$j]);
        return $data;
    }

    private function generateCompletedBoardExceptSomeEmptyCells(array $emptyCells): array
    {
        $data = $this->generateCompletedBoard();
        foreach ($emptyCells as $emptyCell) {
            unset($data[$emptyCell[0]][$emptyCell[1]]);
        }
        return $data;
    }
}
