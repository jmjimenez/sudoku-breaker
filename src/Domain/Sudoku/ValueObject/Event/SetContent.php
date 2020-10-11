<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject\Event;

use App\Domain\Sudoku\ValueObject\CellContent\CellContent;
use App\Domain\Sudoku\ValueObject\Position;

class SetContent extends Event
{
    private Position $position;
    private CellContent $content;

    public function __construct(Position $position, CellContent $content)
    {
        $this->position = $position;
        $this->content = $content;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function content(): CellContent
    {
        return $this->content;
    }
}
