<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Strategy;

interface StrategiesLoader
{
    /**
     * @return Strategy[]
     */
    public function load(): array;
}
