<?php declare(strict_types=1);

namespace App\Tests\Unit\Domain\Sudoku\Strategy\Strategy\Substrategy;

use App\Domain\Sudoku\Entity\Board;
use App\Domain\Sudoku\Exception\CellDoesNotAllowTips;
use App\Domain\Sudoku\Exception\InvalidColumnValue;
use App\Domain\Sudoku\Exception\InvalidPosition;
use App\Domain\Sudoku\Exception\InvalidRowValue;
use App\Domain\Sudoku\Exception\InvalidValue;
use App\Domain\Sudoku\Strategy\Strategy\Substrategy\IsDigitTipInRow;
use PHPUnit\Framework\TestCase;

class IsDigitTipInRowTest extends TestCase
{
     private IsDigitTipInRow $isDigitTipInRow;

    protected function setUp(): void
    {
        $this->isDigitTipInRow = new IsDigitTipInRow();

        parent::setUp();
    }

    /**
     * @param array $data
     * @param array $tips
     * @param int $digit
     * @param bool $isDigitTipInRow
     * @param string $message
     * @throws CellDoesNotAllowTips
     * @throws InvalidColumnValue
     * @throws InvalidPosition
     * @throws InvalidRowValue
     * @throws InvalidValue
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute(array $data, array $tips, int $digit, bool $isDigitTipInRow, string $message): void
    {
        $board = new Board();
        $board->load($data);

        foreach ($tips as $tip) {
            $board->cells()[$tip[0]][$tip[1]]->addTip($tip[2]);
        }

        $this->assertEquals($isDigitTipInRow, $this->isDigitTipInRow->execute($digit, $board->rows()[1]), $message);
    }

    public function executeDataProvider(): array
    {
        return [
            [ $this->data01(), $this->tips01a(), 1, true, '3 tips in the same row' ],
            [ $this->data01(), $this->tips01b(), 1, true, '2 tips in the same row' ],
            [ $this->data01(), $this->tips01c(), 1, false, '2 tips in the same row but 1 tip in another row' ],
            [ $this->data01(), $this->tips01d(), 1, false, '2 tips in the same row but different squares' ],
        ];
    }

    private function data01(): array
    {
        return [
            2 => [ 4 => 2 ],
            3 => [ 7 => 2 ],
            4 => [ 2 => 1, 7 => 2, 8 => 3, 9 => 4 ],
            5 => [ 7 => 5, 8 => 6, 9 => 7 ],
            6 => [ 7 => 8 ],
            7 => [ 3 => 1 ],
            8 => [ 6 => 1 ],
            9 => [ 9 => 1 ],
        ];
    }

    public function tips01a(): array
    {
        return [
            [ 1, 1, 1],
            [ 1, 2, 1],
            [ 1, 3, 1],
        ];
    }

    public function tips01b(): array
    {
        return [
            [ 1, 1, 1],
            [ 1, 3, 1],
        ];
    }

    public function tips01c(): array
    {
        return [
            [ 1, 1, 1],
            [ 2, 1, 1],
            [ 1, 3, 1],
        ];
    }

    public function tips01d(): array
    {
        return [
            [ 1, 1, 1],
            [ 1, 4, 1],
        ];
    }
}
