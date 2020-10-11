<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\CellContent\CellContent;
use App\Domain\Sudoku\ValueObject\CellContent\Fixed;
use App\Domain\Sudoku\ValueObject\CellContent\Found;
use App\Domain\Sudoku\ValueObject\CellContent\Nothing;
use App\Domain\Sudoku\ValueObject\CellContent\Tied;
use App\Domain\Sudoku\Entity\Cell;
use App\Domain\Sudoku\Entity\Column;
use App\Domain\Sudoku\ValueObject\Position;
use App\Domain\Sudoku\Entity\Row;
use App\Domain\Sudoku\Entity\Square;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidPosition;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    /**
     * @throws InvalidPosition
     */
    public function testPosition(): void
    {
        $rowNumber = 1;
        $columnNumber = 2;

        $cell = new Cell(new Nothing());
        $cell->setRow(new Row($rowNumber));
        $cell->setColumn(new Column($columnNumber));

        $this->assertEquals(new Position($rowNumber, $columnNumber), $cell->position());
    }

    public function testSetRow(): void
    {
        $rowNumber = 1;
        $cell = new Cell(new Nothing());
        $row = new Row($rowNumber);

        $cell->setRow($row);
        $this->assertEquals($row, $cell->row());
    }

    public function testSetColumn(): void
    {
        $columnNumber = 1;
        $cell = new Cell(new Nothing());
        $column = new Column($columnNumber);

        $cell->setColumn($column);
        $this->assertEquals($column, $cell->column());
    }

    public function testSetSquare(): void
    {
        $squareNumber = 1;
        $cell = new Cell(new Nothing());
        $square = new Square($squareNumber);

        $cell->setSquare($square);
        $this->assertEquals($square, $cell->square());
    }

    /**
     * @throws InvalidPosition
     */
    public function testSetContent(): void
    {
        $cell = $this->generateCell(new Nothing());
        $this->assertEquals(new Nothing(), $cell->content());

        $fixedContent = new Fixed(1);
        $cell->setContent($fixedContent);
        $this->assertEquals($fixedContent, $cell->content());
    }

    /**
     * @param CellContent $cellContent
     * @param int|null $value
     *
     * @dataProvider valueDataProvider
     */
    public function testValue(CellContent $cellContent, ?int $value): void
    {
        $cell = new Cell($cellContent);
        $this->assertEquals($value, $cell->value());
    }

    public function valueDataProvider(): array
    {
        return [
            [ new Nothing(), null ],
            [ new Found(9), 9 ],
            [ new Fixed(9), 9 ],
            [ new Tied(), null ],
        ];
    }

    /**
     * @param CellContent $cellContent
     * @param bool $isEmpty
     *
     * @dataProvider isEmptyDataProvider
     */
    public function testIsEmpy(CellContent $cellContent, bool $isEmpty): void
    {
        $cell = new Cell($cellContent);

        $this->assertEquals($isEmpty, $cell->isEmpty());
    }

    public function isEmptyDataProvider(): array
    {
        return [
            [ new Nothing(), true ],
            [ new Fixed(9), false ],
            [ new Found(9), false ],
            [ new Tied(), false ],
        ];
    }

    /**
     * @param CellContent $cellContent
     * @param int[] $ties
     *
     * @dataProvider tiesDataProvider
     */
    public function testTies(CellContent $cellContent, array $ties): void
    {
        $cell = new Cell($cellContent);
        $this->assertEquals($ties, $cell->ties());
    }

    public function tiesDataProvider(): array
    {
        return [
            [ new Nothing(), [] ],
            [ new Fixed(1), [] ],
            [ new Found(2), [] ],
            [ new Tied(1, 2), [1, 2] ],
        ];
    }

    /**
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    public function testAddTip(): void
    {
        $cell = $this->generateCell(new Nothing());

        $cell->addTip(2);
        $this->assertEquals([2], $cell->tips());
        $cell->addTip(1);
        $this->assertEquals([1,2], $cell->tips());
    }

    /**
     * @param CellContent $cellContent
     * @param bool $allowsTips
     *
     * @dataProvider addTipErrorWhenNotEmptyDataProvider
     * @throws InvalidPosition
     */
    public function testAddTipErrorWhenNotEmpty(CellContent $cellContent, bool $allowsTips): void
    {
        $cell = $this->generateCell($cellContent);
        $exception = false;

        try {
            $cell->addTip(1);
        } catch (CellDoesNotAllowTips $exception) {
            $exception = true;
        }

        $this->assertEquals($exception, !$allowsTips);
    }

    public function addTipErrorWhenNotEmptyDataProvider(): array
    {
        return [
            [ new Nothing(), true ],
            [ new Fixed(3), false ],
            [ new Found(3), false ],
            [ new Tied(), true ],
        ];
    }

    /**
     * @param int|null $digit
     * @param array $result
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     *
     * @dataProvider clearTipsDataProvider
     */
    public function testClearTips(?int $digit, array $result): void
    {
        $cell = $this->generateCell(new Nothing());

        $cell->addTip(3);
        $cell->addTip(1);
        $cell->addTip(2);
        $cell->clearTips($digit);

        $this->assertEquals($result, $cell->tips());
    }

    public function clearTipsDataProvider(): array
    {
        return [
            [ null, [] ],
            [ 1, [2, 3] ],
            [ 2, [1, 3] ],
            [ 4, [1, 2, 3] ],
        ];
    }

    private function generateCell(CellContent $content): Cell
    {
        $cell = new Cell($content);
        $row = new Row(1);
        $column = new Column(1);
        $cell->setRow($row);
        $cell->setColumn($column);

        return $cell;
    }
}
