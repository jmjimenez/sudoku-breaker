<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Entity\Cell;
use App\Domain\Sudoku\Entity\Square;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\Strategy\Substrategy\FindCellsTiedByTipsInSquare;
use App\Domain\Sudoku\ValueObject\CellContent\Tied;
use App\Tests\Unit\Domain\Sudoku\Strategy\Strategy\DebugEvents;
use PHPUnit\Framework\TestCase;

class FindCellsTiedByTipsInSquareTest extends TestCase
{
    use DebugEvents;

    /**
     * @param Square $square
     * @param callable $doTests
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(Square $square, callable $doTests)
    {
        $strategy = new FindCellsTiedByTipsInSquare();
//        $this->debugEvents();
        $this->assertTrue($strategy->execute($square));
        $doTests($square);
    }

    /**
     * @return array|array[]
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     * @throws InvalidColumnValue
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    public function executeDataProvider(): array
    {

        return [
            $this->scenario01(),
        ];
    }

    /**
     * @return array
     * @throws CellDoesNotAllowTips
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    private function scenario01(): array
    {
        $square = $this->generateEmptySquare();

        $this->addTipsToCell($square->cells()[1][1], [1,2,3]);
        $this->addTipsToCell($square->cells()[1][2], [1,2,4]);

        $checkAction = function(Square $square): void {
            $this->assertCellIsTiedTo($square->cells()[1][1], [1,2]);
            $this->assertCellIsTiedTo($square->cells()[1][2], [1,2]);
        };

        return [ $square, $checkAction ];
    }

    /**
     * @return Square
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    private function generateEmptySquare(): Square
    {
        $board = new Board();
        $board->load([]);
        return $board->squares()[1];
    }

    /**
     * @param Cell $cell
     * @param int[]|array $tips
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     */
    private function addTipsToCell(Cell $cell, array $tips): void
    {
        foreach ($tips as $tip) {
            $cell->addTip($tip);
        }
    }

    private function assertCellIsTiedTo(Cell $cell, array $tips): void
    {
        $this->assertEquals(new Tied(...$tips), $cell->content());
    }
}
