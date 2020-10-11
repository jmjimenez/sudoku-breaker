<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy;

use App\Domain\Sudoku\Entity\Board;

interface Strategy
{
    public function execute(Board $board): bool;
}
