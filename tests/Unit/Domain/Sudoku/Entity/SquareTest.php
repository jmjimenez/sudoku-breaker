<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Entity;

use App\Domain\Sudoku\Entity\Cell;
use App\Domain\Sudoku\Entity\Column;
use App\Domain\Sudoku\Entity\Row;
use App\Domain\Sudoku\ValueObject\CellContent\CellContent;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Found;
use App\Domain\Sudoku\ValueObject\CellContent\Nothing;
use App\Domain\Sudoku\ValueObject\Position;
use App\Domain\Sudoku\Entity\Square;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;
use PHPUnit\Framework\TestCase;

class SquareTest extends TestCase
{
    private Column $column;
    private Row $row;
    private Square $square;
    private int $squareNumber = 1;

    protected function setUp(): void
    {
        $this->squareNumber;
        $this->square = new Square($this->squareNumber);
        $this->row = new Row(1);
        $this->column = new Column(1);
    }

    public function testNumber(): void
    {
        $this->assertEquals($this->squareNumber, $this->square->number());
    }

    public function testCells(): void
    {
        $data = [ [1,null,3], [4,5,6], [7,8,null] ];
        $this->initializeSquare($data);
        $cells = $this->square->cells();

        foreach ($data as $i => $dataRow) {
            foreach ($dataRow as $j => $cellValue) {
                if ($cellValue === null) {
                    $this->assertEquals($this->generateCell(new Nothing()), $cells[$i+1][$j+1]);
                } else {
                    $this->assertEquals($this->generateCell(new Fixed($cellValue)), $cells[$i+1][$j+1]);
                }
            }
        }
    }

    /**
     * @param array $data
     * @param array $values
     *
     * @throws InvalidPosition
     * @dataProvider valuesDataProvider
     */
    public function testValues(array $data, array $values): void
    {
        $this->initializeSquare($data);

        $this->assertEquals($values, $this->square->values());
    }

    /**
     * @return array|array[]
     * @throws InvalidPosition
     */
    public function valuesDataProvider(): array
    {
        return [
            [
                [ [1,2,3], [4,5,6], [7,8,9] ],
                [
                    1=>new Position(1,1),
                    2=>new Position(1,2),
                    3=>new Position(1,3),
                    4=>new Position(2,1),
                    5=>new Position(2,2),
                    6=>new Position(2,3),
                    7=>new Position(3,1),
                    8=>new Position(3,2),
                    9=>new Position(3,3)
                ]
            ],
            [
                [ [1,null,3], [4,5,null], [null,8,9] ],
                [
                    1=>new Position(1,1),
                    3=>new Position(1,3),
                    4=>new Position(2,1),
                    5=>new Position(2,2),
                    8=>new Position(3,2),
                    9=>new Position(3,3)
                ]
            ],
        ];
    }

    /**
     * @param array $data
     * @param array $missingValues
     *
     * @throws InvalidPosition
     * @dataProvider missingValuesDataProvider
     */
    public function testMissingValues(array $data, array $missingValues): void
    {
        $this->initializeSquare($data);
        $this->assertEquals($missingValues, $this->square->missingValues());
    }

    public function missingValuesDataProvider(): array
    {
        return [
            [
                [ [1,2,3], [4,5,6], [7,8,9] ],
                []
            ],
            [
                [ [1,null,3], [4,5,6], [7,null,9] ],
                [ 2, 8]
            ],
            [
                [ [3,null,1], [null,6,5], [9,null,7] ],
                [ 2, 4, 8]
            ],
        ];
    }

    public function testContent(): void
    {
        $cell = new Cell(new Fixed(3));
        $this->square->setCell(1, 1, $cell);
        $this->assertEquals(new Fixed(3), $this->square->content(1, 1));
    }

    public function testSetCell(): void
    {
        $testDigit = 2;
        $testCell = new Cell(new Fixed($testDigit));
        $testRow = 1;
        $testColumn = 2;

        $this->initializeSquare([ [1,null,3], [4,5,6], [null,8,9] ]);

        $this->square->setCell($testRow, $testColumn, $testCell);
        $this->assertEquals($testCell, $this->square->cells()[$testRow][$testColumn]);
    }

