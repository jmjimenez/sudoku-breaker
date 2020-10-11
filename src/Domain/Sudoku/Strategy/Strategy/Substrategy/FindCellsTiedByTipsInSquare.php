<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Square;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;

class FindCellsTiedByTipsInSquare extends Substrategy
{
    /**
     * @param Square $square
     * @return bool
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function execute(Square $square): bool
    {
        foreach ($square->cellsAsSingleArray() as $cell) {
            if ($cell->value() !== null) {
                return false;
            }

            $tips = $cell->tips();
            $position = $cell->position();

            if (count($tips) < 2) {
                return false;
            }

            foreach ($square->cellsAsSingleArray() as $otherCell) {
                if ($otherCell->position() === $position) {
                    continue;
                }

                if ($otherCell->value() !== null) {
                    continue;
                }

                if (count($otherCell->tips()) < 2) {
                    continue;
                }

                $tipsMatches = $this->findTipsMatches($tips, $otherCell->tips());
                if (count($tipsMatches) == 0) {
                    continue;
                }

                foreach ($tipsMatches as $tipsMatch) {
                    if ($this->isValidTipMatchForSquare($tipsMatch, $square, [ $position, $otherCell->position() ])) {
                        $cell->setTied(...$tipsMatch);
                        $otherCell->setTied(...$tipsMatch);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function findTipsMatches(array $tipsA, array $tipsB): array
    {
        $matches = array_intersect($tipsA, $tipsB);

        if (count($matches) < 2) {
            return [];
        }

        if (count($matches) == 2) {
            return [ $matches ];
        }

        $matches = array_values($matches);

        $tipMatches = [];

        for ($i = 0; $i < count($matches); $i++) {
            for ($j = $i + 1; $j < count($matches); $j++) {
                $tipMatches[] = [ $matches[$i], $matches[$j] ];
            }
        }

        return $tipMatches;
    }

    /**
     * @param array $tips
     * @param Square $square
     * @param array $positions
     * @return bool
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    private function isValidTipMatchForSquare(array $tips, Square $square, array $positions): bool
    {
        foreach ($square->cellsAsSingleArray() as $cell) {
            if (in_array($cell->position(), $positions)) {
                continue;
            }

            if ($cell->value() !== null) {
                continue;
            }

            if (count(array_intersect($tips, $cell->tips())) !== 0) {
                return false;
            }
        }

        return true;
    }
}
