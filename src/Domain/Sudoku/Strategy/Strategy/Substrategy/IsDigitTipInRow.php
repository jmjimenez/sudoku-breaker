<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Row;
use App\Domain\Sudoku\Exception\InvalidPosition;

class IsDigitTipInRow extends IsDigitTipInOneDimensionMatrix
{
    /**
     * @param int $digit
     * @param Row $row
     * @return bool
     * @throws InvalidPosition
     */
    public function execute(int $digit, $row): bool
    {
        return parent::execute($digit, $row);
    }
}
