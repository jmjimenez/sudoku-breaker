<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\Strategy\Substrategy\IsDigitValidInCell;
use PHPUnit\Framework\TestCase;

class IsDigitValidInCellTest extends TestCase
{
     private IsDigitValidInCell $isValidDigitInCell;

    protected function setUp(): void
    {
        $this->isValidDigitInCell = new IsDigitValidInCell();

        parent::setUp();
    }

    /**
     * @param array $data
     * @param array $tips
     * @param array $ties
     * @param int $digit
     * @param int $row
     * @param int $column
     * @param bool $isDigitValidInCell
     * @param string $message
     * @throws CellDoesNotAllowTips
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     * @dataProvider executeDataProvider
     */
    public function testExecute(
        array $data,
        array $tips,
        array $ties,
        int $digit,
        int $row,
        int $column,
        bool $isDigitValidInCell,
        string $message
    ): void {
        $board = new Board();
        $board->load($data);
        $cell = $board->cells()[$row][$column];

        foreach ($tips as $tip) {
            $board->cells()[$tip[0]][$tip[1]]->addTip($tip[2]);
        }

        foreach ($ties as $tie) {
            $board->cells()[$tie[0]][$tie[1]]->setTied(...$tie[2]);
        }

        $this->assertEquals($isDigitValidInCell, $this->isValidDigitInCell->execute($digit, $cell), $message);
    }

    public function executeDataProvider(): array
    {
        return [
            [ $this->data01(), $this->tips01(), $this->ties01(), 1, 1, 1, true, 'cell is empty without limitations' ],
            [ $this->data01(), $this->tips01(), $this->ties01(), 1, 2, 4, false, 'cell is not empty ' ],
            [ $this->data01(), $this->tips01(), $this->ties01(), 1, 6, 9, false, 'cell is tied' ],
            [ $this->data01(), $this->tips01(), $this->ties01(), 2, 7, 4, false, 'column already has the digit' ],
            [ $this->data01(), $this->tips01(), $this->ties01(), 1, 7, 4, false, 'row already has the digit' ],
            [ $this->data01(), $this->tips01(), $this->ties01(), 2, 5, 1, false, 'square already has the digit' ],
            [ $this->data01(), $this->tips01(), $this->ties01(), 1, 2, 5, false, 'row already has tip of digit' ],
            [ $this->data01(), $this->tips01(), $this->ties01(), 7, 7, 9, false, 'column already has tip of digit' ],
        ];
    }

    private function data01(): array
    {
        return [
            2 => [ 4 => 2 ],
            3 => [ 7 => 2 ],
            4 => [ 2 => 2, 7 => 2, 8 => 3, 9 => 4 ],
            5 => [ 7 => 5, 8 => 6 ],
            6 => [ 7 => 8 ],
            7 => [ 3 => 1 ],
            8 => [ 6 => 1 ],
            9 => [ 9 => 1 ],
        ];
    }

    public function tips01(): array
    {
        return [
            [ 2, 1, 1],
            [ 2, 2, 1],
            [ 2, 3, 1],
        ];
    }

    public function ties01(): array
    {
        return [
            [ 5, 9, [7,9]],
            [ 6, 9, [7,9]],
        ];
    }
}
