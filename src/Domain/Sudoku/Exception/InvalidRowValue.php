<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Exception;

class InvalidRowValue extends SudokuException
{
    private int $row;

    public function __construct(int $row)
    {
        $this->row = $row;

        parent::__construct();
    }
}
