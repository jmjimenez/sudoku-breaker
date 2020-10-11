<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Cell;
use App\Domain\Sudoku\Entity\OneDimensionMatrix;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;

abstract class IsDigitTipInOneDimensionMatrix extends Substrategy
{
    /**
     * @param int $digit
     * @param OneDimensionMatrix $oneDimensionMatrix
     * @return bool
     * @throws InvalidPosition
     */
    public function execute(int $digit, $oneDimensionMatrix): bool
    {
        $cells = $oneDimensionMatrix->cells();

        return $this->findDigitTipInCells($digit, $cells[1], $cells[2], $cells[3])
            || $this->findDigitTipInCells($digit, $cells[4], $cells[5], $cells[6])
            || $this->findDigitTipInCells($digit, $cells[7], $cells[8], $cells[9]);
    }

    /**
     * @param int $digit
     * @param Cell ...$cells
     * @return bool
     * @throws InvalidPosition
     */
    public function findDigitTipInCells(int $digit, Cell ...$cells): bool
    {
        $count = 0;
        $positions = [];
        foreach ($cells as $cell) {
            try {
                $count += $cell->findTip($digit) ? 1 : 0;
            } catch (CellDoesNotAllowTips $exception) {
                // do nothing
            }
            $positions[] = $cell->position();
        }

        if ($count < 2) {
            return false;
        }

        foreach ($cells[0]->square()->cellsAsSingleArray() as $cell) {
            try {
                if ($cell->findTip($digit)) {
                    if (!in_array($cell->position(), $positions)) {
                        return false;
                    }
                }
            } catch (CellDoesNotAllowTips $exception) {
                // do nothing
            }
        }

        return true;
    }
}
