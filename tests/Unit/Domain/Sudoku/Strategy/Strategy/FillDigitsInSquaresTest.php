<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\Strategy\FillDigitsInSquares;
use App\Tests\Unit\Domain\Sudoku\Entity\BoardGeneratorMethods;
use PHPUnit\Framework\TestCase;

class FillDigitsInSquaresTest extends TestCase
{
    use BoardGeneratorMethods;
    use DebugEvents;

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws CellDoesNotAllowTips
     */
    public function testExecute()
    {
        $strategy = new FillDigitsInSquares();
        $board = $this->generateBoard();

        $steps = [
            function (Board $board) {
                return $board->cells()[1][2]->tips() == [2]
                    && $board->cells()[1][3]->tips() == [2]
                    && $board->cells()[2][2]->tips() == [2]
                    && $board->cells()[2][3]->tips() == [2]
                    && $board->cells()[3][2]->tips() == [2]
                    && $board->cells()[3][3]->tips() == [2];
            },
            function (Board $board) {
                return $board->cells()[1][2]->tips() == [2]
                    && $board->cells()[1][3]->tips() == [2]
                    && $board->cells()[2][1]->tips() == [3]
                    && $board->cells()[2][2]->tips() == [2,3]
                    && $board->cells()[2][3]->tips() == [2,3]
                    && $board->cells()[3][2]->tips() == [2]
                    && $board->cells()[3][3]->tips() == [2];
            },
            function (Board $board) {
                return $board->cells()[1][2]->tips() == [2,4]
                    && $board->cells()[1][3]->tips() == [2,4]
                    && $board->cells()[2][1]->tips() == [3,4]
                    && $board->cells()[2][2]->tips() == [2,3,4]
                    && $board->cells()[2][3]->tips() == [2,3,4]
                    && $board->cells()[3][1]->tips() == [4]
                    && $board->cells()[3][2]->tips() == [2,4]
                    && $board->cells()[3][3]->tips() == [2,4];
            },
        ];

//        $this->debugEvents();
        foreach ($steps as $step) {
            $strategy->execute($board);
            $status = $board->status();
            $this->assertTrue($status->isValid());
            $this->assertTrue($step($board));
        }
    }

    /**
     * @return Board
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     */
    private function generateBoard(): Board
    {
        $dataTest = [
            1 => [1 => 1, 5 => 3],
            2 => [4 => 1],
            3 => [7 => 1, 8 => 3],
            4 => [1 => 2, 2 => 1],
            5 => [7 => 5, 8 => 6, 9 => 7],
            6 => [7 => 8],
            7 => [3 => 1],
            8 => [6 => 1],
            9 => [9 => 1],
        ];

        $board = new Board();
        $board->load($dataTest);

        return $board;
    }

}
