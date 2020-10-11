<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject;

class Status
{
    private bool $isValid;
    private bool $isCompleted;

    public function __construct(bool $isValid, bool $isCompleted)
    {
        $this->isValid = $isValid;
        $this->isCompleted = $isCompleted;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }
}
