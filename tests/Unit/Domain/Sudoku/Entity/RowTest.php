<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Entity;

use App\Domain\Sudoku\Entity\Column;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\ValueObject\CellContent\Found;
use App\Domain\Sudoku\ValueObject\CellContent\Tied;
use App\Domain\Sudoku\Entity\Cell;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Nothing;
use App\Domain\Sudoku\Entity\Row;
use App\Domain\Sudoku\Entity\Square;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    private Row $row;
    private int $rowNumber = 1;

    protected function setUp(): void
    {
        $this->row = new Row($this->rowNumber);
    }

    public function testNumber(): void
    {
        $this->assertEquals($this->rowNumber, $this->row->number());
    }

    public function testContent(): void
    {
        for ($i = 1; $i <= 9; $i++) {
            $this->row->setCell($i, new Cell(new Fixed($i)));
        }

        for ($i = 1; $i <= 9; $i++) {
            $this->assertEquals(new Fixed($i), $this->row->content($i));
        }
    }

    public function testSetCell(): void
    {
        $cellsContent = [
            1 => new Fixed(1),
            2 => new Found(2),
            3 => new Nothing(),
            4 => new Tied(),
            5 => new Fixed(1),
            6 => new Found(2),
            7 => new Nothing(),
            8 => new Tied(),
            9 => new Nothing(),
        ];

        foreach ($cellsContent as $index => $cellContent) {
            $this->row->setCell($index, new Cell($cellContent));
        }

        foreach ($cellsContent as $index => $cellContent) {
            $this->assertEquals($cellContent, $this->row->content($index));
        }
    }

    /**
     * @param array $data
     *
     * @param bool $isValid
     *
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(array $data, bool $isValid): void
    {
        $this->initializeRow($data);

        $this->assertEquals($isValid, $this->row->isValid());
    }

    public function isValidDataProvider(): array
    {
        return [
            [ [1,2,3,4,5,6,7,8,9], true ],
            [ [1,null,3,4,5,6,7,8,9], true ],
            [ [1,null,null,null,null,1,null,null,null], false ]
        ];
    }

    /**
     * @param array $data
     * @param bool $isCompleted
     *
     * @dataProvider isCompletedDataProvider
     */
    public function testIsCompleted(array $data, bool $isCompleted): void
    {
        $this->initializeRow($data);

        $this->assertEquals($isCompleted, $this->row->isCompleted());
    }

    /**
     * @param array $data
     * @param array $values
     *
     * @dataProvider valuesDataProvider
     */
    public function testValues(array $data, array $values): void
    {
        $this->initializeRow($data);

        $this->assertEquals($values, $this->row->values());
    }

    public function valuesDataProvider(): array
    {
        return [
            [ [1,2,3,4,5,6,7,8,9], [ 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9] ],
            [ [1,9,3,null,null,null,null,null,null], [ 1 => 1, 3 => 3, 9 => 2] ],
            [ [1,null,null,null,null,null,null,null,2], [ 1 => 1, 2 => 9] ],
            [ [null,null,null,null,null,null,null,null,null], [ ] ],
        ];
    }

    /**
     * @param array $data
     * @param array $missingValues
     *
     * @dataProvider missingValuesDataProvider
     */
    public function testMissingValues(array $data, array $missingValues): void
    {
        $this->initializeRow($data);

        $this->assertEquals($missingValues, $this->row->missingValues());
    }

    public function missingValuesDataProvider(): array
    {
        return [
            [[1,2,3,4,5,6,7,8,9],[]],
            [[9,8,7,6,5,4,3,2,1],[]],
            [[1,null,null,null,null,3,null,null,null],[2,4,5,6,7,8,9]],
            [[null,null,null,null,null,null,null,null,null],[1,2,3,4,5,6,7,8,9]],
        ];
    }

    /**
     * @throws InvalidPosition
     */
    public function testSetFixed(): void
    {
        $this->initializeRow([null,null,null,null,null,null,null,null,null]);
        $this->row->setFixed(1, 1);
        $this->assertEquals(1, $this->row->content(1)->value());
    }

    /**
     * @param array $data
     * @param int $digit
     * @param int|null $position
     *
     * @dataProvider findDigitDataProvider
     */
    public function testFindDigit(array $data, int $digit, ?int $position): void
    {
        $this->initializeRow($data);
        $this->assertEquals($position, $this->row->findDigit($digit));
    }

    public function findDigitDataProvider(): array
    {
        return [
            [[1,2,3,4,5,6,7,8,9], 5, 5],
            [[1,null,null,null,null,null,null,null,null], 5, null],
        ];
    }

    public function testSquares(): void
    {
        $squares = [
            1=> new Square(1),
            2 => new Square(2),
            3 => new Square(3),
        ];

        $cells = [
            new Cell(new Nothing()),
            new Cell(new Nothing()),
            new Cell(new Nothing()),
            new Cell(new Nothing()),
            new Cell(new Nothing()),
            new Cell(new Nothing()),
            new Cell(new Nothing()),
            new Cell(new Nothing()),
            new Cell(new Nothing()),
        ];

        foreach ($cells as $index => $cell) {
            $cell->setSquare($squares[$index % 3 + 1]);
            $cell->setRow($this->row);
            $this->row->setCell($index + 1, $cell);
        }

        $this->assertEquals($squares, $this->row->squares());
    }

    private function initializeRow(array $data): void
    {
        $column = new Column(1);

        for ($i = 0; $i < 9; $i++) {
            $cell = new Cell($data[$i] === null ? new Nothing(): new Fixed($data[$i]));
            $cell->setRow($this->row);
            $cell->setColumn($column);
            $this->row->setCell( $i+1, $cell);
        }
    }

    public function isCompletedDataProvider(): array
    {
        return [
            [ [1,2,3,4,5,6,7,8,9], true ],
            [ [1,null,3,4,5,6,7,8,9], false ],
        ];
    }
}
