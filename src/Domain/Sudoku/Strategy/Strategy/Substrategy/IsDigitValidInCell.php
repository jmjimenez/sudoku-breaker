<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Cell;
use App\Domain\Sudoku\Exception\InvalidPosition;

class IsDigitValidInCell extends Substrategy
{
    private IsDigitTipInRow $isDigitTipInRow;
    private IsDigitTipInColumn $isDigitTipInColumn;

    public function __construct()
    {
        $this->isDigitTipInColumn = new IsDigitTipInColumn();
        $this->isDigitTipInRow = new IsDigitTipInRow();
    }

    /**
     * @param int $digit
     * @param Cell $cell
     * @return bool
     * @throws InvalidPosition
     */
    public function execute(int $digit, Cell $cell): bool
    {
        if (!$cell->isEmpty()) {
            return false;
        }
        if ($cell->isTied() and !array_search($digit, $cell->ties())) {
            return false;
        }
        if ($cell->row()->findDigit($digit) !== null) {
            return false;
        }
        if ($cell->column()->findDigit($digit) !== null) {
            return false;
        }
        if ($cell->square()->findDigit($digit) !== null) {
            return false;
        }
        if ($this->isDigitTipInRow->execute($digit, $cell->row())) {
            return false;
        }
        if ($this->isDigitTipInColumn->execute($digit, $cell->column())) {
            return false;
        }

        return true;
    }
}