    /**
     * @throws InvalidPosition
     */
    public function testSetFound(): void
    {
        $testDigit = 2;
        $testRow = 1;
        $testColumn = 2;

        $this->initializeSquare([ [1,null,3], [4,5,6], [null,8,9] ]);

        $this->square->setFound($testRow, $testColumn, $testDigit);
        $this->assertInstanceOf(Found::class , $this->square->content($testRow, $testColumn));
        $this->assertEquals($testDigit , $this->square->content($testRow, $testColumn)->value());
    }

    /**
     * @param array $matrix
     *
     * @param bool $isValid
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(array $matrix, bool $isValid): void
    {
        $this->initializeSquare($matrix);
        $this->assertEquals($isValid, $this->square->isValid());
    }

    public function isValidDataProvider(): array
    {
        return [
            [
                [ [1,2,3], [4,5,6], [7,8,9] ],
                true
            ],
            [
                [ [1,null,3], [4,5,null], [7,null,9] ],
                true,
            ],
            [
                [ [1,1,3], [4,5,6], [7,8,9] ],
                false
            ],
            [
                [ [1,2,3], [1,5,6], [7,8,9] ],
                false
            ],
        ];
    }

    /**
     * @param array $matrix
     *
     * @param bool $isCompleted
     * @dataProvider isCompletedDataProvider
     */
    public function testIsCompleted(array $matrix, bool $isCompleted): void
    {
        $this->initializeSquare($matrix);
        $this->assertEquals($isCompleted, $this->square->isCompleted());
    }

    public function isCompletedDataProvider(): array
    {
        return [
            [
                [ [1,2,3], [4,5,6], [7,8,9] ],
                true
            ],
            [
                [ [1,null,3], [4,5,null], [7,null,9] ],
                false,
            ],
            [
                [ [1,2,3], [4,5,6], [7,8,null] ],
                false
            ],
        ];
    }

    /**
     * @param array $data
     * @param int $digit
     * @param Position|null $position
     * @throws InvalidPosition
     *
     * @dataProvider findDigitDataProvider
     */
    public function testFindDigit(array $data, int $digit, ?Position $position): void
    {
        $this->initializeSquare($data);
        $this->assertEquals($position, $this->square->findDigit($digit));
    }

    /**
     * @return array|array[]
     * @throws InvalidPosition
     */
    public function findDigitDataProvider(): array
    {
        return [
            [
                [ [1,2,3], [4,5,6], [7,8,9] ],
                1,
                new Position(1,1)
            ],
            [
                [ [1,null,3], [4,5,6], [7,8,9] ],
                2,
                null
            ],
        ];
    }

    /**
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function testTips(): void
    {
        $this->initializeSquare([ [1,null,3], [4,5,6], [7,8,null] ]);

        $cellA = $this->square->cells()[1][2];
        $cellA->addTip(1);
        $cellA->addTip(2);

        $cellB = $this->square->cells()[3][3];
        $cellB->addTip(3);
        $cellB->addTip(4);

        $this->assertEquals(
            [ 1 => [ 1=>[], 2=>[1,2], 3=>[] ], 2 => [ 1=>[], 2=>[], 3=>[]], 3 => [ 1=>[], 2=>[], 3=>[3,4]]],
            $this->square->tips()
        );
    }

    /**
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function testClearTips(): void
    {
        $this->initializeSquare([ [1,null,3], [4,5,6], [7,8,null] ]);

        $cellA = $this->square->cells()[1][2];
        $cellA->addTip(1);
        $cellA->addTip(2);

        $cellB = $this->square->cells()[3][3];
        $cellB->addTip(3);
        $cellB->addTip(4);

        $this->square->clearTips();
        $this->assertEquals( [], $cellA->tips());
        $this->assertEquals( [], $cellB->tips());

    }

    private function initializeSquare(array $matrix): void
    {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($matrix[$i][$j] === null) {
                    $cell = $this->generateCell(new Nothing());
                } else {
                    $cell = $this->generateCell(new Fixed($matrix[$i][$j]));
                }
                $this->square->setCell($i+1, $j+1, $cell);
            }
        }
    }

    private function generateCell(CellContent $content): Cell
    {
        $cell = new Cell($content);
        $cell->setRow($this->row);
        $cell->setColumn($this->column);

        return $cell;
    }
}
