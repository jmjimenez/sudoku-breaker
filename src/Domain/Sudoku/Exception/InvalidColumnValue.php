<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Exception;

class InvalidColumnValue extends SudokuException
{
    private int $column;

    public function __construct(int $column)
    {
        $this->column = $column;

        parent::__construct();
    }
}
