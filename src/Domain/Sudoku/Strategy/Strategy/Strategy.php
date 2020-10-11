<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Entity\Entity;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Entity\OneDimensionMatrix;
use App\Domain\Sudoku\Entity\Row;

abstract class Strategy extends Entity implements \App\Domain\Sudoku\Strategy\Strategy
{
    public function countExistingValues(array $values): int
    {
        return count(array_filter($values, function ($position) { return !is_null($position); }));
    }

    /**
     * @param Row $row
     * @return bool
     * @throws InvalidPosition
     */
    protected function fillLastCellInRow(Row $row): bool
    {
        return $this->fillLastCellInSet($row);
    }

    /**
     * @param Board $board
     * @return array|null
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     */
    protected function findLastEmptyCell(Board $board): ?array
    {
        $lastEmptyCell = null;

        for ($i = 1, $emptyCells = 0, $lastEmptyCell = null; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                if ($board->content($i, $j)->value() === null) {
                    $lastEmptyCell = [$i, $j];
                    $emptyCells++;
                }
                if ($emptyCells > 1) {
                    return null;
                }
            }
        }

        return $lastEmptyCell;
    }

    /**
     * @param OneDimensionMatrix $set
     * @return bool
     * @throws InvalidPosition
     */
    protected function fillLastCellInSet(OneDimensionMatrix $set): bool
    {
        $missingValues = $set->missingValues();

        for ($i = 1; $i <= 9; $i++) {
            if ($set->content($i)->value() === null) {
                $set->setFound($i, $missingValues[0]);
                return true;
            }
        }

        return false;
    }
}
