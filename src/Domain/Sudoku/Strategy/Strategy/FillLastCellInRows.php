<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\InvalidPosition;

class FillLastCellInRows extends Strategy
{
    /**
     * @param Board $board
     * @return bool
     * @throws InvalidPosition
     */
    public function execute(Board $board): bool
    {
        foreach ($board->rows() as $row) {
            $values = $row->values();

            if ($this->countExistingValues($values) == 8) {
                return $this->fillLastCellInRow($row);
            }
        }

        return false;
    }
}
