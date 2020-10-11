<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Entity;

use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\CellContent\CellContent;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Found;

abstract class OneDimensionMatrix extends Entity
{
    /** @var Cell[] */
    private array $cells;

    private int $number;

    public function __construct(int $number)
    {
        $this->number = $number;

        $this->cells = [];

        for ($i = 1; $i <= 9; $i++) {
            $this->cells[$i] = null;
        }
    }

    public function number(): int
    {
        return $this->number;
    }

    /**
     * @return Cell[]
     */
    public function cells(): array
    {
        return $this->cells;
    }

    public function content(int $index): CellContent
    {
        return $this->cells[$index]->content();
    }

    public function setCell(int $index, Cell $cell): void
    {
        $this->cells[$index] = $cell;
    }

    public function isValid(): bool
    {
        $values = [];

        foreach ($this->cells as $cell) {
            $content = $cell->content();
            if ($content->value() !== null) {
                if (isset($values[$content->value()])) {
                    return false;
                }
                $values[$content->value()] = true;
            }
        }

        return true;
    }

    public function isCompleted(): bool
    {
        $values = [];

        foreach ($this->cells as $cell) {
            $content = $cell->content();
            if ($content->value() === null) {
                return false;
            }
            $values[$content->value()] = true;
        }

        return count($values) === 9;
    }

    public function values(): array
    {
        $values = [];

        for ($i = 1; $i <= 9; $i++) {
            if ($this->content($i)->value() !== null) {
                $values[$this->content($i)->value()] = $i;
            }

        }

        return $values;
    }

    public function missingValues(): array
    {
        $missingValues = [];
        $values = $this->values();

        for ($i = 1; $i <= 9; $i++) {
            if (!isset($values[$i])) {
                $missingValues[] = $i;
            }
        }

        return $missingValues;
    }

    /**
     * @param int $index
     * @param int $value
     * @throws InvalidPosition
     */
    public function setFixed(int $index, int $value): void
    {
        $this->cells[$index]->setContent(new Fixed($value));
    }

    /**
     * @param int $index
     * @param int $value
     * @throws InvalidPosition
     */
    public function setFound(int $index, int $value): void
    {
        $this->cells[$index]->setContent(new Found($value));
    }

    public function findDigit(int $digit): ?int
    {
        $values = $this->values();

        if (isset($values[$digit])) {
            return $values[$digit];
        }

        return null;
    }

    /**
     * @return array
     */
    public function squares(): array
    {
        $squares = [];

        foreach ($this->cells as $cell) {
            $squares[$cell->square()->number()] = $cell->square();
        }

        return $squares;
    }
}
