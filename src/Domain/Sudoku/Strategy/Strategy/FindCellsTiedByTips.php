<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Strategy\Strategy\Substrategy\FindCellsTiedByTipsInSquare;

class FindCellsTiedByTips extends Strategy
{
    private FindCellsTiedByTipsInSquare $findCellsTiedByTipsInSquare;

    public function __construct()
    {
        $this->findCellsTiedByTipsInSquare = new FindCellsTiedByTipsInSquare();
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
            if ($this->findCellsTiedByTipsInSquare->execute($square)) {
                return true;
            }
        }

        return false;
    }
}
