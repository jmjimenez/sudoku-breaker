<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject\CellContent;

abstract class CellContent
{
    public function value(): ?int
    {
        return null;
    }
}
