<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;

class FillLastDigitInBoard extends Strategy
{
    /**
     * @param Board $board
     * @return bool
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function execute(Board $board): bool
    {
        for ($i = 1; $i <= 9; $i++) {
            if ($board->countDigit($i) == 8) {
                return $this->fillLastDigit($board, $i);
            }
        }
        return false;
    }

    /**
     * @param Board $board
     * @param int $digit
     * @return bool
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    private function fillLastDigit(Board $board, int $digit): bool
    {
        $rowNumber = null;
        $colNumber = null;

        foreach ($board->rows() as $row) {
            if ($row->findDigit($digit) === null) {
                $rowNumber = $row->number();
                break;
            }
        }

        foreach ($board->columns() as $column) {
            if ($column->findDigit($digit) === null) {
                $colNumber = $column->number();
            }
        }

        if ($rowNumber === null || $colNumber === null) {
            return false;
        }

        $board->setFound($rowNumber, $colNumber, $digit);

        return true;
    }
}
