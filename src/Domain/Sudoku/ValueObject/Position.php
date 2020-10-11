<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject;

use App\Domain\Sudoku\Exception\InvalidPosition;

class Position
{
    private int $row;
    private int $column;

    /**
     * Position constructor.
     * @param int $row
     * @param int $column
     * @throws InvalidPosition
     */
    public function __construct(int $row, int $column)
    {
        $this->checkPosition($row, $column);

        $this->row = $row;
        $this->column = $column;
    }

    public function row(): int
    {
        return $this->row;
    }

    public function column(): int
    {
        return $this->column;
    }

    public function __toString(): string
    {
        return sprintf('(%s,%s)', $this->row, $this->column);
    }

    /**
     * @param int $row
     * @param int $column
     * @throws InvalidPosition
     */
    private function checkPosition(int $row, int $column): void
    {
        if ($row < 1 || $row > 9 || $column < 1 || $column > 9) {
            throw new InvalidPosition($row, $column);
        }
    }
}
