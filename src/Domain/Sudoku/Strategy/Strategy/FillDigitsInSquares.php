<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Entity\Square;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Strategy\Strategy\Substrategy\FillDigitInSquare;

class FillDigitsInSquares extends Strategy
{
    private FillDigitInSquare $fillDigitInSquare;

    public function __construct()
    {
        $this->fillDigitInSquare = new FillDigitInSquare();
    }

    /**
     * @param Board $board
     * @return bool
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function execute(Board $board): bool
    {
        foreach ($board->squares() as $square) {
            if ($this->fillDigitsInSquare($square)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Square $square
     * @return bool
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    private function fillDigitsInSquare(Square $square)
    {
        for ($i = 1; $i <= 9; $i++) {
            if ($this->fillDigitInSquare->execute($i, $square)) {
                return true;
            }
        }

        return false;
    }
}
