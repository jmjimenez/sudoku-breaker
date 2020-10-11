<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Entity\Column;
use App\Domain\Sudoku\Exception\InvalidPosition;

class FillLastCellInColumns extends Strategy
{
    /**
     * @param Board $board
     * @return bool
     * @throws InvalidPosition
     */
    public function execute(Board $board): bool
    {
        foreach ($board->columns() as $column) {
            $values = $column->values();

            if ($this->countExistingValues($values) == 8) {
                return $this->fillLastCellInColumn($column);
            }
        }

        return false;
    }

    /**
     * @param Column $column
     * @return bool
     * @throws InvalidPosition
     */
    public function fillLastCellInColumn(Column $column): bool
    {
        return $this->fillLastCellInSet($column);
    }
}
