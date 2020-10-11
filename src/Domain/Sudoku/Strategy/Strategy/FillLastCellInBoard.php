<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;

class FillLastCellInBoard extends Strategy
{
    /**
     * @param Board $board
     * @return bool
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidPosition
     */
    public function execute(Board $board): bool
    {
        $lastEmptyCell = $this->findLastEmptyCell($board);

        if ($lastEmptyCell !== null) {
            return $this->fillLastCellInRow($board->row($lastEmptyCell[0]));
        }

        return false;
    }
}
