<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Application;

class SolveSudoku
{
    /** @var int[][] */
    private array $data;
    private int $maxIterations;
    private bool $returnSteps;

    public function __construct(
        array $data,
        int $maxIterations,
        bool $returnSteps
    ) {
        $this->data = $data;
        $this->maxIterations = $maxIterations;
        $this->returnSteps = $returnSteps;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function maxIterations(): int
    {
        return $this->maxIterations;
    }

    public function returnSteps(): bool
    {
        return $this->returnSteps;
    }
}
