<?php declare(strict_types=1);

namespace App\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\CellContent\CellContent;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Found;
use App\Domain\Sudoku\ValueObject\CellContent\Nothing;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\ValueObject\Status;

class Board extends Entity
{
    /** @var Cell[][] */
    private array $cells;
    /** @var Row[] */
    private array $rows;
    /** @var Column[] */
    private array $columns;
    /** @var Square[] */
    private array $squares;

    public function __construct()
    {
        $this->initializeCells();
        $this->initializeRows();
        $this->initializeColumns();
        $this->initializeSquares();
    }

    /**
     * @param array $data
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function load(array $data): void
    {
        foreach ($data as $rowNumber => $rowData) {
            foreach ($rowData as $colNumber => $colValue) {
                $this->setFixed($rowNumber, $colNumber, $colValue);
            }
        }
    }

    /**
     * @return Row[]
     */
    public function rows(): array
    {
        return $this->rows;
    }

    public function row(int $rowNumber): Row
    {
        return $this->rows[$rowNumber];
    }

    /**
     * @return Column[]
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * @return Square[]
     */
    public function squares(): array
    {
        return $this->squares;
    }

    /**
     * @param int $row
     * @param int $col
     * @param int $value
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function setFixed(int $row, int $col, int $value): void
    {
        $this->checkRow($row);
        $this->checkColumn($col);
        $this->checkValue($value);

        $this->cells[$row][$col]->setContent(new Fixed($value));
    }

    /**
     * @param int $row
     * @param int $col
     * @param int $value
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function setFound(int $row, int $col, int $value): void
    {
        $this->checkRow($row);
        $this->checkColumn($col);
        $this->checkValue($value);

        $this->cells[$row][$col]->setContent(new Found($value));
    }

    /**
     * @return Cell[][]
     */
    public function cells(): array
    {
        return $this->cells;
    }

    /**
     * @param int $row
     * @param int $col
     * @return CellContent
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     */
    public function content(int $row, int $col): CellContent
    {
        $this->checkRow($row);
        $this->checkColumn($col);

        return $this->cells[$row][$col]->content();
    }

    /**
     * @param int $row
     * @param int $col
     * @return int|null
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     */
    public function value(int $row, int $col): ?int
    {
        $this->checkRow($row);
        $this->checkColumn($col);

        return $this->cells[$row][$col]->value();
    }

    /**
     * @param int $digit
     * @return int
     * @throws InvalidPosition
     */
    public function countDigit(int $digit): int
    {
        $count = 0;

        foreach ($this->squares() as $square) {
            if ($square->findDigit($digit) !== null) {
                $count++;
            }
        }

        return $count;
    }

    public function status(): Status
    {
        $isValid = $this->isValid();
        $isCompleted = $isValid ? $this->isCompleted() : false;
        return new Status($isValid, $isCompleted);
    }

    /**
     * @param int $value
     * @throws InvalidValue
     */
    private function checkValue(int $value): void
    {
        if ($value < 1 || $value > 9) {
            throw new InvalidValue($value);

        }
    }

    /**
     * @param int $row
     * @throws InvalidRowValue
     */
    private function checkRow(int $row): void
    {
        if ($row < 1 || $row > 9) {
            throw new InvalidRowValue($row);
        }
    }

    /**
     * @param int $column
     * @throws InvalidColumnValue
     */
    private function checkColumn(int $column): void
    {
        if ($column < 1 || $column > 9) {
            throw new InvalidColumnValue($column);
        }
    }

    private function isValid(): bool
    {
        foreach ($this->rows() as $row) {
            if (!$row->isValid()) {
                return false;
            }
        }

        foreach ($this->columns() as $column) {
            if (!$column->isValid()) {
                return false;
            }
        }

        foreach ($this->squares() as $square) {
            if (!$square->isValid()) {
                return false;
            }
        }

        return true;
    }

    private function isCompleted(): bool
    {
        foreach ($this->rows() as $row) {
            if (!$row->isCompleted()) {
                return false;
            }
        }

        foreach ($this->columns() as $column) {
            if (!$column->isCompleted()) {
                return false;
            }
        }

        foreach ($this->squares() as $square) {
            if (!$square->isCompleted()) {
                return false;
            }
        }

        return true;
    }

    private function initializeCells(): void
    {
        $this->cells = [];

        for ($i = 1; $i <= 9; $i++) {
            $this->cells[$i] = [];

            for ($j = 1; $j <= 9; $j++) {
                $this->cells[$i][$j] = new Cell(new Nothing());
            }
        }
    }

    private function initializeRows(): void
    {
        $this->rows = [];

        for ($i = 1; $i <= 9; $i++) {
            $row = new Row($i);

            foreach ($this->cells[$i] as $column => $cell) {
                $row->setCell($column, $cell);
                $cell->setRow($row);
            }

            $this->rows[$i] = $row;
        }
    }

    private function initializeColumns(): void
    {
        $this->columns = [];

        for ($j = 1; $j <= 9; $j++) {
            $column = new Column($j);

            for ($i = 1; $i <= 9; $i++) {
                $column->setCell($i, $this->cells[$i][$j]);
                $this->cells[$i][$j]->setColumn($column);
            }

            $this->columns[$j] = $column;
        }
    }

    private function initializeSquares(): void
    {
        $this->squares = [];

        $this->squares[1] = $this->createSquare(1, 1,3,1,3);
        $this->squares[2] = $this->createSquare(2, 1,3,4,6);
        $this->squares[3] = $this->createSquare(3, 1,3,7,9);

        $this->squares[4] = $this->createSquare(4, 4,6,1,3);
        $this->squares[5] = $this->createSquare(5, 4,6,4,6);
        $this->squares[6] = $this->createSquare(6, 4,6,7,9);

        $this->squares[7] = $this->createSquare(7, 7,9,1,3);
        $this->squares[8] = $this->createSquare(8, 7,9,4,6);
        $this->squares[9] = $this->createSquare(9, 7,9,7,9);
    }

    private function createSquare(int $squareNumber, int $fromRow, int $toRow, int $fromColumn, int $toColumn): Square
    {
        $square = new Square($squareNumber);
        for ($row = 1, $i = $fromRow; $i <= $toRow; $i++, $row++) {
            for ($col = 1, $j = $fromColumn; $j <= $toColumn; $j++, $col++) {
                $square->setCell($row, $col, $this->cells[$i][$j]);
                $this->cells[$i][$j]->setSquare($square);
            }
        }

        return $square;
    }
}
