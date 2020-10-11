<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\Strategy\FillLastCellInBoard;
use App\Tests\Unit\Domain\Sudoku\Entity\BoardGeneratorMethods;
use PHPUnit\Framework\TestCase;

class FillLastCellInBoardTest extends TestCase
{
    use BoardGeneratorMethods;

    /**
     * @param int $row
     * @param int $column
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(int $row, int $column)
    {
        $strategy = new FillLastCellInBoard();
        $board = new Board();
        $data = $this->generateCompletedBoardExceptOneCell($row, $column);
        $board->load($data);
        $this->assertTrue($strategy->execute($board));
        $status = $board->status();
        $this->assertTrue($status->isValid());
        $this->assertTrue($status->isCompleted());
    }

    public function executeDataProvider(): array
    {
        return [
            [1,1],
            [3,3],
            [3,1],
            [3,9],
            [9,9],
        ];
    }

    /**
     * @param int[][] $emptyCells
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     *
     * @dataProvider  executeWhenMoreThanOneEmptyCellsDataProvider
     */
    public function testExecuteErrorWhenMoreThanOneEmptyCells(array $emptyCells)
    {
        $strategy = new FillLastCellInBoard();
        $board = new Board();
        $data = $this->generateCompletedBoardExceptSomeEmptyCells($emptyCells);
        $board->load($data);
        $this->assertFalse($strategy->execute($board));
        $status = $board->status();
        $this->assertTrue($status->isValid());
        $this->assertFalse($status->isCompleted());
    }

    public function executeWhenMoreThanOneEmptyCellsDataProvider(): array
    {
        return [
            [ [ [1,1], [1,2] ] ],
            [ [ [1,1], [9,9] ] ],
        ];
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testExecuteErrorWhenBoardIsAlreadyCompleted()
    {
        $strategy = new FillLastCellInBoard();
        $board = new Board();
        $data = $this->generateCompletedBoard();
        $board->load($data);
        $this->assertFalse($strategy->execute($board));
        $status = $board->status();
        $this->assertTrue($status->isValid());
        $this->assertTrue($status->isCompleted());
    }
}
