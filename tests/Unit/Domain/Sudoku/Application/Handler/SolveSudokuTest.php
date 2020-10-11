<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Application\Handler;

use App\Domain\Sudoku\Application\SolveSudoku as SolveSudokuPayload;
use App\Domain\Sudoku\Application\Handler\Exception\NotPossibleToSolve;
use App\Domain\Sudoku\Application\Handler\Exception\NotValidSudoku;
use App\Domain\Sudoku\Application\Handler\Exception\ReachedInvalidStatus;
use App\Domain\Sudoku\Application\Handler\Exception\SudokuAlreadyCompleted;
use App\Domain\Sudoku\Application\Handler\SolveSudoku;
use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Infrastructure\Domain\Sudoku\Strategy\StrategiesLoaderHardcoded;
use App\Tests\Unit\Domain\Sudoku\Entity\BoardGeneratorMethods;
use App\Tests\Unit\Domain\Sudoku\Strategy\Strategy\DebugEvents;
use Exception;
use PHPUnit\Framework\TestCase;

class SolveSudokuTest extends TestCase
{
    use BoardGeneratorMethods;
    use DebugEvents;

    private SolveSudoku $game;

    protected function setUp(): void
    {
        $this->game = new SolveSudoku(
            new StrategiesLoaderHardcoded()
        );
        parent::setUp();
    }

    /**
     * @param array $problem
     * @param array $solution
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws NotPossibleToSolve
     * @throws NotValidSudoku
     * @throws ReachedInvalidStatus
     * @throws SudokuAlreadyCompleted
     *
     * @dataProvider handlerDataProvider
     */
    public function testHandler(array $problem, array $solution)
    {
        $includeSteps = false;
        $maxIterations = 30;

        $this->checkData($problem, $solution);
        $payload = new SolveSudokuPayload($problem, $maxIterations, $includeSteps);
//        $this->debugEvents();
        $this->assertEquals($solution, $this->game->execute($payload)->toArray());
    }

    public function handlerDataProvider()
    {
        return [
            $this->example01(),
        ];
    }

    private function example01(): array
    {
        $problem = [
             1 => [ 2 => 6, 3 => 2, 4 => 4, 6 => 9, 7 => 8, 8 => 1 ],
             2 => [ 6 => 3, 8 => 4 ],
             3 => [ 1 => 4, 4 => 2, 5 => 6, 8 => 5],
             4 => [ 1 => 8, 2 => 2, 4 => 7, 6 => 1 ],
             5 => [ 3 => 9, 4 => 6, 6 => 5, 7 => 7, 9 => 1 ],
             6 => [ 3 => 5 ],
             7 => [ 2 => 1, 3 => 3, 4 => 9, 7 => 2, 8 => 7, 9 => 8 ],
             8 => [ 2 => 9, 3 => 8, 4 => 1, 5 => 2, 6 => 6, 8 => 3 ],
             9 => [ 1 => 2, 2 => 5, 3 => 4, 5 => 3, 8 => 6 ],
        ];

        $solution = [
            1 => [1 => 5, 2 => 6, 3 => 2, 4 => 4, 5 => 7, 6 => 9, 7 => 8, 8 => 1, 9 => 3],
            2 => [1 => 9, 2 => 8, 3 => 7, 4 => 5, 5 => 1, 6 => 3, 7 => 6, 8 => 4, 9 => 2],
            3 => [1 => 4, 2 => 3, 3 => 1, 4 => 2, 5 => 6, 6 => 8, 7 => 9, 8 => 5, 9 => 7],
            4 => [1 => 8, 2 => 2, 3 => 6, 4 => 7, 5 => 4, 6 => 1, 7 => 3, 8 => 9, 9 => 5],
            5 => [1 => 3, 2 => 4, 3 => 9, 4 => 6, 5 => 8, 6 => 5, 7 => 7, 8 => 2, 9 => 1],
            6 => [1 => 1, 2 => 7, 3 => 5, 4 => 3, 5 => 9, 6 => 2, 7 => 4, 8 => 8, 9 => 6],
            7 => [1 => 6, 2 => 1, 3 => 3, 4 => 9, 5 => 5, 6 => 4, 7 => 2, 8 => 7, 9 => 8],
            8 => [1 => 7, 2 => 9, 3 => 8, 4 => 1, 5 => 2, 6 => 6, 7 => 5, 8 => 3, 9 => 4],
            9 => [1 => 2, 2 => 5, 3 => 4, 4 => 8, 5 => 3, 6 => 7, 7 => 1, 8 => 6, 9 => 9],
        ];

        return [ $problem, $solution ];
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws NotPossibleToSolve
     * @throws NotValidSudoku
     * @throws ReachedInvalidStatus
     * @throws SudokuAlreadyCompleted
     */
    public function testHandlerErrorWhenDataIsNotValid(): void
    {
        $includeSteps = false;
        $maxIterations = 5;

        $problem = [
            1 => [ 1 => 1, 3 => 1 ],
        ];

        $payload = new SolveSudokuPayload($problem, $maxIterations, $includeSteps);
        $this->expectException(NotValidSudoku::class);
        $this->game->execute($payload);
    }

    /**
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws NotPossibleToSolve
     * @throws NotValidSudoku
     * @throws ReachedInvalidStatus
     * @throws SudokuAlreadyCompleted
     */
    public function testHandlerErrorWhenSudokuIsAlreadyCompleted(): void
    {
        $problem = $this->generateCompletedBoard();
        $includeSteps = false;
        $maxIterations = 5;

        $payload = new SolveSudokuPayload($problem, $maxIterations, $includeSteps);
        $this->expectException(SudokuAlreadyCompleted::class);
        $this->game->execute($payload);
    }

    /**
     * @param array $problem
     * @param array $solution
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @throws Exception
     */
    private function checkData(array $problem, array $solution): void
    {
        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                if (isset($problem[$i][$j])) {
                    if ($problem[$i][$j] !== $solution[$i][$j]) {
                        throw new Exception('Solution does not fit with problem');
                    }
                }
            }
        }

        $board = new Board();
        $board->load($solution);
        $status = $board->status();
        if (!$status->isValid()) {
            throw new Exception('Solution is not valid');
        }
        if (!$status->isCompleted()) {
            throw new Exception('Solution is not completed');
        }
    }
}
