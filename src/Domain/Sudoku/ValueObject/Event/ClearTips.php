<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject\Event;

use App\Domain\Sudoku\ValueObject\Position;

class ClearTips extends Event
{
    private Position $position;
    private ?int $digit;

    public function __construct(Position $position, ?int $digit = null)
    {
        $this->position = $position;
        $this->digit = $digit;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function digit(): ?int
    {
        return $this->digit;
    }
}
