<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\Strategy\FillLastCellInRows;
use App\Tests\Unit\Domain\Sudoku\Entity\BoardGeneratorMethods;
use PHPUnit\Framework\TestCase;

class FillLastCellInRowsTest extends TestCase
{
    use BoardGeneratorMethods;

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testExecute()
    {
        $strategy = new FillLastCellInRows();
        $board = new Board();
        $data = $this->generateCompletedBoardExceptSomeEmptyCells([[1,1],[2,2]]);
        $board->load($data);
        $iterations = 0;
        while ($strategy->execute($board)) {
            $iterations++;
        }
        $this->assertEquals(2, $iterations);
        $status = $board->status();
        $this->assertTrue($status->isValid());
        $this->assertTrue($status->isCompleted());
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws InvalidPosition
     */
    public function testExecuteErrorWhenMoreThanOneEmptyCells()
    {
        $strategy = new FillLastCellInRows();
        $board = new Board();
        $data = $this->generateCompletedBoardExceptSomeEmptyCells([[1,1],[1,2]]);
        $board->load($data);
        $this->assertFalse($strategy->execute($board));
        $status = $board->status();
        $this->assertTrue($status->isValid());
        $this->assertFalse($status->isCompleted());
    }
}
