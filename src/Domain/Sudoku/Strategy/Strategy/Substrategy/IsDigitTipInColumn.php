<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Column;
use App\Domain\Sudoku\Exception\InvalidPosition;

class IsDigitTipInColumn extends IsDigitTipInOneDimensionMatrix
{
    /**
     * @param int $digit
     * @param Column $column
     * @return bool
     * @throws InvalidPosition
     */
    public function execute(int $digit, $column): bool
    {
        return parent::execute($digit, $column);
    }
}
