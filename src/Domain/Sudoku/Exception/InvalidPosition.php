<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Exception;

class InvalidPosition extends SudokuException
{
    private int $row;
    private int $column;

    public function __construct(int $row, int $column)
    {
        $this->row = $row;
        $this->column = $column;

        parent::__construct();
    }
}
