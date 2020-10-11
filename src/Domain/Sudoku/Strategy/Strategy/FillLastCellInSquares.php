<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Entity\Square;
use App\Domain\Sudoku\Exception\InvalidPosition;

class FillLastCellInSquares extends Strategy
{
    /**
     * @param Board $board
     * @return bool
     * @throws InvalidPosition
     */
    public function execute(Board $board): bool
    {
        foreach ($board->squares() as $square) {
            $values = $square->values();

            if ($this->countExistingValues($values) === 8) {
                return $this->fillLastCellInSquare($square);
            }
        }
        return false;
    }

    /**
     * @param Square $square
     * @return bool
     * @throws InvalidPosition
     */
    private function fillLastCellInSquare(Square $square): bool
    {
        $missingValues = $square->missingValues();

        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                if ($square->content($i, $j)->value() === null) {
                    $square->setFound($i, $j, $missingValues[0]);
                    return true;
                }
            }
        }

        return false;
    }
}
