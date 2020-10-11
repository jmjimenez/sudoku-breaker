<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject\CellContent;

class Tied extends CellContent
{
    private array $ties;

    public function __construct(int ...$ties)
    {
        $this->ties = $ties;
    }

    public function ties(): array
    {
        return $this->ties;
    }
}
