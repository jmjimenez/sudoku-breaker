<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\Strategy\Substrategy\FillDigitInSquare;
use App\Tests\Unit\Domain\Sudoku\Entity\BoardGeneratorMethods;
use App\Tests\Unit\Domain\Sudoku\Strategy\Strategy\DebugEvents;
use PHPUnit\Framework\TestCase;

class FillDigitInSquareTest extends TestCase
{
    use BoardGeneratorMethods;
    use DebugEvents;

    /**
     * @param Board $board
     * @param int $digit
     * @param int $square
     * @param callable $checkAction
     * @throws CellDoesNotAllowTips
     * @throws InvalidPosition
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(Board $board, int $digit, int $square, callable $checkAction)
    {
        $strategy = new FillDigitInSquare();
//        $this->debugEvents();
        $this->assertTrue($strategy->execute($digit, $board->squares()[$square]));
        $status = $board->status();
        $this->assertTrue($status->isValid());
        $this->assertTrue($checkAction($board));
    }

    /**
     * @return array|array[]
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws CellDoesNotAllowTips
     */
    public function executeDataProvider(): array
    {
        $dataTest1 = [
            1 => [ 1 => 1 ],
            2 => [ 4 => 1 ],
            3 => [ 7 => 1 ],
            4 => [ 1 => 2, 2 => 1 ],
            5 => [ 7 => 5, 8 => 6, 9 => 7 ],
            6 => [ 7 => 8 ],
            7 => [ 3 => 1 ],
            8 => [ 6 => 1 ],
            9 => [ 9 => 1 ],
        ];

        $board1 = new Board();
        $board1->load($dataTest1);

        $board2 = new Board();
        $board2->load($dataTest1);
        $board2->setFound(6, 8, 1);

        $board3 = new Board();
        $board3->load($dataTest1);
        $board3->setFound(6, 8, 1);
        $board3->setFound(5, 5, 1);

        $board4 = new Board();
        $board4->load($dataTest1);
        $board4->setFound(6, 8, 1);
        $board4->setFound(5, 5, 1);
        $board4->cells()[1][9]->addTip(3);
        $board4->cells()[2][9]->addTip(3);
        $board4->cells()[8][8]->addTip(3);
        $board4->cells()[9][8]->addTip(3);

        return [
            [
                $board1,
                1,
                6,
                function(Board $board) { return $board->row(6)->findDigit(1) === 8; }
            ],
            [
                $board2,
                1,
                5,
                function(Board $board) { return $board->cells()[5][5]->value() == 1; }
            ],
            [
                $board3,
                2,
                6,
                function(Board $board) { return $board->cells()[6][9]->value() == 2; }
            ],
            [
                $board4,
                3,
                6,
                function(Board $board) { return $board->cells()[4][7]->value() == 3; }
            ],
        ];
    }
}
