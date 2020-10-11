<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject\CellContent;

class Fixed extends CellContent
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
}
