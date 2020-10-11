<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Exception;

class InvalidValue extends SudokuException
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;

        parent::__construct();
    }
}
