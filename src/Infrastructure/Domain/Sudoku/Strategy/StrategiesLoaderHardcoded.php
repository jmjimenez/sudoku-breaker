<?php declare(strict_types=1);

namespace App\Infrastructure\Domain\Sudoku\Strategy;

use App\Domain\Sudoku\Strategy\Strategy\FillDigitsInSquares;
use App\Domain\Sudoku\Strategy\Strategy\FillLastCellInBoard;
use App\Domain\Sudoku\Strategy\Strategy\FillLastCellInColumns;
use App\Domain\Sudoku\Strategy\Strategy\FillLastCellInRows;
use App\Domain\Sudoku\Strategy\Strategy\FillLastCellInSquares;
use App\Domain\Sudoku\Strategy\Strategy\FillLastDigitInBoard;
use App\Domain\Sudoku\Strategy\StrategiesLoader;
use App\Domain\Sudoku\Strategy\Strategy\FindCellsTiedByTips;

class StrategiesLoaderHardcoded implements StrategiesLoader
{
    /** @inheritDoc */
    public function load(): array
    {
        return [
            new FillLastCellInBoard(),
            new FillLastCellInRows(),
            new FillLastCellInColumns(),
            new FillLastCellInSquares(),
            new FillLastDigitInBoard(),
            new FindCellsTiedByTips(),
            new FillDigitsInSquares(),
        ];
    }
}
