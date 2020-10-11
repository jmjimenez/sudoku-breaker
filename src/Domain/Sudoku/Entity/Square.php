<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\CellContent\CellContent;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\CellContent\Found;
use App\Domain\Sudoku\ValueObject\Position;

class Square extends Entity
{
    /** @var Cell[][] */
    private array $cells;

    protected int $number;

    public function __construct(int $number)
    {
        $this->number = $number;

        $this->cells = [
            1 => [
                1 => null, 2 => null, 3 => null
            ],
            2 => [
                1 => null, 2 => null, 3 => null
            ],
            3 => [
                1 => null, 2 => null, 3 => null
            ],
        ];
    }

    public function number(): int
    {
        return $this->number;
    }

    /**
     * @return Cell[][]
     */
    public function cells(): array
    {
        return $this->cells;
    }

    /**
     * @return Cell[]
     */
    public function cellsAsSingleArray(): array
    {
        $cells = [];

        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $cells[] = $this->cells()[$i][$j];
            }
        }

        return $cells;
    }

    /**
     * @return array
     * @throws InvalidPosition
     */
    public function values(): array
    {
        $values = [];

        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                if ($this->content($i, $j)->value() !== null) {
                    $values[$this->content($i, $j)->value()] = new Position($i, $j);
                }
            }
        }

        return $values;
    }

    /**
     * @return array
     * @throws InvalidPosition
     */
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

    public function content(int $row, int $column): CellContent
    {
        return $this->cells[$row][$column]->content();
    }

    public function setCell(int $row, int $column, Cell $cell): void
    {
        $this->cells[$row][$column] = $cell;
    }

    /**
     * @param int $row
     * @param int $column
     * @param int $value
     * @throws InvalidPosition
     */
    public function setFound(int $row, int $column, int $value): void
    {
        $this->cells[$row][$column]->setContent(new Found($value));
    }

    public function isValid(): bool
    {
        $values = [];

        foreach ($this->cells as $row) {
            foreach ($row as $cell) {
                $content = $cell->content();
                if ($content->value() !== null) {
                    if (isset($values[$content->value()])) {
                        return false;
                    }
                    $values[$content->value()] = true;
                }
            }
        }

        return true;
    }

    public function isCompleted(): bool
    {
        $values = [];

        foreach ($this->cells as $row) {
            foreach ($row as $cell) {
                $content = $cell->content();
                if ($content->value() === null) {
                    return false;
                }
                $values[$content->value()] = true;
            }
        }

        return count($values) === 9;
    }

    /**
     * @param int $digit
     * @return Position|null
     * @throws InvalidPosition
     */
    public function findDigit(int $digit): ?Position
    {
        $values = $this->values();

        if (isset($values[$digit])) {
            return $values[$digit];
        }

        return null;
    }

    /**
     * @return array
     * @throws CellDoesNotAllowTips
     */
    public function tips(): array
    {
        $tips = [];

        foreach ($this->cells as $row => $cellsRow) {
            $tips[$row] = [];

            /** @var Cell $cell */
            foreach ($cellsRow as $column => $cell) {
                $tips[$row][$column] = $cell->isTipAllowed() ? $cell->tips() : [];
            }
        }

        return $tips;
    }

    /**
     * @param int|null $digit
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function clearTips(?int $digit = null): void
    {
        foreach ($this->cells as $row) {
            foreach ($row as $cell) {
                if ($cell->isTipAllowed()) {
                    $cell->clearTips($digit);
                }
            }
        }
    }

}
