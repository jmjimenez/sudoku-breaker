<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Cell;
use App\Domain\Sudoku\Entity\Square;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\CellContent\Found;

class FillDigitInSquare extends Substrategy
{
    private IsDigitValidInCell $isValidDigitInCell;

    public function __construct()
    {
        $this->isValidDigitInCell = new IsDigitValidInCell();
    }

    /**
     * @param int $digit
     * @param Square $square
     * @return bool
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function execute(int $digit, Square $square): bool
    {
        if ($square->findDigit($digit)) {
            return false;
        }

        $oldTips = $square->tips();

        $square->clearTips($digit);

        $possibleCells = [];

        foreach ($square->cellsAsSingleArray() as $cell) {
            if ($this->isValidDigitInCell->execute($digit, $cell)) {
                $possibleCells[] = $cell;
            }
        }

        if (count($possibleCells) == 1) {
            $possibleCells[0]->setContent(new Found($digit));
            return true;
        }

        if (count($possibleCells) >= 2) {
            /** @var Cell $cell */
            foreach ($possibleCells as $cell) {
                $cell->addTip($digit);
            }

            return $oldTips != $square->tips();
        }

        return false;
    }
}
