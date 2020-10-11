<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Entity;

use App\Domain\Sudoku\ValueObject\Position;
use App\Domain\Sudoku\Exception\InvalidPosition;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    /**
     * @param int $row
     * @param int $column
     * @throws InvalidPosition
     *
     * @dataProvider newErrorWhenPositionInvalidDataProvider
     */
    public function testNewErrorWhenPositionInvalid(int $row, int $column): void
    {
        $this->expectExceptionObject(new InvalidPosition($row, $column));
        new Position($row, $column);
    }

    public function newErrorWhenPositionInvalidDataProvider()
    {
        return [
            [ 1, 10 ],
            [ 0, 9 ],
        ];
    }

    /**
     * @throws InvalidPosition
     */
    public function testRow(): void
    {
        $row = 1;
        $column = 2;

        $position = new Position($row, $column);

        $this->assertEquals($row, $position->row());
    }

    /**
     * @throws InvalidPosition
     */
    public function testColumn(): void
    {
        $row = 1;
        $column = 2;

        $position = new Position($row, $column);

        $this->assertEquals($column, $position->column());
    }
}
