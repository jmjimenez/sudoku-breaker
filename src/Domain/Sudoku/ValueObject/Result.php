<?php declare(strict_types=1);

namespace App\Domain\Sudoku\ValueObject;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidRowValue;

class Result
{
    private array $cells;
    private array $events;

    /**
     * Result constructor.
     * @param Board $board
     * @param array $events
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     */
    public function __construct(Board $board, array $events)
    {
        for ($row = 1; $row <= 9; $row++) {
            $this->cells[$row] = [];
            for ($column = 1; $column <= 9; $column++) {
                $this->cells[$row][$column] = $board->content($row, $column)->value();
            }
        }

        $this->events = $events;
    }

    public function cell(int $row, int $column): ?int
    {
        return $this->cells[$row][$column] ?? null;
    }

    public function steps(): array
    {
        return $this->events;
    }

    public function toArray()
    {
        $array = [];

        for ($row = 1; $row <= 9; $row++) {
            for ($column = 1; $column <= 9; $column++) {
                if ($this->cell($row, $column) !== null) {
                    if (!isset($array[$row])) {
                        $array[$row] = [];
                    }
                    $array[$row][$column] = $this->cell($row, $column);
                }
            }
        }

        return $array;
    }
}
